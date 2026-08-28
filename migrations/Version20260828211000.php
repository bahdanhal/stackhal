<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260828211000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Normalize JSON values seeded by the initial blog migration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE blog_articles SET visual_lines = (visual_lines #>> '{}')::json WHERE json_typeof(visual_lines) = 'string'");
        $this->addSql("UPDATE blog_articles SET how_to_steps = (how_to_steps #>> '{}')::json WHERE json_typeof(how_to_steps) = 'string'");
    }

    public function down(Schema $schema): void
    {
    }
}

// phpcs:enable Generic.Files.LineLength.TooLong
