<?php

declare(strict_types=1);

namespace App\Tests\Blog;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\AbortMigration;
use DoctrineMigrations\Version20260917233000;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class CensusStandaloneMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/migrations/Version20260917233000.php';
    }

    public function testArticleIsStandaloneAndHasNoGoArgument(): void
    {
        $articles = Version20260917233000::corrections();
        self::assertCount(1, $articles);
        $article = $articles['ai-coding-agents-deprecated-dependencies-census'];
        self::assertSame('New Repositories, Old Dependencies: A 34,187-Repository Study', $article['title']);
        foreach (['21,653', '1,981', '2,187', '857', '517', '236', '14.2.15', '124', '9.39.5', '58', '2.12.7', '77', 'first ten', 'not confirmed installed versions', 'approximate version matching', 'observations.sqlite.gz', 'verify.sql', 'methodology notes'] as $text) {
            self::assertStringContainsString($text, $article['content_html']);
        }
        foreach (['earlier article', 'previous version', 'recovered', 'correction', 'original claims', 'Go minimum', 'go.mod', '50.6%', 'go-minimum-version-sample', 'EOL compilers'] as $text) {
            self::assertStringNotContainsString($text, $article['content_html']);
        }
        self::assertDoesNotMatchRegularExpression('/\\bGo\\b/', $article['content_html']);
    }

    public function testPackageExamplesMatchPublicObservations(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'census-examples-');
        self::assertNotFalse($path);
        $compressed = file_get_contents(dirname(__DIR__, 2) . '/public/research/repository-sample-2026-09-04/observations.sqlite.gz');
        self::assertNotFalse($compressed);
        $bytes = gzdecode($compressed);
        self::assertNotFalse($bytes);
        file_put_contents($path, $bytes);
        unset($compressed, $bytes);
        try {
            $database = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'path' => $path]);
            self::assertSame(21653, $database->fetchOne('SELECT COUNT(DISTINCT repo_name) FROM dependencies'));
            foreach (['next' => 857, 'eslint' => 517, 'recharts' => 236] as $package => $count) {
                self::assertSame($count, $database->fetchOne("SELECT COUNT(DISTINCT repo_name) FROM dependencies WHERE ecosystem = 'npm' AND is_deprecated = 1 AND package_name = ?", [$package]));
            }
            foreach ([['next', '14.2.15', 124], ['next', '14.2.5', 114], ['eslint', '9.39.5', 58], ['recharts', '2.12.7', 77]] as [$package, $version, $count]) {
                self::assertSame($count, $database->fetchOne("SELECT COUNT(*) FROM dependencies WHERE ecosystem = 'npm' AND is_deprecated = 1 AND package_name = ? AND matched_version = ?", [$package, $version]));
            }
            $database->close();
        } finally {
            unlink($path);
        }
    }

    public function testMigrationIsScopedAndRestoresOriginalSnapshots(): void
    {
        $connection = $this->connection();
        $original = $connection->fetchAllAssociative('SELECT * FROM blog_articles ORDER BY slug, locale');
        $migration = new Version20260917233000($connection, new NullLogger());
        $migration->up(new Schema());
        $this->execute($connection, $migration);
        foreach (Version20260917233000::corrections() as $slug => $correction) {
            $article = $connection->fetchAssociative('SELECT * FROM blog_articles WHERE slug = ? AND locale = ?', [$slug, 'en']);
            self::assertIsArray($article);
            self::assertSame($correction['title'], $article['title']);
            self::assertSame($correction['content_html'], $article['content_html']);
            self::assertSame('', $article['cta_path']);
            self::assertSame('[]', $article['how_to_steps']);
            self::assertSame('Original', $connection->fetchOne('SELECT title FROM blog_articles WHERE slug = ? AND locale = ?', [$slug, 'pl']));
        }
        self::assertSame('Original', $connection->fetchOne("SELECT title FROM blog_articles WHERE slug = 'unrelated'"));
        $rollback = new Version20260917233000($connection, new NullLogger());
        $rollback->down(new Schema());
        $this->execute($connection, $rollback);
        self::assertSame($original, $connection->fetchAllAssociative('SELECT * FROM blog_articles ORDER BY slug, locale'));
    }

    public function testRollbackRefusesToOverwriteLaterEditorialChanges(): void
    {
        $connection = $this->connection();
        $migration = new Version20260917233000($connection, new NullLogger());
        $migration->up(new Schema());
        $this->execute($connection, $migration);
        $connection->executeStatement("UPDATE blog_articles SET description = 'Later edit' WHERE locale = 'en'");
        $this->expectException(AbortMigration::class);
        (new Version20260917233000($connection, new NullLogger()))->down(new Schema());
    }

    public function testMissingArticlesAreNotCreated(): void
    {
        $connection = $this->connection();
        $connection->executeStatement("DELETE FROM blog_articles WHERE slug <> 'unrelated'");
        $migration = new Version20260917233000($connection, new NullLogger());
        $migration->up(new Schema());
        $this->execute($connection, $migration);
        self::assertSame(2, $connection->fetchOne('SELECT COUNT(*) FROM blog_articles'));
        $rollback = new Version20260917233000($connection, new NullLogger());
        $rollback->down(new Schema());
        $this->execute($connection, $rollback);
        self::assertSame(2, $connection->fetchOne('SELECT COUNT(*) FROM blog_articles'));
    }

    private function connection(): Connection
    {
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $connection->executeStatement('CREATE TABLE blog_articles (slug TEXT, locale TEXT, title TEXT, description TEXT, category TEXT, read_time_minutes INTEGER, updated_at TEXT, content_html TEXT, cta_label TEXT, cta_path TEXT, visual_lines TEXT, how_to_steps TEXT)');
        foreach ([...array_keys(Version20260917233000::corrections()), 'unrelated'] as $slug) {
            foreach (['en', 'pl'] as $locale) {
                $connection->insert('blog_articles', ['slug' => $slug, 'locale' => $locale, 'title' => 'Original', 'description' => 'Original description', 'category' => 'Original category', 'read_time_minutes' => 5, 'updated_at' => '2026-09-04', 'content_html' => '<p>Original body</p>', 'cta_label' => 'Original CTA', 'cta_path' => '/original', 'visual_lines' => '["Original"]', 'how_to_steps' => '[]']);
            }
        }
        return $connection;
    }

    private function execute(Connection $connection, Version20260917233000 $migration): void
    {
        foreach ($migration->getSql() as $query) {
            $connection->executeStatement($query->getStatement(), $query->getParameters(), $query->getTypes());
        }
    }
}
