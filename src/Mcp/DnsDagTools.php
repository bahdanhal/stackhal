<?php

declare(strict_types=1);

namespace App\Mcp;

use App\DnsDagTracer\Application\DnsDagTracerService;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;

final readonly class DnsDagTools
{
    public function __construct(private DnsDagTracerService $dnsDagService)
    {
    }

    #[McpTool(
        name: 'trace_dns_delegation',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Trace hierarchical DNS delegation chain (Root -> TLD -> Authoritative NS -> Global Resolvers) with DNSSEC trust chain validation and propagation consistency diagnostics.'
    )]
    public function traceDnsDelegation(
        #[Schema(description: 'The target domain name (e.g. "stackhal.com") to trace hierarchical DNS delegation for.')]
        string $domain,
        // phpcs:ignore Generic.Files.LineLength
        #[Schema(description: 'Optional DNS record type: "A" (default), "AAAA", "CNAME", "TXT", "MX", "NS", "SOA", "CAA", "DS", "DNSKEY".')]
        ?string $query_type = 'A',
    ): string {
        try {
            $result = $this->dnsDagService->trace(
                domain: $domain,
                queryType: $query_type,
            );

            return $this->json([
                'status' => 'completed',
                'result' => $result->toArray(),
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function json(array $data): string
    {
        return json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }
}
