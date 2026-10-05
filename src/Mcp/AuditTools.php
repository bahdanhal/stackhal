<?php

declare(strict_types=1);

namespace App\Mcp;

use App\Audit\Application\SiteAuditor;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Schema\ToolAnnotations;
use Mcp\Capability\Attribute\Schema;

final readonly class AuditTools
{
    public function __construct(private SiteAuditor $auditor)
    {
    }

    #[McpTool(
        name: 'audit_website_seo',
        title: 'Technical SEO Audit',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Run a deterministic technical SEO audit for a public website URL. Checks canonicals, title tags, headings, robots.txt, sitemaps, redirects, crawl traps, and indexability.',
        annotations: new ToolAnnotations(readOnlyHint: true, openWorldHint: true),
    )]
    public function auditWebsite(
        #[Schema(description: 'The public website URL to audit (e.g. https://example.com).')]
        string $url,
    ): string {
        try {
            $report = $this->auditor->audit($url);
            $issues = $report['issues'] ?? [];
            // Counted from the returned list: $report['counts'] counts each issue code once, however often it occurs.
            $severities = array_count_values(array_column($issues, 'severity'));

            return $this->json([
                'target' => $report['target'] ?? $url,
                'status' => 'completed',
                'summary' => [
                    'pages_crawled' => $report['summary']['pages_crawled'] ?? 0,
                    'critical_issues' => $severities['critical'] ?? 0,
                    'warnings' => $severities['warning'] ?? 0,
                    'info_items' => $severities['info'] ?? 0,
                ],
                'issues' => $issues,
                'redirect_matrix' => $report['redirect_matrix'] ?? [],
                'robots_summary' => $report['robots']['status'] ?? null,
                'sitemap_summary' => [
                    'url_count' => count($report['sitemap']['urls'] ?? []),
                    'error_count' => count($report['sitemap']['errors'] ?? []),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'target' => $url,
                'status' => 'error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /** @param array<string, mixed> $data */
    private function json(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
