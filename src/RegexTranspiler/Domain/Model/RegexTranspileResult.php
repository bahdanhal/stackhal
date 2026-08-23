<?php

declare(strict_types=1);

namespace App\RegexTranspiler\Domain\Model;

final readonly class RegexTranspileResult
{
    /**
     * @param list<TranspileDiagnostic> $diagnostics
     * @param list<EngineCompatibility> $compatibilityMatrix
     */
    public function __construct(
        public RegexEngine $sourceEngine,
        public RegexEngine $targetEngine,
        public string $sourcePattern,
        public string $transpiledPattern,
        public bool $isCompatible,
        public array $diagnostics = [],
        public array $compatibilityMatrix = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'source_engine' => $this->sourceEngine->value,
            'target_engine' => $this->targetEngine->value,
            'source_pattern' => $this->sourcePattern,
            'transpiled_pattern' => $this->transpiledPattern,
            'is_compatible' => $this->isCompatible,
            'is_target_linear_time' => $this->targetEngine->isLinearTime(),
            'diagnostics' => array_map(static fn (TranspileDiagnostic $d) => $d->toArray(), $this->diagnostics),
            'errors' => array_values(array_filter(
                array_map(static fn (TranspileDiagnostic $d) => $d->toArray(), $this->diagnostics),
                static fn (array $d) => $d['severity'] === TranspileDiagnostic::SEVERITY_ERROR
            )),
            'warnings' => array_values(array_filter(
                array_map(static fn (TranspileDiagnostic $d) => $d->toArray(), $this->diagnostics),
                static fn (array $d) => $d['severity'] === TranspileDiagnostic::SEVERITY_WARNING
            )),
            'compatibility_matrix' => array_map(
                static fn (EngineCompatibility $c) => $c->toArray(),
                $this->compatibilityMatrix
            ),
        ];
    }
}
