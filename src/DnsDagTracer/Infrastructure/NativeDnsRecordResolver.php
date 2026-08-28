<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Infrastructure;

use App\DnsDagTracer\Domain\Port\DnsRecordResolver;

final class NativeDnsRecordResolver implements DnsRecordResolver
{
    public function resolve(string $hostname, int $type): array|false
    {
        error_clear_last();

        /** @var list<array<string, mixed>>|false */
        return @dns_get_record($hostname, $type);
    }
}
