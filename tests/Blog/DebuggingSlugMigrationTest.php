<?php

declare(strict_types=1);

namespace App\Tests\Blog;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use DoctrineMigrations\Version20261013120000;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class DebuggingSlugMigrationTest extends TestCase
{
    public function testMoveKeepsContentAndLeavesOtherArticlesAlone(): void
    {
        $connection = $this->connection();
        $connection->insert('blog_articles', [
            'slug' => 'debugging-is-almost-dead-in-2026',
            'locale' => 'en',
            'title' => 'Debugging Is Dead',
            'content_html' => '<p>Body.</p>',
        ]);
        $connection->insert('blog_articles', [
            'slug' => 'unrelated',
            'locale' => 'en',
            'title' => 'Other',
            'content_html' => '<p>Other.</p>',
        ]);

        $this->migrate($connection);
        $this->migrate($connection);

        self::assertSame(
            '<p>Body.</p>',
            $connection->fetchOne("SELECT content_html FROM blog_articles WHERE slug = 'debugging-is-dead'")
        );
        self::assertSame(0, (int) $connection->fetchOne("SELECT COUNT(*) FROM blog_articles WHERE slug = 'debugging-is-almost-dead-in-2026'"));
        self::assertSame(1, (int) $connection->fetchOne("SELECT COUNT(*) FROM blog_articles WHERE slug = 'unrelated'"));

        $this->migrate($connection, true);
        self::assertSame(1, (int) $connection->fetchOne("SELECT COUNT(*) FROM blog_articles WHERE slug = 'debugging-is-almost-dead-in-2026'"));
    }

    public function testMoveDoesNotOverwriteAnArticleAlreadyAtTheNewUrl(): void
    {
        $connection = $this->connection();
        foreach (['debugging-is-almost-dead-in-2026', 'debugging-is-dead'] as $slug) {
            $connection->insert('blog_articles', ['slug' => $slug, 'locale' => 'en', 'title' => $slug, 'content_html' => '']);
        }

        $this->migrate($connection);

        self::assertSame(1, (int) $connection->fetchOne("SELECT COUNT(*) FROM blog_articles WHERE slug = 'debugging-is-almost-dead-in-2026'"));
        self::assertSame(1, (int) $connection->fetchOne("SELECT COUNT(*) FROM blog_articles WHERE slug = 'debugging-is-dead'"));
    }

    private function connection(): Connection
    {
        require_once dirname(__DIR__, 2) . '/migrations/Version20261013120000.php';
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $connection->executeStatement('CREATE TABLE blog_articles (slug TEXT, locale TEXT, title TEXT, content_html TEXT)');

        return $connection;
    }

    private function migrate(Connection $connection, bool $rollback = false): void
    {
        $migration = new Version20261013120000($connection, new NullLogger());
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
