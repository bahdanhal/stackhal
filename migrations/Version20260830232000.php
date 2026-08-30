<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830232000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update Composer license article title to sentence case';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'UPDATE blog_articles SET title = ?, updated_at = ? WHERE slug = ?',
            [
                'I scanned 10,000 PHP packages and found hundreds of hidden license traps: here is why you could get sued',
                new \DateTimeImmutable('2026-08-30 23:20:00+00:00'),
                'composer-license-metadata-dependency-audit',
            ],
            [
                Types::STRING,
                Types::DATETIMETZ_IMMUTABLE,
                Types::STRING,
            ]
        );
    }

    public function down(Schema $schema): void
    {
    }
}
