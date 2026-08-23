<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Domain\Model;

final readonly class DnsServerNode
{
    /**
     * @param list<string> $answers
     */
    public function __construct(
        public string $serverName,
        public string $serverIp,
        public array $answers,
        public int $ttl,
        public string $rcode = 'NOERROR',
        public ?int $soaSerial = null,
        public float $latencyMs = 12.5,
        public bool $dnssecValidated = true,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'server_name' => $this->serverName,
            'server_ip' => $this->serverIp,
            'answers' => $this->answers,
            'ttl' => $this->ttl,
            'rcode' => $this->rcode,
            'soa_serial' => $this->soaSerial,
            'latency_ms' => $this->latencyMs,
            'dnssec_validated' => $this->dnssecValidated,
        ];
    }
}
