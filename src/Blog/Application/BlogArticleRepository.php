<?php

declare(strict_types=1);

namespace App\Blog\Application;

use App\Blog\Domain\BlogArticle;
use App\Entity\BlogArticleEntity;

interface BlogArticleRepository
{
    /** @return list<BlogArticle> */
    public function findPublished(?string $locale = null): array;

    public function findPublishedBySlug(string $slug, string $locale = 'en'): ?BlogArticle;

    /** @return list<BlogArticle> */
    public function findAllForAdmin(?string $locale = null): array;

    public function findEntity(int $id): ?BlogArticleEntity;

    public function findEntityBySlugAndLocale(string $slug, string $locale): ?BlogArticleEntity;

    public function save(BlogArticleEntity $entity): void;

    public function delete(BlogArticleEntity $entity): void;
}
