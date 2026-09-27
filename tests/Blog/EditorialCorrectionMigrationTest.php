<?php

declare(strict_types=1);

namespace App\Tests\Blog;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\AbortMigration;
use DoctrineMigrations\Version20260917120000;
use DoctrineMigrations\Version20260927120000;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class EditorialCorrectionMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/migrations/Version20260917120000.php';
        require_once dirname(__DIR__, 2) . '/migrations/Version20260927120000.php';
    }

    public function testCorrectionsMakeClaimsAndLimitationsExplicit(): void
    {
        $corrections = Version20260917120000::corrections();
        $census = $corrections['ai-coding-agents-deprecated-dependencies-census'];
        self::assertStringContainsString('Observations and Limits', $census['title']);
        self::assertStringContainsString('minimum required Go version', $census['content_html']);
        self::assertStringContainsString('does not identify the actual compiler', $census['content_html']);
        self::assertStringContainsString('not independently verified results', $census['content_html']);
        self::assertStringContainsString('34,187', $census['content_html']);
        self::assertStringContainsString('171,796', $census['content_html']);
        self::assertStringContainsString('have not been published', $census['content_html']);
        self::assertStringNotContainsString('/domain-security', $census['content_html']);
        $framework = $corrections['bmad-vs-gsd-ai-agent-frameworks-benchmark'];
        self::assertSame('BMAD vs GSD: An Engineering Tradeoff Guide', $framework['title']);
        self::assertStringContainsString('does not publish a controlled comparison', $framework['content_html']);
        self::assertStringContainsString('Where role decomposition can help', $framework['content_html']);
        self::assertStringContainsString('https://arxiv.org/abs/2307.03172', $framework['content_html']);
        foreach (['55.8%', '37.3%', 'GSD won', 'VERIFIED BENCHMARKS', 'stopgap hack'] as $claim) {
            self::assertStringNotContainsString($claim, $framework['content_html']);
        }
    }

    public function testMigrationIsScopedAndRestoresOriginalSnapshots(): void
    {
        $connection = $this->connection();
        $original = $connection->fetchAllAssociative('SELECT * FROM blog_articles ORDER BY slug, locale');
        $migration = new Version20260917120000($connection, new NullLogger());
        $migration->up(new Schema());
        $this->execute($connection, $migration);
        foreach (Version20260917120000::corrections() as $slug => $correction) {
            $article = $connection->fetchAssociative('SELECT * FROM blog_articles WHERE slug = ? AND locale = ?', [$slug, 'en']);
            self::assertIsArray($article);
            self::assertSame($correction['title'], $article['title']);
            self::assertSame($correction['content_html'], $article['content_html']);
            self::assertSame('', $article['cta_path']);
            self::assertSame('[]', $article['how_to_steps']);
            self::assertSame('Original', $connection->fetchOne('SELECT title FROM blog_articles WHERE slug = ? AND locale = ?', [$slug, 'pl']));
        }
        self::assertSame('Original', $connection->fetchOne("SELECT title FROM blog_articles WHERE slug = 'unrelated'"));
        $rollback = new Version20260917120000($connection, new NullLogger());
        $rollback->down(new Schema());
        $this->execute($connection, $rollback);
        self::assertSame($original, $connection->fetchAllAssociative('SELECT * FROM blog_articles ORDER BY slug, locale'));
    }

    public function testRollbackRefusesToOverwriteLaterEditorialChanges(): void
    {
        $connection = $this->connection();
        $migration = new Version20260917120000($connection, new NullLogger());
        $migration->up(new Schema());
        $this->execute($connection, $migration);
        $connection->executeStatement("UPDATE blog_articles SET description = 'Later edit' WHERE locale = 'en'");
        $this->expectException(AbortMigration::class);
        (new Version20260917120000($connection, new NullLogger()))->down(new Schema());
    }

    public function testMissingArticlesAreNotCreated(): void
    {
        $connection = $this->connection();
        $connection->executeStatement("DELETE FROM blog_articles WHERE slug <> 'unrelated'");
        $migration = new Version20260917120000($connection, new NullLogger());
        $migration->up(new Schema());
        $this->execute($connection, $migration);
        self::assertSame(2, $connection->fetchOne('SELECT COUNT(*) FROM blog_articles'));
        $rollback = new Version20260917120000($connection, new NullLogger());
        $rollback->down(new Schema());
        $this->execute($connection, $rollback);
        self::assertSame(2, $connection->fetchOne('SELECT COUNT(*) FROM blog_articles'));
    }

    public function testForwardMigrationUpdatesExistingArticleAndPreservesSurroundingEdits(): void
    {
        $connection = $this->connection();
        $historicalMigration = new Version20260917120000($connection, new NullLogger());
        $historicalMigration->up(new Schema());
        $this->execute($connection, $historicalMigration);

        $historicalArticle = $connection->fetchAssociative('SELECT content_html FROM blog_articles WHERE slug = ? AND locale = ?', ['bmad-vs-gsd-ai-agent-frameworks-benchmark', 'en']);
        self::assertIsArray($historicalArticle);
        self::assertStringContainsString('Correction: no measured winner', $historicalArticle['content_html']);

        $editedContent = '<p class="custom-editorial-note">Retain this later addition.</p>' . $historicalArticle['content_html'];
        $connection->executeStatement("UPDATE blog_articles SET content_html = ?, updated_at = '2000-01-01' WHERE slug = ? AND locale = ?", [$editedContent, 'bmad-vs-gsd-ai-agent-frameworks-benchmark', 'en']);

        $migration = new Version20260927120000($connection, new NullLogger());
        $migration->up(new Schema());
        $this->execute($connection, $migration);

        $article = $connection->fetchAssociative('SELECT * FROM blog_articles WHERE slug = ? AND locale = ?', ['bmad-vs-gsd-ai-agent-frameworks-benchmark', 'en']);
        self::assertIsArray($article);
        self::assertStringContainsString('Retain this later addition.', $article['content_html']);
        self::assertStringContainsString('Where role decomposition can help', $article['content_html']);
        self::assertStringContainsString('task uncertainty, team structure, and the cost of coordination', $article['content_html']);
        self::assertStringContainsString('track completion, defects, cost, and review effort', $article['content_html']);
        self::assertStringNotContainsString('Correction: no measured winner', $article['content_html']);
        self::assertSame('Compare role-based and spec-driven AI coding workflows by task clarity, handoff cost, parallelism, and the evidence needed to review completed work.', $article['description']);
        self::assertSame('BMAD vs GSD: An Engineering Tradeoff Guide', $article['title']);
        self::assertSame('AI engineering opinion', $article['category']);
        self::assertNotSame('2000-01-01', $article['updated_at']);
        self::assertSame('Original', $connection->fetchOne('SELECT title FROM blog_articles WHERE slug = ? AND locale = ?', ['bmad-vs-gsd-ai-agent-frameworks-benchmark', 'pl']));
    }

    public function testForwardMigrationAbortsWhenTargetCopyWasEdited(): void
    {
        $connection = $this->connection();
        $connection->executeStatement(
            "UPDATE blog_articles SET content_html = '<p>Newer editorial version</p>' WHERE slug = ? AND locale = ?",
            ['bmad-vs-gsd-ai-agent-frameworks-benchmark', 'en']
        );

        $this->expectException(AbortMigration::class);
        (new Version20260927120000($connection, new NullLogger()))->up(new Schema());
    }

    private function connection(): Connection
    {
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $connection->executeStatement('CREATE TABLE blog_articles (slug TEXT, locale TEXT, title TEXT, description TEXT, category TEXT, read_time_minutes INTEGER, updated_at TEXT, content_html TEXT, cta_label TEXT, cta_path TEXT, visual_lines TEXT, how_to_steps TEXT)');
        foreach ([...array_keys(Version20260917120000::corrections()), 'unrelated'] as $slug) {
            foreach (['en', 'pl'] as $locale) {
                $connection->insert('blog_articles', ['slug' => $slug, 'locale' => $locale, 'title' => 'Original', 'description' => 'Original description', 'category' => 'Original category', 'read_time_minutes' => 5, 'updated_at' => '2026-09-04', 'content_html' => '<p>Original body</p>', 'cta_label' => 'Original CTA', 'cta_path' => '/original', 'visual_lines' => '["Original"]', 'how_to_steps' => '[]']);
            }
        }
        return $connection;
    }

    private function execute(Connection $connection, Version20260917120000|Version20260927120000 $migration): void
    {
        foreach ($migration->getSql() as $query) {
            $connection->executeStatement($query->getStatement(), $query->getParameters(), $query->getTypes());
        }
    }
}
