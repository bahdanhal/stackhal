<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261013120000 extends AbstractMigration
{
    private const OLD_SLUG = 'debugging-is-almost-dead-in-2026';
    private const NEW_SLUG = 'debugging-is-dead';

    public function getDescription(): string
    {
        return 'Move the debugging essay to a URL that matches its title.';
    }

    public function up(Schema $schema): void
    {
        $this->move(self::OLD_SLUG, self::NEW_SLUG);
    }

    public function down(Schema $schema): void
    {
        $this->move(self::NEW_SLUG, self::OLD_SLUG);
    }

    private function move(string $from, string $to): void
    {
        $this->addSql(
            'UPDATE blog_articles SET slug = ? WHERE slug = ? AND locale = ?'
                . ' AND NOT EXISTS (SELECT 1 FROM (SELECT slug, locale FROM blog_articles) existing'
                . ' WHERE existing.slug = ? AND existing.locale = ?)',
            [$to, $from, 'en', $to, 'en']
        );
    }
}
