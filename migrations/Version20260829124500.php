<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260829124500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Link the Azure Storage proxy finding to the existing httpoxy advisory';
    }

    public function up(Schema $schema): void
    {
        $originalHtml = <<<'HTML'
<p>The uppercase variable is important. Under CGI-style environments, incoming HTTP headers are mapped into environment variables. An attacker-supplied request header named <code>Proxy</code> can become <code>HTTP_PROXY</code>. This collision is the vulnerability class known as <a href="https://httpoxy.org/">httpoxy</a>.</p>
HTML;
        $updatedHtml = $originalHtml . <<<'HTML'

<p>This vulnerability class is tracked as <a href="https://github.com/advisories/GHSA-m6ch-gg5f-wxx3">CVE-2016-5385 / GHSA-m6ch-gg5f-wxx3</a>. The advisory explicitly describes applications that call <code>getenv('HTTP_PROXY')</code>, but its affected Composer packages currently do not include <code>microsoft/azure-storage-blob</code> or <code>microsoft/azure-storage-common</code>. The Azure client added this behaviour after the original CVE was published.</p>
HTML;

        $this->replaceContent($originalHtml, $updatedHtml, new \DateTimeImmutable('2026-08-29 12:45:00+00:00'));
    }

    public function down(Schema $schema): void
    {
        $originalHtml = <<<'HTML'
<p>The uppercase variable is important. Under CGI-style environments, incoming HTTP headers are mapped into environment variables. An attacker-supplied request header named <code>Proxy</code> can become <code>HTTP_PROXY</code>. This collision is the vulnerability class known as <a href="https://httpoxy.org/">httpoxy</a>.</p>
HTML;
        $updatedHtml = $originalHtml . <<<'HTML'

<p>This vulnerability class is tracked as <a href="https://github.com/advisories/GHSA-m6ch-gg5f-wxx3">CVE-2016-5385 / GHSA-m6ch-gg5f-wxx3</a>. The advisory explicitly describes applications that call <code>getenv('HTTP_PROXY')</code>, but its affected Composer packages currently do not include <code>microsoft/azure-storage-blob</code> or <code>microsoft/azure-storage-common</code>. The Azure client added this behaviour after the original CVE was published.</p>
HTML;

        $this->replaceContent($updatedHtml, $originalHtml, new \DateTimeImmutable('2026-08-29 12:00:00+00:00'));
    }

    private function replaceContent(string $search, string $replacement, \DateTimeImmutable $updatedAt): void
    {
        $this->addSql(
            'UPDATE blog_articles SET content_html = REPLACE(content_html, ?, ?), updated_at = ? WHERE slug = ? AND content_html LIKE ?',
            [
                $search,
                $replacement,
                $updatedAt,
                'retired-azure-sdk-for-php-migration',
                '%' . $search . '%',
            ],
            [
                Types::TEXT,
                Types::TEXT,
                Types::DATETIMETZ_IMMUTABLE,
                Types::STRING,
                Types::TEXT,
            ]
        );
    }
}

// phpcs:enable Generic.Files.LineLength.TooLong
