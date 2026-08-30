<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\Analytics\Application\TrafficAnalytics;
use App\Analytics\Infrastructure\JsonlPageViewRepository;
use App\Audit\Infrastructure\AuditLogger;
use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use App\Entity\BlogArticleEntity;
use Bahdan\LeadCaptureBundle\Domain\Lead;
use Bahdan\LeadCaptureBundle\Domain\LeadRepository;
use App\Lead\Infrastructure\JsonlLeadRepository;
use App\Mcp\AdminAccess;
use App\Mcp\AdminTools;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class AdminToolsTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/admin-mcp-test-' . bin2hex(random_bytes(5));
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->directory);
    }

    public function testAllAdminToolsFailClosedWithoutBearerToken(): void
    {
        $tools = $this->tools(false);

        $responses = [
            $tools->statistics(),
            $tools->contactLeads(),
            $tools->recentAudits(),
            $tools->blogArticles(),
            $tools->getBlogArticle('test-slug'),
            $tools->saveBlogArticle('test-slug', 'Title', 'Desc', '<p>Content</p>'),
            $tools->deleteBlogArticle('test-slug'),
        ];
        foreach ($responses as $json) {
            $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
            self::assertStringContainsString('Unauthorized', $data['error']);
        }
    }

    public function testAdminCanReadSubmissionsAndAggregateStatistics(): void
    {
        $leadRepository = new JsonlLeadRepository($this->directory . '/leads');
        $leadRepository->save(Lead::create(
            'person@example.com',
            '+48 500 000 000',
            'Please review my workflow.',
            'hashed-ip',
            'landing',
        ));

        $auditLogger = new AuditLogger($this->directory . '/audits', 14);
        $auditLogger->log('audit_requested', [
            'audit_id' => 'audit-123',
            'target' => 'https://example.com/',
        ]);
        $auditLogger->log('audit_completed', [
            'audit_id' => 'audit-123',
            'target' => 'https://example.com/',
            'score' => 91,
            'pages_crawled' => 4,
            'request_duration_ms' => 1200,
            'cache_hit' => false,
        ]);
        $auditLogger->log('audit_failed', [
            'audit_id' => 'audit-unsafe',
            'request_duration_ms' => 0,
            'error_type' => 'App\\Exception\\UnsafeUrlException',
            'error' => 'Local and internal hostnames are not allowed.',
        ]);

        $tools = $this->tools(true, $leadRepository, $auditLogger);
        $statistics = json_decode($tools->statistics(), true, flags: JSON_THROW_ON_ERROR);
        $leads = json_decode($tools->contactLeads(10), true, flags: JSON_THROW_ON_ERROR);
        $audits = json_decode($tools->recentAudits(10), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame(1, $statistics['submissions']['contact_leads']['last_7_days']);
        self::assertSame(2, $statistics['seo_audits']['total']);
        self::assertSame(2, $statistics['seo_audits']['last_7_days']);
        self::assertSame(1, $statistics['seo_audits']['completed']);
        self::assertSame(1, $statistics['seo_audits']['failed']);
        self::assertSame(0, $statistics['geo_audits']['total']);
        self::assertSame(0, $statistics['traffic']['last_30_days']['page_views']);
        self::assertSame('person@example.com', $leads['items'][0]['email']);
        self::assertSame(2, $audits['total']);
        self::assertSame('failed', $audits['items'][0]['status']);
        self::assertSame(91, $audits['items'][1]['score']);
    }

    public function testAdminCanManageBlogArticlesViaMcp(): void
    {
        $repo = new class implements BlogArticleRepository {
            /** @var array<string, BlogArticleEntity> */
            private array $entities = [];

            public function findPublished(?string $locale = null): array
            {
                return $this->findAllForAdmin($locale);
            }

            public function findPublishedBySlug(string $slug, string $locale = 'en'): ?BlogArticle
            {
                $key = $locale . ':' . $slug;
                $e = $this->entities[$key] ?? null;
                return $e === null ? null : new BlogArticle(
                    $e->getSlug(),
                    $e->getTitle(),
                    $e->getDescription(),
                    $e->getCategory(),
                    $e->getReadTimeMinutes(),
                    $e->getPublishedAt(),
                    $e->getUpdatedAt(),
                    $e->getContentHtml(),
                    $e->getCtaLabel(),
                    $e->getCtaPath(),
                    $e->getVisualClass(),
                    $e->getVisualLines(),
                    $e->getHowToSteps(),
                    $e->getLocale(),
                    $e->getAlternateSlug()
                );
            }

            public function findAllForAdmin(?string $locale = null): array
            {
                $res = [];
                foreach ($this->entities as $e) {
                    if ($locale === null || $e->getLocale() === $locale) {
                        $res[] = new BlogArticle(
                            $e->getSlug(),
                            $e->getTitle(),
                            $e->getDescription(),
                            $e->getCategory(),
                            $e->getReadTimeMinutes(),
                            $e->getPublishedAt(),
                            $e->getUpdatedAt(),
                            $e->getContentHtml(),
                            $e->getCtaLabel(),
                            $e->getCtaPath(),
                            $e->getVisualClass(),
                            $e->getVisualLines(),
                            $e->getHowToSteps(),
                            $e->getLocale(),
                            $e->getAlternateSlug()
                        );
                    }
                }
                return $res;
            }

            public function findEntity(int $id): ?BlogArticleEntity
            {
                return null;
            }

            public function findEntityBySlugAndLocale(string $slug, string $locale): ?BlogArticleEntity
            {
                return $this->entities[$locale . ':' . $slug] ?? null;
            }

            public function save(BlogArticleEntity $entity): void
            {
                $this->entities[$entity->getLocale() . ':' . $entity->getSlug()] = $entity;
            }

            public function delete(BlogArticleEntity $entity): void
            {
                unset($this->entities[$entity->getLocale() . ':' . $entity->getSlug()]);
            }
        };

        $tools = $this->tools(true, null, null, $repo);

        // Save article with em-dash and curly quotes to verify automatic cleaning
        $saveRes = json_decode($tools->saveBlogArticle(
            'dns-guide',
            "DNS — The Engineer's Guide",
            'A guide on DNS.',
            '<p>DNS is important.</p>',
            'en',
            'przewodnik-dns'
        ), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('success', $saveRes['status']);
        self::assertSame('created', $saveRes['action']);
        self::assertSame("DNS - The Engineer's Guide", $saveRes['title']);

        // List articles
        $listRes = json_decode($tools->blogArticles('en'), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(1, $listRes['total']);
        self::assertSame('dns-guide', $listRes['items'][0]['slug']);

        // Get single article
        $getRes = json_decode($tools->getBlogArticle('dns-guide', 'en'), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame("DNS - The Engineer's Guide", $getRes['title']);
        self::assertSame('przewodnik-dns', $getRes['alternate_slug']);

        // Delete article
        $delRes = json_decode($tools->deleteBlogArticle('dns-guide', 'en'), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('success', $delRes['status']);
        self::assertSame('deleted', $delRes['action']);

        $listEmpty = json_decode($tools->blogArticles('en'), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(0, $listEmpty['total']);
    }

    public function testAdminListsRejectUnsafeResultLimits(): void
    {
        $tools = $this->tools(true);
        $data = json_decode($tools->contactLeads(101), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('Limit must be between 1 and 100.', $data['error']);
    }

    private function tools(
        bool $authenticated,
        ?LeadRepository $leads = null,
        ?AuditLogger $auditLogger = null,
        ?BlogArticleRepository $blogArticles = null,
    ): AdminTools {
        $requestStack = new RequestStack();
        $request = new Request();
        if ($authenticated) {
            $request->headers->set('Authorization', 'Bearer admin-test-token');
        }
        $requestStack->push($request);

        $defaultBlogRepo = new class implements BlogArticleRepository {
            public function findPublished(?string $locale = null): array
            {
                return [];
            }
            public function findPublishedBySlug(string $slug, string $locale = 'en'): ?BlogArticle
            {
                return null;
            }
            public function findAllForAdmin(?string $locale = null): array
            {
                return [];
            }
            public function findEntity(int $id): ?BlogArticleEntity
            {
                return null;
            }
            public function findEntityBySlugAndLocale(string $slug, string $locale): ?BlogArticleEntity
            {
                return null;
            }
            public function save(BlogArticleEntity $entity): void
            {
            }
            public function delete(BlogArticleEntity $entity): void
            {
            }
        };

        return new AdminTools(
            new AdminAccess($requestStack, 'admin-test-token'),
            $leads ?? new JsonlLeadRepository($this->directory . '/leads'),
            $auditLogger ?? new AuditLogger($this->directory . '/audits', 14),
            new TrafficAnalytics(new JsonlPageViewRepository($this->directory . '/analytics', 90)),
            $blogArticles ?? $defaultBlogRepo,
        );
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }
        foreach (scandir($directory) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $directory . '/' . $entry;
            is_dir($path) ? $this->removeDirectory($path) : @unlink($path);
        }
        @rmdir($directory);
    }
}
