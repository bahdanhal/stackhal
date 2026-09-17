<?php

declare(strict_types=1);

namespace App\Tests\Blog;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\AbortMigration;
use DoctrineMigrations\Version20260917220000;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class CensusRecoveryMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/migrations/Version20260917220000.php';
    }

    public function testRecoveredArticleLinksEvidenceAndBoundsClaims(): void
    {
        $corrections = Version20260917220000::corrections();
        self::assertCount(1, $corrections);
        $census = $corrections['ai-coding-agents-deprecated-dependencies-census'];
        self::assertSame('What a Scan of 34,187 New Repositories Actually Measured', $census['title']);
        foreach (['401,703', '171,796', '171,766', '171,642', '154', '50.6%', 'one without a parsed version', 'first ten', 'No stored field identifies AI authorship', 'observations.sqlite.gz', 'verify.sql', 'languages.csv', 'README.txt', 'go-minimum-version-sample-2026.svg'] as $claim) {
            self::assertStringContainsString($claim, $census['content_html']);
        }
        self::assertStringNotContainsString('not independently verified results', $census['content_html']);
        self::assertStringNotContainsString('go-compiler-eol-census-2026.svg', $census['content_html']);
        self::assertStringNotContainsString('/domain-security', $census['content_html']);
    }

    public function testPublicEvidenceReproducesPublishedCounts(): void
    {
        $directory = dirname(__DIR__, 2) . '/public/research/repository-sample-2026-09-04';
        $temporary = tempnam(sys_get_temp_dir(), 'census-evidence-');
        self::assertNotFalse($temporary);
        $compressed = gzopen($directory . '/observations.sqlite.gz', 'rb');
        self::assertIsResource($compressed);
        $destination = fopen($temporary, 'wb');
        self::assertIsResource($destination);
        try {
            while (!gzeof($compressed)) {
                $chunk = gzread($compressed, 65536);
                self::assertNotFalse($chunk);
                fwrite($destination, $chunk);
            }
            fclose($destination);
            gzclose($compressed);
            self::assertSame('ef50e8c23862a6698a05ac89b2b684ff90c863bd39b826aa1d6a52f0f9d76dfb', hash_file('sha256', $temporary));
            $database = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'path' => $temporary]);
            self::assertSame('ok', $database->fetchOne('PRAGMA quick_check'));
            self::assertSame(34187, $database->fetchOne('SELECT COUNT(*) FROM repos'));
            self::assertSame(25322, $database->fetchOne("SELECT COUNT(*) FROM repos WHERE manifest_path != 'NONE'"));
            self::assertSame(401703, $database->fetchOne('SELECT SUM(total_deps) FROM repos'));
            self::assertSame(171766, $database->fetchOne('SELECT SUM(sampled_deps) FROM repos'));
            self::assertSame(171796, $database->fetchOne('SELECT COUNT(*) FROM dependencies'));
            self::assertSame(2187, $database->fetchOne('SELECT COUNT(*) FROM dependencies WHERE is_deprecated = 1'));
            self::assertSame(1981, $database->fetchOne('SELECT COUNT(DISTINCT repo_name) FROM dependencies WHERE is_deprecated = 1'));
            self::assertSame(154, $database->fetchOne('SELECT SUM(n - 1) FROM (SELECT COUNT(*) n FROM dependencies GROUP BY repo_name, ecosystem, package_name, version_spec, matched_version, is_deprecated, dep_reason, published_at, age_days HAVING COUNT(*) > 1)'));
            self::assertSame(575, $database->fetchOne("SELECT COUNT(*) FROM repos WHERE language = 'Go'"));
            self::assertSame(291, $database->fetchOne("SELECT COUNT(*) FROM repos WHERE language = 'Go' AND runtime_status LIKE 'EOL%'"));
            self::assertSame(177, $database->fetchOne("SELECT COUNT(*) FROM repos WHERE language = 'Go' AND runtime_status = 'Modern (Go 1.26)'"));
            self::assertSame(106, $database->fetchOne("SELECT COUNT(*) FROM repos WHERE language = 'Go' AND runtime_status = 'Modern (Go 1.27)'"));
            self::assertSame(1, $database->fetchOne("SELECT COUNT(*) FROM repos WHERE language = 'Go' AND runtime_status = 'DEFAULT'"));
            self::assertSame(150, $database->fetchOne('SELECT COUNT(*) FROM scanned_slices'));
            self::assertSame(0, $database->fetchOne("SELECT COUNT(*) FROM sqlite_master WHERE name = 'registry_cache'"));
            self::assertSame(0, $database->fetchOne("SELECT COUNT(*) FROM repos WHERE length(repo_name) != 64 OR repo_name GLOB '*[^0-9a-f]*'"));
            $csv = fopen($directory . '/languages.csv', 'rb');
            self::assertIsResource($csv);
            fgetcsv($csv, escape: '');
            while (($row = fgetcsv($csv, escape: '')) !== false) {
                self::assertCount(7, $row);
                self::assertSame((int) $row[1], $database->fetchOne('SELECT COUNT(*) FROM repos WHERE language = ?', [$row[0]]));
                self::assertSame((int) $row[5], $database->fetchOne('SELECT COUNT(*) FROM dependencies JOIN repos USING(repo_name) WHERE language = ?', [$row[0]]));
            }
            fclose($csv);
            $sql = file_get_contents($directory . '/verify.sql');
            self::assertNotFalse($sql);
            $sql = preg_replace('/^--.*$/m', '', $sql);
            self::assertNotNull($sql);
            foreach (explode(';', $sql) as $query) {
                if (trim($query) !== '') {
                    self::assertNotEmpty($database->fetchAllAssociative($query));
                }
            }
            $database->close();
        } finally {
            if (is_resource($destination)) {
                fclose($destination);
            }
            if (is_resource($compressed)) {
                gzclose($compressed);
            }
            unlink($temporary);
        }
    }

    public function testMigrationIsScopedAndRestoresOriginalSnapshots(): void
    {
        $connection = $this->connection();
        $original = $connection->fetchAllAssociative('SELECT * FROM blog_articles ORDER BY slug, locale');
        $migration = new Version20260917220000($connection, new NullLogger());
        $migration->up(new Schema());
        $this->execute($connection, $migration);
        foreach (Version20260917220000::corrections() as $slug => $correction) {
            $article = $connection->fetchAssociative('SELECT * FROM blog_articles WHERE slug = ? AND locale = ?', [$slug, 'en']);
            self::assertIsArray($article);
            self::assertSame($correction['title'], $article['title']);
            self::assertSame($correction['content_html'], $article['content_html']);
            self::assertSame('', $article['cta_path']);
            self::assertSame('[]', $article['how_to_steps']);
            self::assertSame('Original', $connection->fetchOne('SELECT title FROM blog_articles WHERE slug = ? AND locale = ?', [$slug, 'pl']));
        }
        self::assertSame('Original', $connection->fetchOne("SELECT title FROM blog_articles WHERE slug = 'unrelated'"));
        $rollback = new Version20260917220000($connection, new NullLogger());
        $rollback->down(new Schema());
        $this->execute($connection, $rollback);
        self::assertSame($original, $connection->fetchAllAssociative('SELECT * FROM blog_articles ORDER BY slug, locale'));
    }

    public function testRollbackRefusesToOverwriteLaterEditorialChanges(): void
    {
        $connection = $this->connection();
        $migration = new Version20260917220000($connection, new NullLogger());
        $migration->up(new Schema());
        $this->execute($connection, $migration);
        $connection->executeStatement("UPDATE blog_articles SET description = 'Later edit' WHERE locale = 'en'");
        $this->expectException(AbortMigration::class);
        (new Version20260917220000($connection, new NullLogger()))->down(new Schema());
    }

    public function testMissingArticlesAreNotCreated(): void
    {
        $connection = $this->connection();
        $connection->executeStatement("DELETE FROM blog_articles WHERE slug <> 'unrelated'");
        $migration = new Version20260917220000($connection, new NullLogger());
        $migration->up(new Schema());
        $this->execute($connection, $migration);
        self::assertSame(2, $connection->fetchOne('SELECT COUNT(*) FROM blog_articles'));
        $rollback = new Version20260917220000($connection, new NullLogger());
        $rollback->down(new Schema());
        $this->execute($connection, $rollback);
        self::assertSame(2, $connection->fetchOne('SELECT COUNT(*) FROM blog_articles'));
    }

    private function connection(): Connection
    {
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $connection->executeStatement('CREATE TABLE blog_articles (slug TEXT, locale TEXT, title TEXT, description TEXT, category TEXT, read_time_minutes INTEGER, updated_at TEXT, content_html TEXT, cta_label TEXT, cta_path TEXT, visual_lines TEXT, how_to_steps TEXT)');
        foreach ([...array_keys(Version20260917220000::corrections()), 'unrelated'] as $slug) {
            foreach (['en', 'pl'] as $locale) {
                $connection->insert('blog_articles', ['slug' => $slug, 'locale' => $locale, 'title' => 'Original', 'description' => 'Original description', 'category' => 'Original category', 'read_time_minutes' => 5, 'updated_at' => '2026-09-04', 'content_html' => '<p>Original body</p>', 'cta_label' => 'Original CTA', 'cta_path' => '/original', 'visual_lines' => '["Original"]', 'how_to_steps' => '[]']);
            }
        }
        return $connection;
    }

    private function execute(Connection $connection, Version20260917220000 $migration): void
    {
        foreach ($migration->getSql() as $query) {
            $connection->executeStatement($query->getStatement(), $query->getParameters(), $query->getTypes());
        }
    }
}
