<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'blog_articles')]
#[ORM\UniqueConstraint(name: 'uniq_blog_articles_locale_slug', columns: ['locale', 'slug'])]
#[ORM\Index(columns: ['locale', 'published_at'], name: 'idx_blog_articles_locale_published')]
class BlogArticleEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 5, options: ['default' => 'en'])]
    private string $locale;

    #[ORM\Column(type: Types::STRING, length: 160)]
    private string $slug;

    #[ORM\Column(type: Types::STRING, length: 160, options: ['default' => ''])]
    private string $alternateSlug;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::STRING, length: 80)]
    private string $category;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $readTimeMinutes;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $publishedAt;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: Types::TEXT)]
    private string $contentHtml;

    #[ORM\Column(type: Types::STRING, length: 160)]
    private string $ctaLabel;

    #[ORM\Column(type: Types::STRING, length: 160)]
    private string $ctaPath;

    #[ORM\Column(type: Types::STRING, length: 80)]
    private string $visualClass;

    /** @var list<string> */
    #[ORM\Column(name: 'visual_lines', type: Types::JSON)]
    private array $visualLinesData = [];

    /** @var list<array{name: string, text: string}> */
    #[ORM\Column(name: 'how_to_steps', type: Types::JSON)]
    private array $howToStepsData = [];

    /**
     * @param list<string> $visualLines
     * @param list<array{name: string, text: string}> $howToSteps
     */
    public function __construct(
        string $slug,
        string $title,
        string $description,
        string $category,
        int $readTimeMinutes,
        \DateTimeImmutable $publishedAt,
        \DateTimeImmutable $updatedAt,
        string $contentHtml,
        string $ctaLabel,
        string $ctaPath,
        string $visualClass,
        array $visualLines,
        array $howToSteps,
        string $locale = 'en',
        string $alternateSlug = ''
    ) {
        $this->locale = $locale;
        $this->slug = $slug;
        $this->alternateSlug = $alternateSlug;
        $this->title = $title;
        $this->description = $description;
        $this->category = $category;
        $this->readTimeMinutes = $readTimeMinutes;
        $this->publishedAt = $publishedAt;
        $this->updatedAt = $updatedAt;
        $this->contentHtml = $contentHtml;
        $this->ctaLabel = $ctaLabel;
        $this->ctaPath = $ctaPath;
        $this->visualClass = $visualClass;
        $this->visualLinesData = $visualLines;
        $this->howToStepsData = $howToSteps;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getAlternateSlug(): string
    {
        return $this->alternateSlug;
    }

    public function setAlternateSlug(string $alternateSlug): void
    {
        $this->alternateSlug = $alternateSlug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getReadTimeMinutes(): int
    {
        return $this->readTimeMinutes;
    }

    public function setReadTimeMinutes(int $readTimeMinutes): void
    {
        $this->readTimeMinutes = $readTimeMinutes;
    }

    public function getPublishedAt(): \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getContentHtml(): string
    {
        return $this->contentHtml;
    }

    public function setContentHtml(string $contentHtml): void
    {
        $this->contentHtml = $contentHtml;
    }

    public function getCtaLabel(): string
    {
        return $this->ctaLabel;
    }

    public function setCtaLabel(string $ctaLabel): void
    {
        $this->ctaLabel = $ctaLabel;
    }

    public function getCtaPath(): string
    {
        return $this->ctaPath;
    }

    public function setCtaPath(string $ctaPath): void
    {
        $this->ctaPath = $ctaPath;
    }

    public function getVisualClass(): string
    {
        return $this->visualClass;
    }

    public function setVisualClass(string $visualClass): void
    {
        $this->visualClass = $visualClass;
    }

    /** @return list<string> */
    public function getVisualLines(): array
    {
        return $this->visualLinesData;
    }

    /** @param list<string> $visualLines */
    public function setVisualLines(array $visualLines): void
    {
        $this->visualLinesData = $visualLines;
    }

    /** @return list<array{name: string, text: string}> */
    public function getHowToSteps(): array
    {
        return $this->howToStepsData;
    }

    /** @param list<array{name: string, text: string}> $howToSteps */
    public function setHowToSteps(array $howToSteps): void
    {
        $this->howToStepsData = $howToSteps;
    }
}
