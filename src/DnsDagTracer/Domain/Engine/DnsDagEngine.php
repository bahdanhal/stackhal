<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Domain\Engine;

use App\DnsDagTracer\Domain\Model\DnsDagResult;
use App\DnsDagTracer\Domain\Model\DnsDiagnostic;
use App\DnsDagTracer\Domain\Model\DnsLayer;
use App\DnsDagTracer\Domain\Model\DnssecStatus;
use App\DnsDagTracer\Domain\Model\DnsServerNode;
use App\DnsDagTracer\Domain\Model\QueryType;

final class DnsDagEngine
{
    private const array DIAGNOSTIC_DEFINITIONS = [
        'ERR_LAME_DELEGATION' => [
            'severity' => 'error',
            'title' => 'Lame Delegation Detected',
            'description' => 'The authoritative nameserver designated by the parent zone is refusing queries or is not authoritative for the domain.',
        ],
        'ERR_DNSSEC_BOGUS' => [
            'severity' => 'error',
            'title' => 'Broken DNSSEC Chain of Trust',
            'description' => 'The Delegation Signer (DS) record in the parent zone does not match any DNSKEY in the child zone, causing validation failure.',
        ],
        'ERR_MISSING_GLUE_RECORD' => [
            'severity' => 'error',
            'title' => 'Missing In-Bailiwick Glue Record',
            'description' => 'Nameserver hostname is a subdomain of the target domain but lacks mandatory A/AAAA glue records in the parent TLD zone.',
        ],
        'ERR_NXDOMAIN' => [
            'severity' => 'error',
            'title' => 'Domain Not Found (NXDOMAIN)',
            'description' => 'Parent or authoritative servers report that the queried domain name does not exist.',
        ],
        'WARN_SOA_SERIAL_DIVERGENCE' => [
            'severity' => 'warning',
            'title' => 'SOA Serial Divergence',
            'description' => 'Multiple authoritative nameservers return differing SOA serial numbers, indicating replication delay across nameservers.',
        ],
        'WARN_HIGH_TTL_MIGRATION_RISK' => [
            'severity' => 'warning',
            'title' => 'High TTL Risk During Migration',
            'description' => 'Resource record TTL exceeds 14,400 seconds (4 hours), which may delay global propagation during nameserver migrations.',
        ],
        'INFO_DNSSEC_SECURE' => [
            'severity' => 'info',
            'title' => 'DNSSEC Authenticated & Secure',
            'description' => 'Full cryptographic chain of trust validated from Root (.) through TLD to Child Zone RRSIG records.',
        ],
        'ERR_LIVE_TRACE_UNAVAILABLE' => [
            'severity' => 'error',
            'title' => 'Live DNS Trace Unavailable',
            'description' => 'This public tool currently provides demonstration traces only. No live DNS data was returned for this domain.',
        ],
    ];

    public function trace(string $domain, QueryType $queryType = QueryType::A): DnsDagResult
    {
        $domain = strtolower(trim($domain, " .\t\n\r\0\x0B"));
        if ($domain === '') {
            return $this->createErrorResult($domain, $queryType, 'ERR_NXDOMAIN');
        }

        // Handle known test vectors and special mocked scenarios
        if ($domain === 'dnssec-failed.org') {
            return $this->buildBogusDnssecResult($domain, $queryType);
        }

        if ($domain === 'divergent-answers.test' || $domain === 'migration-example.org') {
            return $this->buildDivergentResult($domain, $queryType);
        }

        if ($domain === 'nxdomain-example.invalid') {
            return $this->createErrorResult($domain, $queryType, 'ERR_NXDOMAIN');
        }

        if ($domain === 'lame-delegation.test') {
            return $this->createErrorResult($domain, $queryType, 'ERR_LAME_DELEGATION');
        }

        if ($domain === 'stackhal.com') {
            return $this->buildAuthoritativeTrace($domain, $queryType);
        }

        return $this->createUnavailableResult($domain, $queryType);
    }

    private function buildAuthoritativeTrace(string $domain, QueryType $queryType): DnsDagResult
    {
        $layers = $this->buildStandardHierarchy($domain, $queryType, [
            'answers' => ['104.21.48.1', '172.67.182.2'],
            'ttl' => 300,
            'dnssec_secure' => true,
            'soa_serial' => 2026082401,
        ]);

        /** @var list<DnsDiagnostic> $diagnostics */
        $diagnostics = [
            $this->createDiagnostic('INFO_DNSSEC_SECURE'),
        ];

        return new DnsDagResult(
            status: 'healthy',
            dnssecStatus: DnssecStatus::SECURE,
            layerCount: count($layers),
            hasDivergence: false,
            layers: $layers,
            diagnostics: $diagnostics,
            domain: $domain,
            queryType: $queryType,
            isSimulation: true,
        );
    }

