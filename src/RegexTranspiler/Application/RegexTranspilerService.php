<?php

declare(strict_types=1);

namespace App\RegexTranspiler\Application;

use App\RegexTranspiler\Domain\Engine\RegexTranspilerEngine;
use App\RegexTranspiler\Domain\Model\RegexEngine;
use App\RegexTranspiler\Domain\Model\RegexTranspileResult;

final readonly class RegexTranspilerService
{
    private RegexTranspilerEngine $engine;
    /** @var array<string, mixed> */
    private array $spec;

    public function __construct(?RegexTranspilerEngine $engine = null)
    {
        $this->engine = $engine ?? new RegexTranspilerEngine();
        $this->spec = $this->loadSpec();
    }

    public function transpile(
        string $pattern,
        RegexEngine|string|null $sourceEngine = null,
        RegexEngine|string|null $targetEngine = null,
    ): RegexTranspileResult {
        $source = $this->resolveEngine($sourceEngine, RegexEngine::Pcre);
        $target = $this->resolveEngine($targetEngine, RegexEngine::GoRe2);

        return $this->engine->transpile($pattern, $source, $target);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getPresets(): array
    {
        /** @var list<array<string, mixed>> $presets */
        $presets = $this->spec['presets'] ?? [];

        return $presets;
    }

    /**
     * @return array<string, mixed>
     */
    public function getEngineMetadata(): array
    {
        /** @var array<string, mixed> $metadata */
        $metadata = $this->spec['engine_metadata'] ?? [];

        return $metadata;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDiagnosticCodes(): array
    {
        /** @var array<string, mixed> $diagnostics */
        $diagnostics = $this->spec['diagnostic_codes'] ?? [];

        return $diagnostics;
    }

    private function resolveEngine(RegexEngine|string|null $engine, RegexEngine $default): RegexEngine
    {
        if ($engine instanceof RegexEngine) {
            return $engine;
        }

        if (is_string($engine) && trim($engine) !== '') {
            try {
                return RegexEngine::fromString($engine);
            } catch (\InvalidArgumentException) {
                return $default;
            }
        }

        return $default;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadSpec(): array
    {
        $specPath = dirname(__DIR__, 3) . '/specs/regex-transpiler.spec.json';
        if (file_exists($specPath)) {
            $content = (string) file_get_contents($specPath);
            /** @var array<string, mixed> $parsed */
            $parsed = json_decode($content, true, flags: JSON_THROW_ON_ERROR);

            return $parsed;
        }

        return [];
    }
}
