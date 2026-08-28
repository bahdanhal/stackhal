<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\DnsDagTracer\Application\DnsDagTracerService;
use App\DnsDagTracer\Domain\Engine\DnsDagEngine;
use App\DnsDagTracer\Domain\Port\DnsRecordResolver;
use App\Mcp\DnsDagTools;
use PHPUnit\Framework\TestCase;

final class DnsDagToolsTest extends TestCase
{
    private DnsDagTools $tools;

    protected function setUp(): void
    {
        $resolver = $this->createStub(DnsRecordResolver::class);
        $resolver->method('resolve')->willReturnCallback(static function (string $hostname, int $type): array|false {
            if ($type === DNS_NS) {
                return [['target' => 'ns1.example.com.', 'ttl' => 300]];
            }
            if (($type & DNS_A) !== 0 && $hostname === 'ns1.example.com.') {
                return [['ip' => '192.0.2.1', 'ttl' => 300]];
            }
            return [['ip' => '93.184.216.34', 'ttl' => 300]];
        });
        $this->tools = new DnsDagTools(new DnsDagTracerService(new DnsDagEngine($resolver)));
    }

    public function testTraceDnsDelegationToolSuccess(): void
    {
        $response = $this->tools->traceDnsDelegation('example.com', 'A');

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertSame('healthy', $data['result']['status']);
        self::assertSame('indeterminate', $data['result']['dnssec_status']);
        self::assertSame(2, $data['result']['layer_count']);
        self::assertFalse($data['result']['has_divergence']);
        self::assertNotEmpty($data['result']['layers']);
    }

    public function testTraceDnsDelegationToolInvalidDomain(): void
    {
        $response = $this->tools->traceDnsDelegation('not a domain', 'A');

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertSame('error', $data['result']['status']);
        self::assertSame('indeterminate', $data['result']['dnssec_status']);
        self::assertContains('ERR_INVALID_DOMAIN', $data['result']['error_codes']);
    }
}