    private function buildBogusDnssecResult(string $domain, QueryType $queryType): DnsDagResult
    {
        $layers = $this->buildStandardHierarchy($domain, $queryType, [
            'answers' => [],
            'ttl' => 300,
            'dnssec_secure' => false,
            'rcode' => 'SERVFAIL',
            'soa_serial' => 2026082401,
        ]);

        /** @var list<DnsDiagnostic> $diagnostics */
        $diagnostics = [
            $this->createDiagnostic('ERR_DNSSEC_BOGUS'),
        ];

        return new DnsDagResult(
            status: 'error',
            dnssecStatus: DnssecStatus::BOGUS,
            layerCount: count($layers),
            hasDivergence: false,
            layers: $layers,
            diagnostics: $diagnostics,
            domain: $domain,
            queryType: $queryType,
            isSimulation: true,
        );
    }

    private function buildDivergentResult(string $domain, QueryType $queryType): DnsDagResult
    {
        $layers = [
            new DnsLayer(
                layer: 0,
                name: 'root',
                label: 'Root Zone (.)',
                servers: '13 IANA Root Clusters (a.root-servers.net - m.root-servers.net)',
                nodes: [
                    new DnsServerNode('a.root-servers.net', '198.41.0.4', ['ns1.iana.org'], 86400, 'NOERROR', 2026082400, 8.2),
                    new DnsServerNode('k.root-servers.net', '193.0.14.129', ['ns2.iana.org'], 86400, 'NOERROR', 2026082400, 11.5),
                ]
            ),
            new DnsLayer(
                layer: 1,
                name: 'tld',
                label: 'Top-Level Domain (TLD)',
                servers: 'Authoritative Registry Nameservers (e.g. gTLD / ccTLD)',
                nodes: [
                    new DnsServerNode('a0.org.afilias-nst.info', '199.19.56.1', ['ns1.old-host.com', 'ns2.new-host.com'], 86400, 'NOERROR', 2026082400, 14.1),
                ]
            ),
            new DnsLayer(
                layer: 2,
                name: 'authoritative',
                label: 'Authoritative Nameservers',
                servers: 'Zone Host Nameservers (e.g. Cloudflare, Route53, NS1)',
                nodes: [
                    new DnsServerNode('ns1.old-host.com', '203.0.113.10', ['198.51.100.1'], 86400, 'NOERROR', 2026082001, 22.4),
                    new DnsServerNode('ns2.new-host.com', '203.0.113.20', ['198.51.100.2'], 300, 'NOERROR', 2026082401, 18.7),
                ]
            ),
            new DnsLayer(
                layer: 3,
                name: 'edge_resolvers',
                label: 'Global Edge Anycast Resolvers',
                servers: 'Public Recursive Resolvers (Cloudflare 1.1.1.1, Google 8.8.8.8, Quad9 9.9.9.9, OpenDNS 208.67.222.222)',
                nodes: [
                    new DnsServerNode('Cloudflare (1.1.1.1)', '1.1.1.1', ['198.51.100.2'], 300, 'NOERROR', null, 7.1),
                    new DnsServerNode('Google (8.8.8.8)', '8.8.8.8', ['198.51.100.1'], 43200, 'NOERROR', null, 12.3),
                    new DnsServerNode('Quad9 (9.9.9.9)', '9.9.9.9', ['198.51.100.1'], 38000, 'NOERROR', null, 14.8),
                    new DnsServerNode('OpenDNS (208.67.222.222)', '208.67.222.222', ['198.51.100.2'], 300, 'NOERROR', null, 16.2),
                ]
            ),
        ];

        /** @var list<DnsDiagnostic> $diagnostics */
        $diagnostics = [
            $this->createDiagnostic('WARN_HIGH_TTL_MIGRATION_RISK'),
            $this->createDiagnostic('WARN_SOA_SERIAL_DIVERGENCE'),
        ];

        return new DnsDagResult(
            status: 'warning',
            dnssecStatus: DnssecStatus::UNSIGNED,
            layerCount: count($layers),
            hasDivergence: true,
            layers: $layers,
            diagnostics: $diagnostics,
            domain: $domain,
            queryType: $queryType,
            isSimulation: true,
        );
    }

