<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Audit\Application\AiSummaryService;
use App\Audit\Application\AuditLogger;
use App\Audit\Application\SiteAuditor;
use App\Audit\Domain\AuditRuleEngine;
use App\Audit\Domain\EditorialAdvisoryCatalog;
use App\Crawl\Application\PageAnalyzer;
use App\Crawl\Application\SitemapInspector;
use App\Crawl\Domain\RobotsPolicy;
use App\Shared\Application\HttpFetcher;
use App\Shared\Application\UrlGuard;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class SiteAuditorTest extends TestCase
{
    public function testRedirectedRootDoesNotProduceDuplicateCrawledPagesOrDuplicateFindings(): void
    {
        $urlGuard = new class () implements UrlGuard {
            public function normalize(string $input): string
            {
                return $input;
            }
        };

        $html = <<<'HTML'
<!doctype html>
<html lang="pl">
<head>
    <title>Web24 - Software House</title>
    <link rel="canonical" href="https://example.com/pl">
    <meta name="description" content="Software house based in Poland.">
</head>
<body>
    <h1>Web24</h1>
    <p>We build exceptional web systems for companies.</p>
    <a href="https://example.com/">Home</a>
</body>
</html>
HTML;

        $fetcher = new class ($html) implements HttpFetcher {
            public function __construct(private readonly string $html)
            {
            }

            /**
             * @return array{
             *     requested_url: string,
             *     final_url: string,
             *     status: int,
             *     headers: array<string, list<string>>,
             *     body: string,
             *     content_type: string,
             *     duration_ms: int,
             *     redirects: list<array{url: string, status: int, location: ?string}>,
             *     error: ?string
             * }
             */
            public function fetch(string $url, int $maxRedirects = 8): array
            {
                if ($url === 'https://example.com' || $url === 'https://example.com/') {
                    return [
                        'requested_url' => $url,
                        'final_url' => 'https://example.com/pl',
                        'status' => 200,
                        'headers' => ['content-type' => ['text/html; charset=utf-8']],
                        'body' => $this->html,
                        'content_type' => 'text/html; charset=utf-8',
                        'duration_ms' => 10,
                        'redirects' => [['url' => $url, 'status' => 302, 'location' => 'https://example.com/pl']],
                        'error' => null,
                    ];
                }

                if ($url === 'https://example.com/pl') {
                    return [
                        'requested_url' => $url,
                        'final_url' => 'https://example.com/pl',
                        'status' => 200,
                        'headers' => ['content-type' => ['text/html; charset=utf-8']],
                        'body' => $this->html,
                        'content_type' => 'text/html; charset=utf-8',
                        'duration_ms' => 10,
                        'redirects' => [],
                        'error' => null,
                    ];
                }

                if (str_ends_with($url, '/robots.txt')) {
                    return [
                        'requested_url' => $url,
                        'final_url' => $url,
                        'status' => 200,
                        'headers' => ['content-type' => ['text/plain']],
                        'body' => "User-agent: *\nAllow: /",
                        'content_type' => 'text/plain',
                        'duration_ms' => 5,
                        'redirects' => [],
                        'error' => null,
                    ];
                }

                if (str_ends_with($url, '/sitemap.xml')) {
                    return [
                        'requested_url' => $url,
                        'final_url' => $url,
                        'status' => 200,
                        'headers' => ['content-type' => ['application/xml']],
                        'body' => '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>https://example.com/pl</loc></url></urlset>',
                        'content_type' => 'application/xml',
                        'duration_ms' => 5,
                        'redirects' => [],
                        'error' => null,
                    ];
                }

                return [
                    'requested_url' => $url,
                    'final_url' => $url,
                    'status' => 404,
                    'headers' => [],
                    'body' => '',
                    'content_type' => 'text/html',
                    'duration_ms' => 5,
                    'redirects' => [],
                    'error' => 'Not Found',
                ];
            }

            /**
             * @param list<string> $urls
             * @return array<string, array{
             *     requested_url: string,
             *     final_url: string,
             *     status: int,
             *     headers: array<string, list<string>>,
             *     body: string,
             *     content_type: string,
             *     duration_ms: int,
             *     redirects: list<array{url: string, status: int, location: ?string}>,
             *     error: ?string
             * }>
             */
            public function fetchMany(array $urls, int $maxRedirects = 8): array
            {
                $results = [];
                foreach ($urls as $url) {
                    $results[$url] = $this->fetch($url, $maxRedirects);
                }
                return $results;
            }

            public function resolveUrl(string $base, string $reference): string
            {
                if (str_starts_with($reference, 'http://') || str_starts_with($reference, 'https://')) {
                    return $reference;
                }
                return rtrim($base, '/') . '/' . ltrim($reference, '/');
            }
        };

        $pageAnalyzer = new PageAnalyzer($fetcher);
        $sitemapInspector = new SitemapInspector($fetcher);
        $ruleEngine = new AuditRuleEngine();
        $editorialCatalog = new EditorialAdvisoryCatalog();
        $robotsPolicy = new RobotsPolicy();
        $aiClient = $this->createStub(\App\Shared\AI\AiClient::class);
        $aiClient->method('complete')->willThrowException(new \RuntimeException('No AI in test'));
        $aiSummary = new AiSummaryService($aiClient, 'test-fingerprint');
        $auditLogger = $this->createStub(AuditLogger::class);
        $auditLogger->method('newAuditId')->willReturn('audit-test');
        $auditCache = new ArrayAdapter();

        $auditor = new SiteAuditor(
            $urlGuard,
            $fetcher,
            $pageAnalyzer,
            $sitemapInspector,
            $ruleEngine,
            $editorialCatalog,
            $robotsPolicy,
            $aiSummary,
            $auditLogger,
            $auditCache,
            maxPages: 10,
            concurrency: 2,
            batchDelayMs: 0,
            cacheTtl: 3600,
        );

        $report = $auditor->audit('https://example.com/');

        self::assertSame(1, $report['summary']['pages_crawled']);
        self::assertCount(1, $report['pages']);
        self::assertSame('https://example.com/pl', $report['pages'][0]['final_url']);

        $issueCodes = array_column($report['issues'], 'code');
        self::assertNotContains('duplicate-content', $issueCodes);
        self::assertNotContains('duplicate-title', $issueCodes);
    }
}
