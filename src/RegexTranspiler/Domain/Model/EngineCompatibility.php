<?php

declare(strict_types=1);

namespace App\RegexTranspiler\Domain\Model;

final readonly class EngineCompatibility
{
    /**
     * @param list<TranspileDiagnostic> $diagnostics
     */
    public function __construct(
        public RegexEngine $engine,
        public bool $isCompatible,
        public bool $isLinearTime,
        public string $transpiledPattern,
        public array $diagnostics = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'engine' => $this->engine->value,
            'engine_name' => $this->engine->getDisplayName(),
            'ecosystem' => $this->engine->getEcosystem(),
            'is_compatible' => $this->isCompatible,
            'is_linear_time' => $this->isLinearTime,
            'transpiled_pattern' => $this->transpiledPattern,
            'diagnostics' => array_map(static fn (TranspileDiagnostic $d) => $d->toArray(), $this->diagnostics),
            'errors' => array_values(array_filter(
                array_map(static fn (TranspileDiagnostic $d) => $d->toArray(), $this->diagnostics),
                static fn (array $d) => $d['severity'] === TranspileDiagnostic::SEVERITY_ERROR
            )),
            'warnings' => array_values(array_filter(
                array_map(static fn (TranspileDiagnostic $d) => $d->toArray(), $this->diagnostics),
                static fn (array $d) => $d['severity'] === TranspileDiagnostic::SEVERITY_WARNING
            )),
        ];
    }
}
