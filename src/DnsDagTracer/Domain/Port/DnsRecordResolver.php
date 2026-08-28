<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Domain\Port;

interface DnsRecordResolver
{
    /** @return list<array<string, mixed>>|false */
    public function resolve(string $hostname, int $type): array|false;
}
