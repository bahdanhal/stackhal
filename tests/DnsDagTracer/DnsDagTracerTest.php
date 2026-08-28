<?php

declare(strict_types=1);

namespace App\Tests\DnsDagTracer;

use App\DnsDagTracer\Application\DnsDagTracerService;
use App\DnsDagTracer\Domain\Engine\DnsDagEngine;
use App\DnsDagTracer\Domain\Model\DnssecStatus;
use App\DnsDagTracer\Domain\Model\QueryType;
use PHPUnit\Framework\TestCase;

final class DnsDagTracerTest extends TestCase
{
    private DnsDagEngine $engine;
    private DnsDagTracerService $service;

    protected function setUp(): void
    {
        $this->engine = new DnsDagEngine();
        $this->service = new DnsDagTracerService($this->engine);
    }

    public function testCleanAuthoritativeTrace(): void
    {
        $result = $this->service->trace('stackhal.com', 'A');

        self::assertSame('healthy', $result->status);
        self::assertSame(DnssecStatus::SECURE, $result->dnssecStatus);
        self::assertSame(4, $result->layerCount);
        self::assertFalse($result->hasDivergence);
        self::assertCount(4, $result->layers);
        self::assertContains('INFO_DNSSEC_SECURE', $result->getInfoCodes());
        self::assertTrue($result->isSimulation);
    }

    public function testBrokenDnssecBogusTrace(): void
    {
        $result = $this->service->trace('dnssec-failed.org', 'A');

        self::assertSame('error', $result->status);
        self::assertSame(DnssecStatus::BOGUS, $result->dnssecStatus);
        self::assertContains('ERR_DNSSEC_BOGUS', $result->getErrorCodes());
    }

    public function testDivergentAnswersTrace(): void
    {
        $result = $this->service->trace('divergent-answers.test', 'A');

        self::assertSame('warning', $result->status);
        self::assertTrue($result->hasDivergence);
        self::assertContains('WARN_HIGH_TTL_MIGRATION_RISK', $result->getWarningCodes());
    }

    public function testNxdomainTrace(): void
    {
        $result = $this->service->trace('nxdomain-example.invalid', 'A');

        self::assertSame('error', $result->status);
        self::assertContains('ERR_NXDOMAIN', $result->getErrorCodes());
    }

    public function testPresetsAvailable(): void
    {
        $presets = $this->service->getPresets();
        self::assertCount(3, $presets);
        self::assertSame('cloudflare_dnssec_clean', $presets[0]['id']);
    }

    public function testArbitraryDomainDoesNotReturnFabricatedTrace(): void
    {
        $result = $this->service->trace('example.com', 'A');

        self::assertSame('error', $result->status);
        self::assertSame(DnssecStatus::INDETERMINATE, $result->dnssecStatus);
        self::assertSame(0, $result->layerCount);
        self::assertEmpty($result->layers);
        self::assertContains('ERR_LIVE_TRACE_UNAVAILABLE', $result->getErrorCodes());
        self::assertFalse($result->isSimulation);
    }

    public function testQueryTypeParsing(): void
    {
        self::assertSame(QueryType::A, QueryType::fromString(null));
        self::assertSame(QueryType::TXT, QueryType::fromString('TXT'));
        self::assertSame(QueryType::AAAA, QueryType::fromString('aaaa'));
        self::assertSame(QueryType::A, QueryType::fromString('INVALID'));
    }
}
