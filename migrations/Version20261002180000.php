<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20261002180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Introduce the first bilingual edition of What agents are talking about';
    }

    public function up(Schema $schema): void
    {
        foreach (['en', 'pl'] as $locale) {
            $file = dirname(__DIR__) . '/resources/agent-conversations/2026-10-02.' . $locale . '.json';
            $content = file_get_contents($file);
            $this->abortIf($content === false, 'The first agent conversations edition is missing.');
            /** @var array<string, string|int> $edition */
            $edition = json_decode((string) $content, true, 512, JSON_THROW_ON_ERROR);
            $publishedAt = (new \DateTimeImmutable('2026-10-02T18:00:00+02:00'))
                ->setTimezone(new \DateTimeZone('UTC'));

            $this->addSql(
                'INSERT INTO blog_articles '
                . '(slug, title, description, category, read_time_minutes, published_at, updated_at, '
                . 'content_html, cta_label, cta_path, visual_class, visual_lines, how_to_steps, locale, alternate_slug) '
                . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON CONFLICT (locale, slug) DO NOTHING',
                [
                    $edition['slug'], $edition['title'], $edition['description'], $edition['category'],
                    $edition['read_time_minutes'], $publishedAt, $publishedAt, $edition['content_html'],
                    '', '', '', [], [], $locale, $edition['alternate_slug'],
                ],
                [
                    Types::STRING, Types::STRING, Types::TEXT, Types::STRING, Types::SMALLINT,
                    Types::DATETIMETZ_IMMUTABLE, Types::DATETIMETZ_IMMUTABLE, Types::TEXT,
                    Types::STRING, Types::STRING, Types::STRING, Types::JSON, Types::JSON,
                    Types::STRING, Types::STRING,
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        // Published editions and later editorial changes survive a rollback.
    }
}
