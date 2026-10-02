<?php

declare(strict_types=1);

namespace App\Tests\Blog;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use DoctrineMigrations\Version20261002210000;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class DebuggingTitleMigrationTest extends TestCase
{
    public function testRenamePreservesContentUrlAndOtherArticles(): void
    {
        $connection = $this->connection();
        foreach (['en', 'pl'] as $locale) {
            $connection->insert('blog_articles', [
                'slug' => 'debugging-is-almost-dead-in-2026',
                'locale' => $locale,
                'title' => 'I Think Debugging Is Almost Dead',
                'content_html' => '<p>A later editorial addition.</p>',
                'updated_at' => '2000-01-01',
            ]);
        }
        $connection->insert('blog_articles', [
            'slug' => 'unrelated',
            'locale' => 'en',
            'title' => 'I Think Debugging Is Almost Dead',
            'content_html' => '<p>Another article.</p>',
            'updated_at' => '2000-01-01',
        ]);
        $this->migrate($connection);
        $this->migrate($connection);

        $article = $connection->fetchAssociative("SELECT * FROM blog_articles WHERE locale = 'en' AND slug <> 'unrelated'");
        self::assertIsArray($article);
        self::assertSame('Debugging Is Dead', $article['title']);
        self::assertSame('debugging-is-almost-dead-in-2026', $article['slug']);
        self::assertSame('<p>A later editorial addition.</p>', $article['content_html']);
        self::assertNotSame('2000-01-01', $article['updated_at']);
        self::assertSame('I Think Debugging Is Almost Dead', $connection->fetchOne("SELECT title FROM blog_articles WHERE locale = 'pl'"));
        self::assertSame('I Think Debugging Is Almost Dead', $connection->fetchOne("SELECT title FROM blog_articles WHERE slug = 'unrelated'"));

        $this->migrate($connection, true);
        self::assertSame(
            'I Think Debugging Is Almost Dead',
            $connection->fetchOne("SELECT title FROM blog_articles WHERE locale = 'en' AND slug <> 'unrelated'")
        );
        $connection->executeStatement("UPDATE blog_articles SET title = 'Newer title' WHERE locale = 'en'");
        $this->migrate($connection);
        $this->migrate($connection, true);
        self::assertSame('Newer title', $connection->fetchOne("SELECT title FROM blog_articles WHERE locale = 'en' AND slug <> 'unrelated'"));
    }

    public function testMissingArticleIsNotCreated(): void
    {
        $connection = $this->connection();
        $this->migrate($connection);
        $this->migrate($connection, true);
        self::assertSame(0, $connection->fetchOne('SELECT COUNT(*) FROM blog_articles'));
    }

    private function connection(): Connection
    {
        require_once dirname(__DIR__, 2) . '/migrations/Version20261002210000.php';
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $connection->executeStatement(
            'CREATE TABLE blog_articles (slug TEXT, locale TEXT, title TEXT, content_html TEXT, updated_at TEXT)'
        );
        return $connection;
    }

    private function migrate(Connection $connection, bool $rollback = false): void
    {
        $migration = new Version20261002210000($connection, new NullLogger());
        if ($rollback) {
            $migration->down(new Schema());
        } else {
            $migration->up(new Schema());
        }
        foreach ($migration->getSql() as $query) {
            $connection->executeStatement($query->getStatement(), $query->getParameters(), $query->getTypes());
        }
    }
}
