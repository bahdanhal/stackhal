<?php

declare(strict_types=1);

namespace App\Tests\AppLinks;

use App\AppLinks\Domain\Engine\AppleCdnComparator;
use App\AppLinks\Domain\Model\AppleCdnReport;
use PHPUnit\Framework\TestCase;

final class AppleCdnComparatorTest extends TestCase
{
    private const string CDN_URL = 'https://app-site-association.cdn-apple.com/a/v1/example.com';
    private const string ORIGIN_URL = 'https://example.com/.well-known/apple-app-site-association';
    private const string AASA = '{"applinks":{"details":[{"appIDs":["ABCDE12345.com.example.app"],"components":[{"/":"/products/*"}]}]}}';

    private AppleCdnComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new AppleCdnComparator();
    }

    public function testBuildsCdnUrlOnlyForPlainHostnames(): void
    {
        self::assertSame(self::CDN_URL, $this->comparator->cdnUrl('example.com'));
        self::assertSame(
            'https://app-site-association.cdn-apple.com/a/v1/xn--bcher-kva.example',
            $this->comparator->cdnUrl('xn--bcher-kva.example'),
        );
        self::assertNull($this->comparator->cdnUrl('example.com/../admin'));
        self::assertNull($this->comparator->cdnUrl('example.com:8443'));
        self::assertNull($this->comparator->cdnUrl('example.com?x=1'));
        self::assertNull($this->comparator->cdnUrl('localhost'));
        self::assertNull($this->comparator->cdnUrl(''));
    }

    public function testPicksTheLastSameDomainAasaUrlAppleReports(): void
    {
        $redirected = ['Apple-From' => [
            'https://example.com/.well-known/apple-app-site-association, '
            . 'https://www.example.com/.well-known/apple-app-site-association',
        ]];
        $rootPath = ['apple-from' => ['https://example.com/apple-app-site-association']];

        self::assertSame(
            'https://www.example.com/.well-known/apple-app-site-association',
            $this->comparator->alternateSource('example.com', $redirected),
        );
        self::assertSame(
            'https://example.com/apple-app-site-association',
            $this->comparator->alternateSource('example.com', $rootPath),
        );
        self::assertNull($this->comparator->alternateSource('example.com', ['apple-from' => [self::ORIGIN_URL]]));
        self::assertNull($this->comparator->alternateSource('example.com', []));
    }

    public function testIgnoresReportedSourcesOutsideTheDomainOrAasaPaths(): void
    {
        foreach (
            [
                'https://evil.test/.well-known/apple-app-site-association',
                'https://notexample.com/.well-known/apple-app-site-association',
                'http://www.example.com/.well-known/apple-app-site-association',
                'https://www.example.com/admin',
                'https://www.example.com:8443/.well-known/apple-app-site-association',
                'https://www.example.com/.well-known/apple-app-site-association?next=1',
                'https://user@www.example.com/.well-known/apple-app-site-association',
            ] as $source
        ) {
            self::assertNull($this->comparator->alternateSource('example.com', ['apple-from' => [$source]]), $source);
        }
    }

    public function testReportsInSyncWhenOnlyKeyOrderAndWhitespaceDiffer(): void
    {
        $cdnBody = <<<'JSON'
{
  "applinks": {
    "details": [
      {"components": [{"/": "/products/*"}], "appIDs": ["ABCDE12345.com.example.app"]}
    ]
  }
}
JSON;

        $report = $this->comparator->compare(self::CDN_URL, self::ORIGIN_URL, true, self::AASA, 200, $cdnBody, [], null);

        self::assertSame(AppleCdnReport::STATE_IN_SYNC, $report->state);
        self::assertSame('INFO_AASA_CDN_IN_SYNC', $report->diagnostics[0]->code);
        self::assertSame(self::ORIGIN_URL, $report->comparedWith);
        self::assertSame('info', $report->diagnostics[0]->severity);
    }

    public function testReportsOutOfSyncWithCacheAge(): void
    {
        $cdnBody = '{"applinks":{"details":[{"appIDs":["ABCDE12345.com.example.app"],"components":[{"/":"/old/*"}]}]}}';
        $headers = ['Cache-Control' => ['max-age=21600,public'], 'Age' => ['1800']];

        $report = $this->comparator->compare(self::CDN_URL, self::ORIGIN_URL, true, self::AASA, 200, $cdnBody, $headers, null);

        self::assertSame(AppleCdnReport::STATE_OUT_OF_SYNC, $report->state);
        self::assertSame('WARN_AASA_CDN_OUT_OF_SYNC', $report->diagnostics[0]->code);
        self::assertSame(21600, $report->cacheMaxAgeSeconds);
        self::assertSame(1800, $report->cacheAgeSeconds);
        self::assertStringContainsString('30 minutes old', $report->diagnostics[0]->description);
        self::assertStringContainsString('up to 6 hours', $report->diagnostics[0]->description);
    }

    public function testReportsErrorWhenOriginServesButAppleCannotFetch(): void
    {
        $headers = [
            'apple-failure-reason' => ['SWCERR00101 Bad HTTP Response: 403 Forbidden'],
            'apple-failure-details' => ['{"status":"403 Forbidden"}'],
            'apple-from' => ['https://example.com/.well-known/apple-app-site-association'],
            'cache-control' => ['max-age=3600,public'],
            'age' => ['60'],
        ];

        $report = $this->comparator->compare(self::CDN_URL, self::ORIGIN_URL, true, self::AASA, 404, 'Not Found', $headers, null);

        self::assertSame(AppleCdnReport::STATE_FETCH_FAILED, $report->state);
        self::assertSame('ERR_AASA_CDN_FETCH_FAILED', $report->diagnostics[0]->code);
        self::assertSame('error', $report->diagnostics[0]->severity);
        self::assertStringContainsString('SWCERR00101 Bad HTTP Response: 403 Forbidden', $report->diagnostics[0]->description);
        self::assertSame('SWCERR00101 Bad HTTP Response: 403 Forbidden', $report->toArray()['failure_reason']);
        self::assertSame('https://example.com/.well-known/apple-app-site-association', $report->source);
    }

    public function testReportsAppleFailureAsInfoWhenOriginAlsoFails(): void
    {
        $headers = ['apple-failure-reason' => ['SWCERR00101 Bad HTTP Response: 404 Not Found']];

        $report = $this->comparator->compare(self::CDN_URL, self::ORIGIN_URL, false, '', 404, 'Not Found', $headers, null);

        self::assertSame(AppleCdnReport::STATE_FETCH_FAILED, $report->state);
        self::assertSame('INFO_AASA_CDN_FAILURE_REPORTED', $report->diagnostics[0]->code);
        self::assertSame('info', $report->diagnostics[0]->severity);
    }

    public function testReportsCdnOnlyCopyWithItsSource(): void
    {
        $headers = ['apple-from' => ['https://example.com/apple-app-site-association']];

        $report = $this->comparator->compare(self::CDN_URL, self::ORIGIN_URL, false, '', 200, self::AASA, $headers, null);

        self::assertSame(AppleCdnReport::STATE_CDN_ONLY, $report->state);
        self::assertSame('WARN_AASA_CDN_ORIGIN_MISMATCH', $report->diagnostics[0]->code);
        self::assertStringContainsString('https://example.com/apple-app-site-association', $report->diagnostics[0]->description);
    }

    public function testReportsUncheckedWhenCdnIsUnreachableOrUnexpected(): void
    {
        $unreachable = $this->comparator->compare(self::CDN_URL, self::ORIGIN_URL, true, self::AASA, 0, '', [], 'Timeout');
        $unexpected = $this->comparator->compare(self::CDN_URL, self::ORIGIN_URL, true, self::AASA, 503, '', [], null);

        self::assertSame(AppleCdnReport::STATE_UNCHECKED, $unreachable->state);
        self::assertSame('INFO_AASA_CDN_UNCHECKED', $unreachable->diagnostics[0]->code);
        self::assertSame(AppleCdnReport::STATE_UNCHECKED, $unexpected->state);
    }

    public function testStripsControlCharactersAndBoundsReportedHeaderText(): void
    {
        $headers = ['apple-failure-reason' => ["SWCERR00301 Timeout\r\n" . str_repeat('x', 500)]];

        $report = $this->comparator->compare(self::CDN_URL, self::ORIGIN_URL, true, self::AASA, 404, '', $headers, null);

        self::assertNotNull($report->failureReason);
        self::assertSame(300, mb_strlen($report->failureReason));
        self::assertStringNotContainsString("\n", $report->failureReason);
    }
}