    private function createUnavailableResult(string $domain, QueryType $queryType): DnsDagResult
    {
        return new DnsDagResult(
            status: 'error',
            dnssecStatus: DnssecStatus::INDETERMINATE,
            layerCount: 0,
            hasDivergence: false,
            layers: [],
            diagnostics: [$this->createDiagnostic('ERR_LIVE_TRACE_UNAVAILABLE')],
            domain: $domain,
            queryType: $queryType,
        );
    }

    private function createErrorResult(string $domain, QueryType $queryType, string $errorCode): DnsDagResult
    {
        $layers = $this->buildStandardHierarchy($domain, $queryType, [
            'answers' => [],
            'ttl' => 0,
            'dnssec_secure' => false,
            'rcode' => 'NXDOMAIN',
            'soa_serial' => null,
        ]);

        return new DnsDagResult(
            status: 'error',
            dnssecStatus: DnssecStatus::INDETERMINATE,
            layerCount: count($layers),
            hasDivergence: false,
            layers: $layers,
            diagnostics: [$this->createDiagnostic($errorCode)],
            domain: $domain,
            queryType: $queryType,
            isSimulation: true,
        );
    }

    /**
     * @param array{answers: list<string>, ttl: int, dnssec_secure: bool, rcode?: string, soa_serial?: int|null} $config
     * @return list<DnsLayer>
     */
    private function buildStandardHierarchy(string $domain, QueryType $queryType, array $config): array
    {
        $rcode = $config['rcode'] ?? 'NOERROR';
        $answers = $config['answers'];
        $ttl = $config['ttl'];
        $dnssec = $config['dnssec_secure'];
        $soa = $config['soa_serial'] ?? 2026082401;

        return [
            new DnsLayer(
                layer: 0,
                name: 'root',
                label: 'Root Zone (.)',
                servers: '13 IANA Root Clusters (a.root-servers.net - m.root-servers.net)',
                nodes: [
                    new DnsServerNode('a.root-servers.net', '198.41.0.4', ['ns1.iana.org'], 86400, 'NOERROR', $soa, 8.2, true),
                    new DnsServerNode('f.root-servers.net', '192.5.5.241', ['ns2.iana.org'], 86400, 'NOERROR', $soa, 9.4, true),
                ]
            ),
            new DnsLayer(
                layer: 1,
                name: 'tld',
                label: 'Top-Level Domain (TLD)',
                servers: 'Authoritative Registry Nameservers (e.g. gTLD / ccTLD)',
                nodes: [
                    new DnsServerNode('a.gtld-servers.net', '192.5.6.30', ['ns1.cloudflare.com', 'ns2.cloudflare.com'], 172800, 'NOERROR', $soa, 12.1, true),
                ]
            ),
            new DnsLayer(
                layer: 2,
                name: 'authoritative',
                label: 'Authoritative Nameservers',
                servers: 'Zone Host Nameservers (e.g. Cloudflare, Route53, NS1)',
                nodes: [
                    new DnsServerNode('ns1.cloudflare.com', '172.64.32.1', $answers, $ttl, $rcode, $soa, 14.5, $dnssec),
                    new DnsServerNode('ns2.cloudflare.com', '172.64.33.1', $answers, $ttl, $rcode, $soa, 15.2, $dnssec),
                ]
            ),
            new DnsLayer(
                layer: 3,
                name: 'edge_resolvers',
                label: 'Global Edge Anycast Resolvers',
                servers: 'Public Recursive Resolvers (Cloudflare 1.1.1.1, Google 8.8.8.8, Quad9 9.9.9.9, OpenDNS 208.67.222.222)',
                nodes: [
                    new DnsServerNode('Cloudflare (1.1.1.1)', '1.1.1.1', $answers, $ttl, $rcode, null, 6.8, $dnssec),
                    new DnsServerNode('Google (8.8.8.8)', '8.8.8.8', $answers, $ttl, $rcode, null, 11.2, $dnssec),
                    new DnsServerNode('Quad9 (9.9.9.9)', '9.9.9.9', $answers, $ttl, $rcode, null, 13.5, $dnssec),
                    new DnsServerNode('OpenDNS (208.67.222.222)', '208.67.222.222', $answers, $ttl, $rcode, null, 15.1, $dnssec),
                ]
            ),
        ];
    }

    private function createDiagnostic(string $code): DnsDiagnostic
    {
        $def = self::DIAGNOSTIC_DEFINITIONS[$code] ?? [
            'severity' => 'info',
            'title' => $code,
            'description' => $code,
        ];

        return new DnsDiagnostic(
            code: $code,
            severity: $def['severity'],
            title: $def['title'],
            description: $def['description'],
        );
    }
}
