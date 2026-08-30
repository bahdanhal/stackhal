<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260831020000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Delete composer license metadata dependency audit article';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'DELETE FROM blog_articles WHERE slug = ? OR alternate_slug = ?',
            [
                'composer-license-metadata-dependency-audit',
                'composer-license-metadata-dependency-audit',
            ]
        );
    }

    public function down(Schema $schema): void
    {
    }
}
