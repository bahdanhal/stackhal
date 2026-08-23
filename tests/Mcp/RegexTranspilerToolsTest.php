<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\Mcp\RegexTranspilerTools;
use App\RegexTranspiler\Application\RegexTranspilerService;
use PHPUnit\Framework\TestCase;

final class RegexTranspilerToolsTest extends TestCase
{
    private RegexTranspilerTools $tools;

    protected function setUp(): void
    {
        $this->tools = new RegexTranspilerTools(new RegexTranspilerService());
    }

    public function testTranspileRegexEngineMcpToolReturnsValidJson(): void
    {
        $pattern = '(?<id>[0-9]+)';
        $raw = $this->tools->transpileRegexEngine($pattern, 'pcre', 'go_re2');

        self::assertJson($raw);
        $data = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('completed', $data['status']);
        self::assertArrayHasKey('result', $data);
        self::assertSame('(?P<id>[0-9]+)', $data['result']['transpiled_pattern']);
        self::assertTrue($data['result']['is_compatible']);
        self::assertTrue($data['result']['is_target_linear_time']);
        self::assertCount(5, $data['result']['compatibility_matrix']);
    }

    public function testTranspileRegexEngineWithLookaroundReturnsDiagnostics(): void
    {
        $pattern = '(?=admin)secret';
        $raw = $this->tools->transpileRegexEngine($pattern, 'pcre', 'go_re2');

        self::assertJson($raw);
        $data = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('completed', $data['status']);
        self::assertFalse($data['result']['is_compatible']);
        self::assertNotEmpty($data['result']['errors']);
        self::assertSame('ERR_UNSUPPORTED_LOOKAROUND', $data['result']['errors'][0]['code']);
    }
}
