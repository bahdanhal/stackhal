<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use Bahdan\SafeHttpClient\DnsResolverInterface;

/**
 * Resolves hosts with blocking system lookups.
 *
 * The package's default resolver awaits an event loop and returns nothing when called from inside a fiber it
 * does not own, which is how the MCP server runs tool handlers. IPv4 addresses come first so the pinned
 * address is reachable from containers without an IPv6 route.
 */
final readonly class NativeHostResolver implements DnsResolverInterface
{
    /** @var \Closure(string, int): (list<array<string, mixed>>|false) */
    private \Closure $lookup;

    /**
     * @param (\Closure(string, int): (list<array<string, mixed>>|false))|null $lookup
     */
    public function __construct(?\Closure $lookup = null)
    {
        $this->lookup = $lookup ?? static fn (string $host, int $type): array|false => @dns_get_record($host, $type);
    }

    public function resolveMany(array $hosts): array
    {
        $results = [];
        foreach (array_values(array_unique($hosts)) as $host) {
            $results[$host] = array_values(array_unique([
                ...$this->addresses($host, DNS_A, 'ip'),
                ...$this->addresses($host, DNS_AAAA, 'ipv6'),
            ]));
        }

        return $results;
    }

    /**
     * @return list<string>
     */
    private function addresses(string $host, int $type, string $key): array
    {
        $addresses = [];
        foreach (($this->lookup)($host, $type) ?: [] as $record) {
            $address = $record[$key] ?? null;
            if (is_string($address) && filter_var($address, FILTER_VALIDATE_IP) !== false) {
                $addresses[] = $address;
            }
        }

        return $addresses;
    }
}
