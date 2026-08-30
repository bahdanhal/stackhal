<?php

declare(strict_types=1);

namespace App\Mcp;

use App\Analytics\Application\TrafficAnalytics;
use App\Audit\Infrastructure\AuditLogger;
use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use App\Entity\BlogArticleEntity;
use Bahdan\LeadCaptureBundle\Domain\Lead;
use Bahdan\LeadCaptureBundle\Domain\LeadRepository;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;

final readonly class AdminTools
{
    public function __construct(
        private AdminAccess $access,
        private LeadRepository $leads,
        private AuditLogger $auditLogger,
        private TrafficAnalytics $trafficAnalytics,
        private BlogArticleRepository $blogArticles,
    ) {
    }

    #[McpTool(
        name: 'get_admin_dashboard_statistics',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Admin-only: Get privacy-preserving traffic, submission, and SEO audit statistics. Requires an Authorization: Bearer header.'
    )]
    public function statistics(): string
    {
        if (!$this->access->isGranted()) {
            return $this->unauthorized();
        }

        $leads = $this->leads->all();
        $auditEvents = $this->auditLogger->events();
        $seoEvents = array_values(array_filter(
            $auditEvents,
            static fn (array $e): bool => !str_starts_with((string) ($e['event'] ?? ''), 'geo_') && (($e['tool'] ?? '') !== 'geo'),
        ));
        $geoEvents = array_values(array_filter(
            $auditEvents,
            static fn (array $e): bool => str_starts_with((string) ($e['event'] ?? ''), 'geo_') || (($e['tool'] ?? '') === 'geo'),
        ));
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $sevenDaysAgo = $now->modify('-7 days');
        $thirtyDaysAgo = $now->modify('-30 days');

        return $this->json([
            'generated_at' => $now->format(DATE_ATOM),
            'submissions' => [
                'contact_leads' => $this->submissionStats(
                    $leads,
                    static fn (Lead $lead): \DateTimeImmutable => $lead->createdAt,
                    $sevenDaysAgo,
                    $thirtyDaysAgo,
                ),
            ],
            'seo_audits' => $this->auditStatistics($seoEvents, $sevenDaysAgo, $thirtyDaysAgo),
            'geo_audits' => $this->auditStatistics($geoEvents, $sevenDaysAgo, $thirtyDaysAgo),
            'traffic' => $this->trafficAnalytics->summary($now),
            'lead_sources' => $this->frequencies(array_map(static fn (Lead $lead): string => $lead->source, $leads)),
        ]);
    }

    #[McpTool(
        name: 'list_admin_recent_audits',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Admin-only: List recent SEO audit runs with sanitized targets, status, score and runtime details. Requires an Authorization: Bearer header.'
    )]
    public function recentAudits(
        #[Schema(description: 'Maximum audit runs to return, from 1 to 100.')] int $limit = 50,
    ): string {
        if (!$this->access->isGranted()) {
            return $this->unauthorized();
        }
        if (!$this->validLimit($limit)) {
            return $this->invalidLimit();
        }

        $runs = array_values($this->auditRuns($this->auditLogger->events()));
        usort(
            $runs,
            static fn (array $left, array $right): int => ($right['requested_at'] ?? $right['completed_at'] ?? '')
                <=> ($left['requested_at'] ?? $left['completed_at'] ?? ''),
        );

        return $this->json([
            'retention_note' => 'Audit logs are retained for the configured short operational window.',
            'total' => count($runs),
            'returned' => min($limit, count($runs)),
            'items' => array_slice($runs, 0, $limit),
        ]);
    }

    #[McpTool(
        name: 'list_admin_contact_leads',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Admin-only: List recent private consultation requests, including contact details and messages. Requires an Authorization: Bearer header.'
    )]
    public function contactLeads(
        #[Schema(description: 'Maximum contact leads to return, from 1 to 100.')] int $limit = 50,
    ): string {
        if (!$this->access->isGranted()) {
            return $this->unauthorized();
        }
        if (!$this->validLimit($limit)) {
            return $this->invalidLimit();
        }

        $leads = $this->leads->all();
        usort(
            $leads,
            static fn (Lead $left, Lead $right): int => $right->createdAt <=> $left->createdAt,
        );

        return $this->json([
            'total' => count($leads),
            'returned' => min($limit, count($leads)),
            'items' => array_map(
                static fn (Lead $lead): array => [
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'message' => $lead->message,
                    'source' => $lead->source,
                    'created_at' => $lead->createdAt->format(DATE_ATOM),
                ],
                array_slice($leads, 0, $limit),
            ),
        ]);
    }

    #[McpTool(
        name: 'list_admin_blog_articles',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Admin-only: List blog articles across locales with metadata, word counts, and readability scores. Requires an Authorization: Bearer header.'
    )]
    public function blogArticles(
        #[Schema(description: 'Filter by locale ("en" or "pl"), or omit for all.')] ?string $locale = null,
        #[Schema(description: 'Maximum articles to return, from 1 to 100.')] int $limit = 50,
    ): string {
        if (!$this->access->isGranted()) {
            return $this->unauthorized();
        }
        if (!$this->validLimit($limit)) {
            return $this->invalidLimit();
        }

        $articles = $this->blogArticles->findAllForAdmin($locale);

        return $this->json([
            'total' => count($articles),
            'returned' => min($limit, count($articles)),
            'items' => array_map(
                function (BlogArticle $article): array {
                    $plain = trim((string) preg_replace('/\s+/', ' ', strip_tags($article->getContentHtml())));
                    $words = count(preg_split('/\s+/', $plain) ?: []);

                    return [
                        'id' => $article->getId(),
                        'locale' => $article->getLocale(),
                        'slug' => $article->getSlug(),
                        'alternate_slug' => $article->getAlternateSlug(),
                        'title' => $article->getTitle(),
                        'description' => $article->getDescription(),
                        'category' => $article->getCategory(),
                        'read_time_minutes' => $article->getReadTimeMinutes(),
                        'words' => $words,
                        'published_at' => $article->getPublishedAt()->format(DATE_ATOM),
                        'updated_at' => $article->getUpdatedAt()->format(DATE_ATOM),
                        'url' => ($article->getLocale() === 'pl' ? '/pl/blog/' : '/blog/') . $article->getSlug(),
                    ];
                },
                array_slice($articles, 0, $limit),
            ),
        ]);
    }

    #[McpTool(
        name: 'get_admin_blog_article',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Admin-only: Get complete blog article content, HTML, and readability analysis by slug. Requires an Authorization: Bearer header.'
    )]
    public function getBlogArticle(
        #[Schema(description: 'URL slug of the article.')] string $slug,
        #[Schema(description: 'Locale of the article ("en" or "pl"). Default: "en".')] string $locale = 'en',
    ): string {
        if (!$this->access->isGranted()) {
            return $this->unauthorized();
        }

        $entity = $this->blogArticles->findEntityBySlugAndLocale($slug, $locale);
        if ($entity === null) {
            return $this->json(['error' => 'Article not found for given slug and locale.']);
        }

        $plain = trim((string) preg_replace('/\s+/', ' ', strip_tags($entity->getContentHtml())));
        $words = array_values(array_filter(preg_split('/\s+/', $plain) ?: []));
        $sentences = array_values(array_filter(preg_split('/[.!?]+/', $plain) ?: []));

        return $this->json([
            'id' => $entity->getId(),
            'locale' => $entity->getLocale(),
            'slug' => $entity->getSlug(),
            'alternate_slug' => $entity->getAlternateSlug(),
            'title' => $entity->getTitle(),
            'description' => $entity->getDescription(),
            'category' => $entity->getCategory(),
            'read_time_minutes' => $entity->getReadTimeMinutes(),
            'content_html' => $entity->getContentHtml(),
            'cta_label' => $entity->getCtaLabel(),
            'cta_path' => $entity->getCtaPath(),
            'visual_class' => $entity->getVisualClass(),
            'visual_lines' => $entity->getVisualLines(),
            'how_to_steps' => $entity->getHowToSteps(),
            'published_at' => $entity->getPublishedAt()->format(DATE_ATOM),
            'updated_at' => $entity->getUpdatedAt()->format(DATE_ATOM),
            'text_stats' => [
                'words' => count($words),
                'sentences' => count($sentences),
            ],
        ]);
    }

    #[McpTool(
        name: 'save_admin_blog_article',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Admin-only: Create or update a blog article directly with automatic smart-character cleanup and plain ASCII normalization. Requires an Authorization: Bearer header.'
    )]
    public function saveBlogArticle(
        #[Schema(description: 'URL slug of the article.')] string $slug,
        #[Schema(description: 'Article headline / title.')] string $title,
        #[Schema(description: 'Short summary or lead dek.')] string $description,
        #[Schema(description: 'Full body HTML content.')] string $content_html,
        #[Schema(description: 'Language locale ("en" or "pl"). Default: "en".')] string $locale = 'en',
        #[Schema(description: 'Paired alternate language slug.')] string $alternate_slug = '',
        #[Schema(description: 'Category name.')] string $category = 'Technical Guide',
        #[Schema(description: 'Estimated reading time in minutes (0 for automatic).')] int $read_time_minutes = 0,
        #[Schema(description: 'Sidebar CTA button text.')] string $cta_label = 'Open Tool',
        #[Schema(description: 'Sidebar CTA destination URL.')] string $cta_path = '/',
        #[Schema(description: 'Visual badge style class.')] string $visual_class = 'terminal-card',
    ): string {
        if (!$this->access->isGranted()) {
            return $this->unauthorized();
        }

        // Clean smart characters
        $slug = strtolower(trim($slug));
        $title = $this->cleanSymbols(trim($title));
        $description = $this->cleanSymbols(trim($description));
        $contentHtml = $this->cleanSymbols(trim($content_html));
        $alternateSlug = strtolower(trim($alternate_slug));

        if ($slug === '' || $title === '' || $contentHtml === '') {
            return $this->json(['error' => 'slug, title, and content_html cannot be empty.']);
        }

        if ($read_time_minutes <= 0) {
            $words = count(preg_split('/\s+/u', strip_tags($contentHtml)) ?: []);
            $read_time_minutes = max(1, (int) ceil($words / 180));
        }

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $entity = $this->blogArticles->findEntityBySlugAndLocale($slug, $locale);
        $isNew = false;

        if ($entity === null) {
            $isNew = true;
            $entity = new BlogArticleEntity(
                $slug,
                $title,
                $description,
                $category,
                $read_time_minutes,
                $now,
                $now,
                $contentHtml,
                $cta_label,
                $cta_path,
                $visual_class,
                [],
                [],
                $locale,
                $alternateSlug
            );
        } else {
            $entity->setTitle($title);
            $entity->setDescription($description);
            $entity->setContentHtml($contentHtml);
            $entity->setCategory($category);
            $entity->setReadTimeMinutes($read_time_minutes);
            $entity->setCtaLabel($cta_label);
            $entity->setCtaPath($cta_path);
            $entity->setVisualClass($visual_class);
            $entity->setAlternateSlug($alternateSlug);
            $entity->setUpdatedAt($now);
        }

        $this->blogArticles->save($entity);

        return $this->json([
            'status' => 'success',
            'action' => $isNew ? 'created' : 'updated',
            'id' => $entity->getId(),
            'locale' => $entity->getLocale(),
            'slug' => $entity->getSlug(),
            'title' => $entity->getTitle(),
            'url' => ($entity->getLocale() === 'pl' ? '/pl/blog/' : '/blog/') . $entity->getSlug(),
        ]);
    }

    #[McpTool(
        name: 'delete_admin_blog_article',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Admin-only: Delete a blog article by slug and locale. Requires an Authorization: Bearer header.'
    )]
    public function deleteBlogArticle(
        #[Schema(description: 'URL slug of the article to delete.')] string $slug,
        #[Schema(description: 'Locale of the article ("en" or "pl"). Default: "en".')] string $locale = 'en',
    ): string {
        if (!$this->access->isGranted()) {
            return $this->unauthorized();
        }

        $entity = $this->blogArticles->findEntityBySlugAndLocale($slug, $locale);
        if ($entity === null) {
            return $this->json(['error' => 'Article not found.']);
        }

        $this->blogArticles->delete($entity);

        return $this->json([
            'status' => 'success',
            'action' => 'deleted',
            'slug' => $slug,
            'locale' => $locale,
        ]);
    }

    private function cleanSymbols(string $text): string
    {
        $replacements = [
            "\u{2014}" => '-',
            "\u{2013}" => '-',
            "\u{2018}" => "'",
            "\u{2019}" => "'",
            "\u{201C}" => '"',
            "\u{201D}" => '"',
            "\u{00A0}" => ' ',
        ];

        return strtr($text, $replacements);
    }

    /**
     * @template T of object
     * @param list<T> $items
     * @param \Closure(T): \DateTimeImmutable $dateExtractor
     * @return array{last_7_days: int, last_30_days: int}
     */
    private function submissionStats(
        array $items,
        \Closure $dateExtractor,
        \DateTimeImmutable $sevenDaysAgo,
        \DateTimeImmutable $thirtyDaysAgo,
    ): array {
        $last7 = 0;
        $last30 = 0;

        foreach ($items as $item) {
            $date = $dateExtractor($item);
            if ($date >= $sevenDaysAgo) {
                ++$last7;
            }
            if ($date >= $thirtyDaysAgo) {
                ++$last30;
            }
        }

        return ['last_7_days' => $last7, 'last_30_days' => $last30];
    }

    /**
     * @param list<array<string, mixed>> $events
     * @return array{total: int, completed: int, failed: int, last_7_days: int, last_30_days: int}
     */
    private function auditStatistics(
        array $events,
        \DateTimeImmutable $sevenDaysAgo,
        \DateTimeImmutable $thirtyDaysAgo,
    ): array {
        $runs = $this->auditRuns($events);
        $total = count($runs);
        $completed = 0;
        $failed = 0;
        $last7 = 0;
        $last30 = 0;

        foreach ($runs as $run) {
            if (($run['status'] ?? null) === 'completed') {
                ++$completed;
            } elseif (($run['status'] ?? null) === 'failed') {
                ++$failed;
            }

            $dateString = (string) ($run['requested_at'] ?? $run['completed_at'] ?? '');
            if ($dateString === '') {
                continue;
            }
            try {
                $date = new \DateTimeImmutable($dateString);
            } catch (\Exception) {
                continue;
            }
            if ($date >= $sevenDaysAgo) {
                ++$last7;
            }
            if ($date >= $thirtyDaysAgo) {
                ++$last30;
            }
        }

        return [
            'total' => $total,
            'completed' => $completed,
            'failed' => $failed,
            'last_7_days' => $last7,
            'last_30_days' => $last30,
        ];
    }

    /**
     * @param list<array<string, mixed>> $events
     * @return array<string, array<string, mixed>>
     */
    private function auditRuns(array $events): array
    {
        $runs = [];
        foreach ($events as $event) {
            $auditId = (string) ($event['audit_id'] ?? '');
            if ($auditId === '') {
                continue;
            }
            $runs[$auditId] ??= ['audit_id' => $auditId, 'status' => 'running'];
            if ($event['event'] === 'audit_requested' || $event['event'] === 'geo_audit_requested') {
                $runs[$auditId]['requested_at'] = (string) $event['timestamp'];
                $runs[$auditId]['target'] = (string) ($event['target'] ?? '');
            }
            if ($event['event'] === 'audit_completed' || $event['event'] === 'geo_audit_completed') {
                $runs[$auditId] = [
                    ...$runs[$auditId],
                    'status' => 'completed',
                    'completed_at' => (string) $event['timestamp'],
                    'target' => (string) ($event['target'] ?? $runs[$auditId]['target'] ?? ''),
                    'score' => (int) ($event['score'] ?? 0),
                    'pages_crawled' => (int) ($event['pages_crawled'] ?? 1),
                    'duration_ms' => (int) ($event['request_duration_ms'] ?? 0),
                    'cache_hit' => (bool) ($event['cache_hit'] ?? false),
                ];
            }
            if ($event['event'] === 'audit_failed' || $event['event'] === 'geo_audit_failed') {
                $runs[$auditId] = [
                    ...$runs[$auditId],
                    'status' => 'failed',
                    'completed_at' => (string) $event['timestamp'],
                    'duration_ms' => (int) ($event['request_duration_ms'] ?? 0),
                    'error_type' => (string) ($event['error_type'] ?? ''),
                    'error' => (string) ($event['error'] ?? ''),
                ];
            }
        }

        return $runs;
    }

    /**
     * @param list<string> $values
     * @return array<string, int>
     */
    private function frequencies(array $values): array
    {
        $counts = array_count_values(array_filter($values, static fn (string $value): bool => $value !== ''));
        arsort($counts);

        return array_slice($counts, 0, 10, true);
    }

    private function validLimit(int $limit): bool
    {
        return $limit >= 1 && $limit <= 100;
    }

    private function unauthorized(): string
    {
        return $this->json(['error' => 'Unauthorized: valid admin Bearer token required.']);
    }

    private function invalidLimit(): string
    {
        return $this->json(['error' => 'Limit must be between 1 and 100.']);
    }

    /** @param array<string, mixed> $data */
    private function json(array $data): string
    {
        return json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        );
    }
}
