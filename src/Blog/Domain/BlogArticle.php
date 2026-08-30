<?php

declare(strict_types=1);

namespace App\Blog\Domain;

final readonly class BlogArticle
{
    /**
     * @param list<string> $visualLines
     * @param list<array{name: string, text: string}> $howToSteps
     */
    public function __construct(
        private string $slug,
        private string $title,
        private string $description,
        private string $category,
        private int $readTimeMinutes,
        private \DateTimeImmutable $publishedAt,
        private \DateTimeImmutable $updatedAt,
        private string $contentHtml,
        private string $ctaLabel,
        private string $ctaPath,
        private string $visualClass,
        /** @var list<string> */
        private array $visualLines,
        /** @var list<array{name: string, text: string}> */
        private array $howToSteps,
        private string $locale = 'en',
        private string $alternateSlug = '',
        private ?int $id = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getAlternateSlug(): string
    {
        return $this->alternateSlug;
    }

    public function getSlug(): string
    {
        return $this->slug;
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
        return $this->visualLines;
    }

    /** @return list<array{name: string, text: string}> */
    public function getHowToSteps(): array
    {
        return $this->howToSteps;
    }
}
