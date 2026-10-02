<?php

declare(strict_types=1);

namespace App\Blog\Presentation;

use App\Blog\Domain\BlogArticle;

final class ArticleIllustrations
{
    /** @var array<string, array{file: string, alt: string, caption: string}> */
    private const ARTWORKS = [
        'debugging-is-almost-dead-in-2026' => [
            'file' => 'debugging-evidence',
            'alt' => 'An inspection lens examining a stack of red and black evidence plates.',
            'caption' => 'Follow the evidence.',
        ],
        'ai-coding-agents-deprecated-dependencies-census' => [
            'file' => 'dependency-census',
            'alt' => 'A mechanical sorter inspecting old and new dependency cartridges.',
            'caption' => 'Inspect the dependencies.',
        ],
        'bmad-vs-gsd-ai-agent-frameworks-benchmark' => [
            'file' => 'agent-workflows',
            'alt' => 'An engineering balance weighing connected role modules against a blueprint and assembled machine.',
            'caption' => 'Weigh the tradeoffs.',
        ],
        'retired-azure-sdk-for-php-migration' => [
            'file' => 'retired-sdk',
            'alt' => 'A cloud terminal connected to a retired adapter with an exposed connector.',
            'caption' => 'Check the connection.',
        ],
        'pkpass-signature-errors' => [
            'file' => 'wallet-signature',
            'alt' => 'A signing press verifying a stack of boarding passes with a red cryptographic seal.',
            'caption' => 'Verify the signature.',
        ],
        'dns-delegation-explained' => [
            'file' => 'dns-delegation',
            'alt' => 'A branching network of mechanical hubs with one delegation path highlighted in red.',
            'caption' => 'Trace the delegation.',
        ],
        'nginx-to-caddy' => [
            'file' => 'proxy-migration',
            'alt' => 'A routing junction turning several black pipes into one clear red connection.',
            'caption' => 'Make the route clear.',
        ],
        'bimi-not-working' => [
            'file' => 'email-authentication',
            'alt' => 'An envelope passing through a mechanical authentication gate and receiving a red identity seal.',
            'caption' => 'Authenticate the message.',
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
