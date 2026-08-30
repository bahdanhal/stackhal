<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add locale and alternate_slug to blog_articles for bilingual blog management';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE blog_articles ADD COLUMN IF NOT EXISTS locale VARCHAR(5) NOT NULL DEFAULT 'en'");
        $this->addSql("ALTER TABLE blog_articles ADD COLUMN IF NOT EXISTS alternate_slug VARCHAR(160) NOT NULL DEFAULT ''");
        $this->addSql('DROP INDEX IF EXISTS uniq_blog_articles_slug');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_blog_articles_locale_slug ON blog_articles (locale, slug)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_blog_articles_locale_published ON blog_articles (locale, published_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS idx_blog_articles_locale_published');
        $this->addSql('DROP INDEX IF EXISTS uniq_blog_articles_locale_slug');
        $this->addSql('CREATE UNIQUE INDEX uniq_blog_articles_slug ON blog_articles (slug)');
        $this->addSql('ALTER TABLE blog_articles DROP COLUMN IF EXISTS alternate_slug');
        $this->addSql('ALTER TABLE blog_articles DROP COLUMN IF EXISTS locale');
    }
}
