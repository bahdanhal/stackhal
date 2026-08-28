<?php

declare(strict_types=1);

namespace App\Blog\Application;

use App\Blog\Domain\BlogArticle;

interface BlogArticleRepository
{
    /** @return list<BlogArticle> */
    public function findPublished(): array;

    public function findPublishedBySlug(string $slug): ?BlogArticle;
}
