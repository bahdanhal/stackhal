<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\DnsDagTracer\Application\DnsDagTracerService;
use App\Mcp\DnsDagTools;
use PHPUnit\Framework\TestCase;

final class DnsDagToolsTest extends TestCase
{
    private DnsDagTools $tools;

    protected function setUp(): void
    {
        $this->tools = new DnsDagTools(new DnsDagTracerService());
    }

    public function testTraceDnsDelegationToolSuccess(): void
    {
        $response = $this->tools->traceDnsDelegation('stackhal.com', 'A');

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertSame('healthy', $data['result']['status']);
        self::assertSame('secure', $data['result']['dnssec_status']);
        self::assertSame(4, $data['result']['layer_count']);
        self::assertFalse($data['result']['has_divergence']);
        self::assertNotEmpty($data['result']['layers']);
    }

    public function testTraceDnsDelegationToolBogus(): void
    {
        $response = $this->tools->traceDnsDelegation('dnssec-failed.org', 'A');

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertSame('error', $data['result']['status']);
        self::assertSame('bogus', $data['result']['dnssec_status']);
        self::assertContains('ERR_DNSSEC_BOGUS', $data['result']['error_codes']);
    }
}
