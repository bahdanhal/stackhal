<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Application;

use App\DnsDagTracer\Domain\Engine\DnsDagEngine;
use App\DnsDagTracer\Domain\Model\DnsDagResult;
use App\DnsDagTracer\Domain\Model\QueryType;

final readonly class DnsDagTracerService
{
    private DnsDagEngine $engine;

    public function __construct(?DnsDagEngine $engine = null)
    {
        $this->engine = $engine ?? new DnsDagEngine();
    }

    public function trace(string $domain, ?string $queryType = null): DnsDagResult
    {
        $type = QueryType::fromString($queryType);

        return $this->engine->trace($domain, $type);
    }

    /**
     * @return list<array{id: string, domain: string, query_type: string, description: string}>
     */
    public function getPresets(): array
    {
        return [
            [
                'id' => 'cloudflare_dnssec_clean',
                'domain' => 'stackhal.com',
                'query_type' => 'A',
                'description' => 'Healthy zone hosted on Cloudflare with valid DNSSEC DS record and fast TTL.',
            ],
            [
                'id' => 'stale_propagation_migration',
                'domain' => 'migration-example.org',
                'query_type' => 'A',
                'description' => 'Domain mid-migration where global public resolvers have divergent answers due to 86,400s TTL.',
            ],
            [
                'id' => 'broken_dnssec_ds',
                'domain' => 'dnssec-failed.org',
                'query_type' => 'TXT',
                // phpcs:ignore Generic.Files.LineLength
                'description' => 'Zone with invalid DS record triggering SERVFAIL on validating resolvers (1.1.1.1, 8.8.8.8, 9.9.9.9).',
            ],
        ];
    }
}
