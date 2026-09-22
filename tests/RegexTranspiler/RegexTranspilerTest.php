<?php

declare(strict_types=1);

namespace App\Tests\RegexTranspiler;

use App\RegexTranspiler\Application\RegexTranspilerService;
use App\RegexTranspiler\Domain\Model\RegexEngine;
use PHPUnit\Framework\TestCase;

final class RegexTranspilerTest extends TestCase
{
    private RegexTranspilerService $service;

    protected function setUp(): void
    {
        $this->service = new RegexTranspilerService();
    }

    public function testMalformedPatternIsNotReportedAsCompatible(): void
    {
        $result = $this->service->transpile('(abc', RegexEngine::Pcre, RegexEngine::JavaScript);

        self::assertFalse($result->isCompatible);
        self::assertSame('ERR_INVALID_PATTERN', $result->diagnostics[0]->code);
    }

    public function testLossyAtomicAndPossessiveRewritesAreRejected(): void
    {
        $atomic = $this->service->transpile('(?>a|ab)c', RegexEngine::Pcre, RegexEngine::GoRe2);
        $possessive = $this->service->transpile('a++a', RegexEngine::Pcre, RegexEngine::GoRe2);

        self::assertFalse($atomic->isCompatible);
        self::assertSame('(?>a|ab)c', $atomic->transpiledPattern);
        self::assertFalse($possessive->isCompatible);
        self::assertSame('a++a', $possessive->transpiledPattern);
    }

    public function testPcreInlineModifierIsNotClaimedCompatibleWithJavaScript(): void
    {
        $result = $this->service->transpile('(?i)^hello$', RegexEngine::Pcre, RegexEngine::JavaScript);

        self::assertFalse($result->isCompatible);
        self::assertContains(
            'ERR_INLINE_MODIFIERS_REQUIRE_EXTERNAL_FLAGS',
            array_map(static fn ($diagnostic): string => $diagnostic->code, $result->diagnostics)
        );
    }

    public function testTranspileNamedGroupFromPcreToGoRe2(): void
    {
        $pattern = '(?<uuid>[a-f0-9-]{36})';
        $result = $this->service->transpile($pattern, RegexEngine::Pcre, RegexEngine::GoRe2);

        self::assertTrue($result->isCompatible);
        self::assertSame('(?P<uuid>[a-f0-9-]{36})', $result->transpiledPattern);
        self::assertTrue($result->targetEngine->isLinearTime());

        $warningCodes = array_map(static fn ($d) => $d->code, $result->diagnostics);
        self::assertContains('WARN_NAMED_GROUP_SYNTAX_TRANSPILLED', $warningCodes);
    }

    public function testRejectsLookaroundForGoRe2(): void
    {
        $pattern = 'foo(?=bar)';
        $result = $this->service->transpile($pattern, RegexEngine::Pcre, RegexEngine::GoRe2);

        self::assertFalse($result->isCompatible);
        $codes = array_map(static fn ($d) => $d->code, $result->diagnostics);
        self::assertContains('ERR_UNSUPPORTED_LOOKAROUND', $codes);
    }

    public function testRejectsAtomicGroupAndPossessiveQuantifierWithoutSafeEquivalent(): void
    {
        $pattern = '(?>[a-z]++)';
        $result = $this->service->transpile($pattern, RegexEngine::Pcre, RegexEngine::GoRe2);

        self::assertFalse($result->isCompatible);
        self::assertSame('(?>[a-z]++)', $result->transpiledPattern);

        $codes = array_map(static fn ($d) => $d->code, $result->diagnostics);
        self::assertContains('ERR_ATOMIC_GROUP_NOT_EQUIVALENT', $codes);
        self::assertContains('ERR_POSSESSIVE_QUANTIFIER_NOT_EQUIVALENT', $codes);
    }

    public function testTranspilesNamedGroupFromGoRe2ToJavaScript(): void
    {
        $pattern = '(?P<year>\\d{4})-(?P<month>\\d{2})';
        $result = $this->service->transpile($pattern, RegexEngine::GoRe2, RegexEngine::JavaScript);

        self::assertTrue($result->isCompatible);
        self::assertSame('(?<year>\\d{4})-(?<month>\\d{2})', $result->transpiledPattern);

        $codes = array_map(static fn ($d) => $d->code, $result->diagnostics);
        self::assertContains('WARN_NAMED_GROUP_SYNTAX_TRANSPILLED', $codes);
    }

    public function testRejectsBackreferenceForRust(): void
    {
        $pattern = '([a-z])\\1';
        $result = $this->service->transpile($pattern, RegexEngine::Pcre, RegexEngine::Rust);

        self::assertFalse($result->isCompatible);
        $codes = array_map(static fn ($d) => $d->code, $result->diagnostics);
        self::assertContains('ERR_UNSUPPORTED_BACKREFERENCE', $codes);
    }

    public function testComputesFullCompatibilityMatrixAcrossAll5Engines(): void
    {
        $pattern = '(?<user>[a-z]+)(?=test)';
        $result = $this->service->transpile($pattern, RegexEngine::Pcre, RegexEngine::GoRe2);

        self::assertCount(5, $result->compatibilityMatrix);

        /** @var array<string, \App\RegexTranspiler\Domain\Model\EngineCompatibility> $matrixByEngine */
        $matrixByEngine = [];
        foreach ($result->compatibilityMatrix as $item) {
            $matrixByEngine[$item->engine->value] = $item;
        }

        self::assertArrayHasKey('pcre', $matrixByEngine);
        self::assertArrayHasKey('javascript', $matrixByEngine);
        self::assertArrayHasKey('python', $matrixByEngine);
        self::assertArrayHasKey('go_re2', $matrixByEngine);
        self::assertArrayHasKey('rust', $matrixByEngine);

        self::assertTrue($matrixByEngine['pcre']->isCompatible);
        self::assertTrue($matrixByEngine['javascript']->isCompatible);
        self::assertTrue($matrixByEngine['python']->isCompatible);
        self::assertFalse($matrixByEngine['go_re2']->isCompatible);
        self::assertFalse($matrixByEngine['rust']->isCompatible);
    }

    public function testLoadsPresetsAndMetadataFromSpec(): void
    {
        $presets = $this->service->getPresets();
        self::assertNotEmpty($presets);
        self::assertCount(4, $presets);

        $metadata = $this->service->getEngineMetadata();
        self::assertArrayHasKey('go_re2', $metadata);
        self::assertArrayHasKey('pcre', $metadata);
        self::assertArrayHasKey('javascript', $metadata);
        self::assertArrayHasKey('python', $metadata);
        self::assertArrayHasKey('rust', $metadata);
    }
}
