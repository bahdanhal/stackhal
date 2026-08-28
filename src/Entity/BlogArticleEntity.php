<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'blog_articles')]
#[ORM\UniqueConstraint(name: 'uniq_blog_articles_slug', columns: ['slug'])]
#[ORM\Index(columns: ['published_at'], name: 'idx_blog_articles_published_at')]
class BlogArticleEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 160)]
    private string $slug;

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
        array $howToSteps
    ) {
        $this->slug = $slug;
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getReadTimeMinutes(): int
    {
        return $this->readTimeMinutes;
    }

    public function getPublishedAt(): \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getContentHtml(): string
    {
        return $this->contentHtml;
    }

    public function getCtaLabel(): string
    {
        return $this->ctaLabel;
    }

    public function getCtaPath(): string
    {
        return $this->ctaPath;
    }

    public function getVisualClass(): string
    {
        return $this->visualClass;
    }

    /** @return list<string> */
    public function getVisualLines(): array
    {
        return $this->visualLinesData;
    }

    /** @return list<array{name: string, text: string}> */
    public function getHowToSteps(): array
    {
        return $this->howToStepsData;
    }
}
