<?php

declare(strict_types=1);

namespace App\Tests\AppLinks;

use App\AppLinks\Application\AppLinksService;
use App\Shared\Application\HttpFetcher;
use PHPUnit\Framework\TestCase;

final class AppLinksServiceCdnTest extends TestCase
{
    private const string CDN_URL = 'https://app-site-association.cdn-apple.com/a/v1/example.com';
    private const string ORIGIN_URL = 'https://example.com/.well-known/apple-app-site-association';
    private const string WWW_URL = 'https://www.example.com/.well-known/apple-app-site-association';
    private const string AASA = '{"applinks":{"details":[{"appIDs":["ABCDE12345.com.example.app"],"components":[{"/":"/products/*"}]}]}}';
    // phpcs:ignore Generic.Files.LineLength
    private const string ASSET_LINKS = '[{"relation":["delegate_permission/common.handle_all_urls"],"target":{"namespace":"android_app","package_name":"com.example.app","sha256_cert_fingerprints":["14:6D:E9:DE:0F:45:79:F6:10:5A:12:60:2B:93:FC:7F:16:17:D6:31:02:61:00:EC:4F:60:9E:78:21:C6:0F:C0"]}}]';

    public function testFlagsDomainWhoseOriginIsValidButAppleCdnCannotFetch(): void
    {
        $requested = $this->requestLog();
        $fetcher = $this->fetcher([
            self::ORIGIN_URL => [200, self::AASA, []],
            self::CDN_URL => [404, 'Not Found', [
                'apple-failure-reason' => ['SWCERR00401 Bad JSON content'],
                'cache-control' => ['max-age=3600,public'],
            ]],
        ], $requested);

        $result = (new AppLinksService(httpFetcher: $fetcher))
            ->validateDomain('example.com', 'https://example.com/products/shoes');

        self::assertFalse($result->isValid);
        self::assertTrue($result->aasaValid);
        self::assertContains('ERR_AASA_CDN_FETCH_FAILED', $result->getErrorCodes());
        self::assertSame('fetch_failed', $result->toArray()['apple_cdn']['state']);
        self::assertSame('SWCERR00401 Bad JSON content', $result->toArray()['apple_cdn']['failure_reason']);
        self::assertContains(self::CDN_URL, $requested->getArrayCopy());
    }

    public function testKeepsDomainValidWhenAppleCdnMatchesOrigin(): void
    {
        $fetcher = $this->fetcher([
            self::ORIGIN_URL => [200, self::AASA, []],
            self::CDN_URL => [200, self::AASA, []],
        ], $this->requestLog());

        $result = (new AppLinksService(httpFetcher: $fetcher))->validateDomain('Example.com/');

        self::assertTrue($result->isValid);
        self::assertContains('INFO_AASA_CDN_IN_SYNC', $result->getInfoCodes());
        self::assertSame('in_sync', $result->toArray()['apple_cdn']['state']);
        self::assertSame(self::ORIGIN_URL, $result->toArray()['apple_cdn']['compared_with']);
    }

    public function testComparesAgainstTheUrlAppleFollowedWhenTheWellKnownUrlRedirects(): void
    {
        $fetcher = $this->fetcher([
            self::ORIGIN_URL => [301, '', ['location' => [self::WWW_URL]]],
            self::WWW_URL => [200, self::AASA, []],
            self::CDN_URL => [200, self::AASA, ['apple-from' => [self::ORIGIN_URL . ', ' . self::WWW_URL]]],
        ], $this->requestLog());

        $result = (new AppLinksService(httpFetcher: $fetcher))->validateDomain('example.com');

        self::assertSame('in_sync', $result->toArray()['apple_cdn']['state']);
        self::assertSame(self::WWW_URL, $result->toArray()['apple_cdn']['compared_with']);
    }

    public function testWarnsWhenOnlyAppleCdnHasACopy(): void
    {
        $fetcher = $this->fetcher([
            self::ORIGIN_URL => [404, 'Not Found', []],
            self::CDN_URL => [200, self::AASA, ['apple-from' => [self::ORIGIN_URL]]],
        ], $this->requestLog());

        $result = (new AppLinksService(httpFetcher: $fetcher))->validateDomain('example.com');

        self::assertSame('cdn_only', $result->toArray()['apple_cdn']['state']);
        self::assertContains('WARN_AASA_CDN_ORIGIN_MISMATCH', $result->getWarningCodes());
    }

    public function testSkipsAppleCdnForInputThatIsNotAPlainHostname(): void
    {
        $requested = $this->requestLog();
        $fetcher = $this->fetcher([], $requested);

        $result = (new AppLinksService(httpFetcher: $fetcher))->validateDomain('example.com/../admin');

        self::assertNull($result->appleCdn);
        self::assertSame([], array_values(array_filter(
            $requested->getArrayCopy(),
            static fn (string $url): bool => str_contains($url, 'cdn-apple.com'),
        )));
    }

    /**
     * @return \ArrayObject<int, string>
     */
    private function requestLog(): \ArrayObject
    {
        /** @var \ArrayObject<int, string> $log */
        $log = new \ArrayObject();

        return $log;
    }

    /**
     * @param array<string, array{int, string, array<string, list<string>>}> $responses keyed by exact URL
     * @param \ArrayObject<int, string> $requested
     */
    private function fetcher(array $responses, \ArrayObject $requested): HttpFetcher
    {
        return new class ($responses, $requested, self::ASSET_LINKS) implements HttpFetcher {
            /**
             * @param array<string, array{int, string, array<string, list<string>>}> $responses
             * @param \ArrayObject<int, string> $requested
             */
            public function __construct(
                private readonly array $responses,
                private readonly \ArrayObject $requested,
                private readonly string $assetLinks,
            ) {
            }

            public function fetch(string $url, int $maxRedirects = 8): array
            {
                $this->requested->append($url);
                [$status, $body, $headers] = $this->responses[$url]
                    ?? (str_ends_with($url, '/assetlinks.json') ? [200, $this->assetLinks, []] : [404, '', []]);

                return [
                    'requested_url' => $url,
                    'final_url' => $url,
                    'status' => $status,
                    'headers' => $headers,
                    'body' => $body,
                    'content_type' => 'application/json',
                    'duration_ms' => 1,
                    'redirects' => [],
                    'error' => null,
                ];
            }

            public function fetchMany(array $urls, int $maxRedirects = 8): array
            {
                $results = [];
                foreach ($urls as $url) {
                    $results[$url] = $this->fetch($url, $maxRedirects);
                }

                return $results;
            }

            public function resolveUrl(string $base, string $reference): string
            {
                return $reference;
            }
        };
    }
}
