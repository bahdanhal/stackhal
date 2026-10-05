<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\Audit\Application\AiSummaryService;
use App\Audit\Application\AuditLogger;
use App\Audit\Application\SiteAuditor;
use App\Audit\Domain\AuditRuleEngine;
use App\Audit\Domain\EditorialAdvisoryCatalog;
use App\Crawl\Application\PageAnalyzer;
use App\Crawl\Application\SitemapInspector;
use App\Crawl\Domain\RobotsPolicy;
use App\Mcp\AuditTools;
use App\Shared\AI\AiClient;
use App\Shared\Application\HttpFetcher;
use App\Shared\Application\UrlGuard;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class AuditToolsTest extends TestCase
{
    public function testSummaryCountsMatchTheReturnedIssueList(): void
    {
        $data = json_decode($this->tools()->auditWebsite('https://example.com/'), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('completed', $data['status']);

        // Both sitemaps fail, so the list holds the "sitemap-error" warning twice.
        $codes = array_count_values(array_column($data['issues'], 'code'));
        self::assertSame(2, $codes['sitemap-error']);

        $severities = array_count_values(array_column($data['issues'], 'severity'));
        self::assertSame(1, $severities['critical']);
        self::assertSame(3, $severities['warning']);
        self::assertSame(2, $severities['info']);

        self::assertSame([
            'pages_crawled' => 1,
            'critical_issues' => 1,
            'warnings' => 3,
            'info_items' => 2,
        ], $data['summary']);
    }

    private function tools(): AuditTools
    {
        $urlGuard = new class () implements UrlGuard {
            public function normalize(string $input): string
            {
                return $input;
            }
        };

        $fetcher = new class () implements HttpFetcher {
            private const HOME = 'https://example.com/';

            // No canonical, two H1 headings, no meta description and very little text.
            private const HOME_HTML = <<<'HTML'
<!doctype html>
<html lang="en">
<head>
    <title>Example Domain</title>
</head>
<body>
    <h1>Example Domain</h1>
    <h1>Documentation examples</h1>
    <p>This domain is for use in documentation examples.</p>
</body>
</html>
HTML;

            private const ROBOTS = <<<'TXT'
User-agent: *
Allow: /
Sitemap: https://example.com/sitemap-a.xml
Sitemap: https://example.com/sitemap-b.xml
TXT;

            public function fetch(string $url, int $maxRedirects = 8): array
            {
                $path = (string) parse_url($url, PHP_URL_PATH);
                $hasQuery = parse_url($url, PHP_URL_QUERY) !== null;

                if (($path === '' || $path === '/') && !$hasQuery) {
                    return $this->response(
                        $url,
                        200,
                        'text/html; charset=utf-8',
                        self::HOME_HTML,
                        self::HOME,
                        $url === self::HOME ? [] : [['url' => $url, 'status' => 301, 'location' => self::HOME]],
                    );
                }

                if ($path === '/robots.txt') {
                    return $this->response($url, 200, 'text/plain', self::ROBOTS);
                }

                return $this->response($url, 404, 'text/html', '');
            }

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
                return $reference;
            }

            /**
             * @param list<array{url: string, status: int, location: ?string}> $redirects
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
            private function response(
                string $url,
                int $status,
                string $contentType,
                string $body,
                ?string $finalUrl = null,
                array $redirects = [],
            ): array {
                return [
                    'requested_url' => $url,
                    'final_url' => $finalUrl ?? $url,
                    'status' => $status,
                    'headers' => ['content-type' => [$contentType]],
                    'body' => $body,
                    'content_type' => $contentType,
                    'duration_ms' => 1,
                    'redirects' => $redirects,
                    'error' => $status === 404 ? 'Not Found' : null,
                ];
            }
        };

        $aiClient = $this->createStub(AiClient::class);
        $aiClient->method('complete')->willThrowException(new \RuntimeException('No AI in test'));
        $auditLogger = $this->createStub(AuditLogger::class);
        $auditLogger->method('newAuditId')->willReturn('audit-test');

        return new AuditTools(new SiteAuditor(
            $urlGuard,
            $fetcher,
            new PageAnalyzer($fetcher),
            new SitemapInspector($fetcher),
            new AuditRuleEngine(),
            new EditorialAdvisoryCatalog(),
            new RobotsPolicy(),
            new AiSummaryService($aiClient, 'test-fingerprint'),
            $auditLogger,
            new ArrayAdapter(),
            maxPages: 10,
            concurrency: 2,
            batchDelayMs: 0,
            cacheTtl: 3600,
        ));
    }
}
