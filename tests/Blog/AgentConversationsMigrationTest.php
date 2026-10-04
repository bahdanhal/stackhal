<?php

declare(strict_types=1);

namespace App\Tests\Blog;

use App\Blog\Domain\BlogArticle;
use App\Blog\Infrastructure\DoctrineBlogArticleRepository;
use App\Tests\DoctrineTestCase;
use Doctrine\DBAL\Schema\Schema;
use DoctrineMigrations\Version20261002180000;
use Psr\Log\NullLogger;

final class AgentConversationsMigrationTest extends DoctrineTestCase
{
    public function testSeedPairsLocalesPreservesEditsAndDoesNotDuplicateAnEdition(): void
    {
        require_once dirname(__DIR__, 2) . '/migrations/Version20261002180000.php';
        $connection = $this->entityManager->getConnection();
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $migration = new Version20261002180000($connection, new NullLogger());
            $migration->up(new Schema());
            foreach ($migration->getSql() as $query) {
                $connection->executeStatement($query->getStatement(), $query->getParameters(), $query->getTypes());
            }
            if ($attempt === 0) {
                $connection->executeStatement("UPDATE blog_articles SET title = 'Reviewed title' WHERE locale = 'en'");
            }
        }

        self::assertSame(2, $connection->fetchOne('SELECT COUNT(*) FROM blog_articles'));
        self::assertSame('Reviewed title', $connection->fetchOne("SELECT title FROM blog_articles WHERE locale = 'en'"));
        $repository = new DoctrineBlogArticleRepository($this->entityManager);
        $english = $repository->findAllForAdmin('en')[0];
        $polish = $repository->findAllForAdmin('pl')[0];
        self::assertSame(BlogArticle::AGENT_CONVERSATIONS_CATEGORY, $english->getCategory());
        self::assertSame('2026-10-02T16:00:00+00:00', $english->getPublishedAt()->format('c'));
        self::assertSame($polish->getSlug(), $english->getAlternateSlug());
        self::assertSame($english->getSlug(), $polish->getAlternateSlug());
        self::assertSame('', $english->getCtaPath());
        self::assertSame('', $polish->getCtaPath());
        foreach ([$english, $polish] as $edition) {
            $document = new \DOMDocument();
            @$document->loadHTML('<?xml encoding="UTF-8">' . $edition->getContentHtml());
            $xpath = new \DOMXPath($document);
            self::assertSame(5.0, $xpath->evaluate('count(//h2)'));
            self::assertSame(5.0, $xpath->evaluate('count(//p[@class="digest-source"]/time)'));
            self::assertSame(5.0, $xpath->evaluate('count(//a[starts-with(@href, "https://")])'));
        }
    }
}
