<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Domain\Engine;

use App\DnsDagTracer\Domain\Model\DnsDagResult;
use App\DnsDagTracer\Domain\Model\DnsDiagnostic;
use App\DnsDagTracer\Domain\Model\DnsLayer;
use App\DnsDagTracer\Domain\Model\DnssecStatus;
use App\DnsDagTracer\Domain\Model\DnsServerNode;
use App\DnsDagTracer\Domain\Model\QueryType;
use App\DnsDagTracer\Domain\Port\DnsRecordResolver;

final class DnsDagEngine
{
    private const array TYPE_FLAGS = [
        'A' => DNS_A, 'AAAA' => DNS_AAAA, 'CNAME' => DNS_CNAME, 'TXT' => DNS_TXT,
        'MX' => DNS_MX, 'NS' => DNS_NS, 'SOA' => DNS_SOA, 'CAA' => DNS_CAA,
    ];

    public function __construct(private DnsRecordResolver $resolver)
    {
    }

    public function trace(string $domain, QueryType $queryType = QueryType::A): DnsDagResult
    {
        $domain = strtolower(trim($domain, " .\t\n\r\0\x0B"));
        if (!$this->isValidDomain($domain)) {
            return $this->errorResult($domain, $queryType, 'ERR_INVALID_DOMAIN', 'Invalid domain name', 'Enter a valid fully qualified domain name.');
        }

        if (!isset(self::TYPE_FLAGS[$queryType->value])) {
            return $this->errorResult(
                $domain,
                $queryType,
                'ERR_UNSUPPORTED_RECORD_TYPE',
                'Unsupported record type',
                'This PHP resolver does not expose the selected DNS record type.',
            );
        }

        $records = $this->resolver->resolve($domain, self::TYPE_FLAGS[$queryType->value]);
        if ($records === false || $records === []) {
            return $this->errorResult(
                $domain,
                $queryType,
                'ERR_NO_RECORDS',
                'No DNS records returned',
                'The selected record type returned no DNS records from the application resolver.',
            );
        }

        $layers = [$this->buildResolverLayer($domain, $queryType, $records)];
        $authoritativeLayer = $this->buildAuthoritativeLayer($domain);
        if ($authoritativeLayer !== null) {
            $layers[] = $authoritativeLayer;
        }

        return new DnsDagResult(
            status: 'healthy',
            dnssecStatus: DnssecStatus::INDETERMINATE,
            layerCount: count($layers),
            hasDivergence: false,
            layers: $layers,
            diagnostics: [$this->diagnostic(
                'INFO_LIVE_LOOKUP',
                'info',
                'Live DNS response',
                'Answers and TTL values below were returned by the server resolver at request time. '
                . 'DNSSEC cryptographic validation and cross-resolver comparison are not performed by this lookup.',
            )],
            domain: $domain,
            queryType: $queryType,
        );
    }

    /** @param list<array<string, mixed>> $records */
    private function buildResolverLayer(string $domain, QueryType $queryType, array $records): DnsLayer
    {
        $answers = [];
        $ttl = null;
        foreach ($records as $record) {
            $answers[] = $this->formatRecord($record, $queryType);
            if (isset($record['ttl']) && is_int($record['ttl'])) {
                $ttl = $ttl === null ? $record['ttl'] : min($ttl, $record['ttl']);
            }
        }

        return new DnsLayer(
            layer: 0,
            name: 'resolver',
            label: 'System Resolver Response',
            servers: 'Resolver used by the StackHal application host',
            nodes: [new DnsServerNode('Application resolver', '', $answers, $ttl ?? 0)],
        );
    }

    private function buildAuthoritativeLayer(string $domain): ?DnsLayer
    {
        $records = $this->resolver->resolve($domain, DNS_NS);
        if ($records === false || $records === []) {
            return null;
        }

        $nodes = [];
        foreach ($records as $record) {
            $target = isset($record['target']) && is_string($record['target']) ? $record['target'] : '';
            if ($target === '') {
                continue;
            }

            $nodes[] = new DnsServerNode(
                rtrim($target, '.'),
                $this->resolveNameserverIp($target),
                [],
                isset($record['ttl']) && is_int($record['ttl']) ? $record['ttl'] : 0,
            );
        }

        return $nodes === [] ? null : new DnsLayer(
            layer: 1,
            name: 'authoritative',
            label: 'Authoritative Nameservers',
            servers: 'Nameservers returned by the live NS query for this domain',
            nodes: $nodes,
        );
    }

    private function resolveNameserverIp(string $hostname): string
    {
        $records = $this->resolver->resolve($hostname, DNS_A | DNS_AAAA);
        if ($records === false) {
            return '';
        }

        foreach ($records as $record) {
            if (isset($record['ip']) && is_string($record['ip'])) {
                return $record['ip'];
            }
            if (isset($record['ipv6']) && is_string($record['ipv6'])) {
                return $record['ipv6'];
            }
        }

        return '';
    }

    /** @param array<string, mixed> $record */
    private function formatRecord(array $record, QueryType $queryType): string
    {
        $field = match ($queryType) {
            QueryType::A => 'ip', QueryType::AAAA => 'ipv6', QueryType::CNAME, QueryType::NS, QueryType::MX => 'target',
            QueryType::TXT => 'txt', QueryType::SOA => 'mname', QueryType::CAA => 'value',
            QueryType::DS => 'digest', QueryType::DNSKEY => 'key',
        };
        $value = $record[$field] ?? null;
        if (is_string($value)) {
            if ($queryType === QueryType::TXT && isset($record['entries']) && is_array($record['entries'])) {
                return implode('', array_filter($record['entries'], is_string(...)));
            }
            if ($queryType === QueryType::SOA && isset($record['serial']) && is_int($record['serial'])) {
                return sprintf('%s serial %d', $value, $record['serial']);
            }
            return $value;
        }

        return json_encode($record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '(record returned)';
    }

    private function errorResult(string $domain, QueryType $queryType, string $code, string $title, string $description): DnsDagResult
    {
        return new DnsDagResult(
            status: 'error',
            dnssecStatus: DnssecStatus::INDETERMINATE,
            layerCount: 0,
            hasDivergence: false,
            layers: [],
            diagnostics: [$this->diagnostic($code, 'error', $title, $description)],
            domain: $domain,
            queryType: $queryType,
        );
    }

    private function diagnostic(string $code, string $severity, string $title, string $description): DnsDiagnostic
    {
        return new DnsDiagnostic($code, $severity, $title, $description);
    }

    private function isValidDomain(string $domain): bool
    {
        return $domain !== ''
            && strlen($domain) <= 253
            && preg_match('/^(?=.{1,253}\\.?$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $domain) === 1;
    }
}
