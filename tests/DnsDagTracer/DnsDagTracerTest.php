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

    public function testLiveTraceReturnsRecordsForResolvableDomain(): void
    {
        $result = $this->service->trace('example.com', 'A');

        self::assertSame('healthy', $result->status);
        self::assertSame(DnssecStatus::INDETERMINATE, $result->dnssecStatus);
        self::assertGreaterThanOrEqual(1, $result->layerCount);
        self::assertFalse($result->hasDivergence);
        self::assertContains('INFO_LIVE_LOOKUP', $result->getInfoCodes());
        self::assertNotEmpty($result->layers[0]->nodes[0]->answers);
    }

    public function testUnresolvableDomainReturnsNoRecords(): void
    {
        $result = $this->service->trace('does-not-exist.invalid', 'A');

        self::assertSame('error', $result->status);
        self::assertContains('ERR_NO_RECORDS', $result->getErrorCodes());
    }

    public function testInvalidDomainDoesNotQueryOrReturnFabricatedTrace(): void
    {
        $result = $this->service->trace('not a domain', 'A');

        self::assertSame('error', $result->status);
        self::assertSame(DnssecStatus::INDETERMINATE, $result->dnssecStatus);
        self::assertSame(0, $result->layerCount);
        self::assertEmpty($result->layers);
        self::assertContains('ERR_INVALID_DOMAIN', $result->getErrorCodes());
    }

    public function testUnsupportedDnssecRecordTypesAreNotClaimedAsSupported(): void
    {
        $result = $this->service->trace('example.com', 'DNSKEY');

        self::assertSame('error', $result->status);
        self::assertContains('ERR_UNSUPPORTED_RECORD_TYPE', $result->getErrorCodes());
    }

    public function testQueryTypeParsing(): void
    {
        self::assertSame(QueryType::A, QueryType::fromString(null));
        self::assertSame(QueryType::TXT, QueryType::fromString('TXT'));
        self::assertSame(QueryType::AAAA, QueryType::fromString('aaaa'));
        self::assertSame(QueryType::A, QueryType::fromString('INVALID'));
    }
}
