<?php

declare(strict_types=1);

namespace App\Shared\Application;

/**
 * @phpstan-type FetchResult array{
 *     requested_url: string,
 *     final_url: string,
 *     status: int,
 *     headers: array<string, list<string>>,
 *     body: string,
 *     content_type: string,
 *     duration_ms: int,
 *     redirects: list<array{url: string, status: int, location: ?string}>,
 *     error: ?string
 * }
 */
interface HttpFetcher
{
    /** @phpstan-return FetchResult */
    public function fetch(string $url, int $maxRedirects = 8): array;

    /**
     * @param list<string> $urls
     * @phpstan-return array<string, FetchResult>
     */
    public function fetchMany(array $urls, int $maxRedirects = 8): array;

    public function resolveUrl(string $base, string $reference): string;
}
