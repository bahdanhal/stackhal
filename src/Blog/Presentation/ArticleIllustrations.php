<?php

declare(strict_types=1);

namespace App\Blog\Presentation;

use App\Blog\Domain\BlogArticle;

final class ArticleIllustrations
{
    /** @var array<string, array{file: string, alt: string, caption: string}> */
    private const ARTWORKS = [
        'bmad-vs-gsd-ai-agent-frameworks-benchmark' => [
            'file' => 'agent-workflows',
            'alt' => 'An engineering balance weighing connected role modules against a blueprint and assembled machine.',
            'caption' => 'Same task, three setups.',
        ],
        'retired-azure-sdk-for-php-migration' => [
            'file' => 'retired-sdk',
            'alt' => 'A cloud terminal connected to a retired adapter with an exposed connector.',
            'caption' => 'Check the connection.',
        ],
    ];

    /** @return array{path: string, alt: string, caption: string}|null */
    public function forArticle(BlogArticle $article): ?array
    {
        $artwork = self::ARTWORKS[$article->getSlug()] ?? self::ARTWORKS[$article->getAlternateSlug()] ?? null;
        if ($artwork === null) {
            return null;
        }

        return [
            'path' => '/images/atelier/' . $artwork['file'] . '.webp',
            'alt' => $artwork['alt'],
            'caption' => $artwork['caption'],
        ];
    }
}
