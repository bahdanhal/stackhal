<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261002210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename the debugging essay while preserving its URL and editorial content.';
    }

    public function up(Schema $schema): void
    {
        $this->rename('I Think Debugging Is Almost Dead', 'Debugging Is Dead');
    }

    public function down(Schema $schema): void
    {
        $this->rename('Debugging Is Dead', 'I Think Debugging Is Almost Dead');
    }

    private function rename(string $oldTitle, string $newTitle): void
    {
        $this->addSql(
            'UPDATE blog_articles SET title = ?, updated_at = CURRENT_TIMESTAMP'
                . ' WHERE slug = ? AND locale = ? AND title = ?',
            [$newTitle, 'debugging-is-almost-dead-in-2026', 'en', $oldTitle]
        );
    }
}
