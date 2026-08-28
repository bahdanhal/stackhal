<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Domain\Model;

final readonly class DnsDagResult
{
    /**
     * @param list<DnsLayer> $layers
     * @param list<DnsDiagnostic> $diagnostics
     */
    public function __construct(
        public string $status,
        public DnssecStatus $dnssecStatus,
        public int $layerCount,
        public bool $hasDivergence,
        public array $layers,
        public array $diagnostics,
        public string $domain = '',
        public QueryType $queryType = QueryType::A,
        public bool $isSimulation = false,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getErrorCodes(): array
    {
        $codes = [];
        foreach ($this->diagnostics as $diagnostic) {
            if ($diagnostic->severity === 'error') {
                $codes[] = $diagnostic->code;
            }
        }

        return $codes;
    }

    /**
     * @return list<string>
     */
    public function getWarningCodes(): array
    {
        $codes = [];
        foreach ($this->diagnostics as $diagnostic) {
            if ($diagnostic->severity === 'warning') {
                $codes[] = $diagnostic->code;
            }
        }

        return $codes;
    }

    /**
     * @return list<string>
     */
    public function getInfoCodes(): array
    {
        $codes = [];
        foreach ($this->diagnostics as $diagnostic) {
            if ($diagnostic->severity === 'info') {
                $codes[] = $diagnostic->code;
            }
        }

        return $codes;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'dnssec_status' => $this->dnssecStatus->value,
            'layer_count' => $this->layerCount,
            'has_divergence' => $this->hasDivergence,
            'domain' => $this->domain,
            'query_type' => $this->queryType->value,
            'is_simulation' => $this->isSimulation,
            'layers' => array_map(static fn (DnsLayer $l) => $l->toArray(), $this->layers),
            'diagnostics' => array_map(static fn (DnsDiagnostic $d) => $d->toArray(), $this->diagnostics),
            'error_codes' => $this->getErrorCodes(),
            'warning_codes' => $this->getWarningCodes(),
        ];
    }
}
