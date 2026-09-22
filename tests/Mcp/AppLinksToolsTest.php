<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\AppLinks\Application\AppLinksService;
use App\Mcp\AppLinksTools;
use App\Shared\Application\HttpFetcher;
use PHPUnit\Framework\TestCase;

final class AppLinksToolsTest extends TestCase
{
    private AppLinksTools $tools;

    protected function setUp(): void
    {
        $fetcher = new class () implements HttpFetcher {
            public function fetch(string $url, int $maxRedirects = 8): array
            {
                $isAasa = str_contains($url, 'apple-app-site-association');
                $body = $isAasa
                    ? <<<'JSON'
{"applinks":{"details":[{"appIDs":["ABCDE12345.com.example.app"],"components":[{"/":"/products/*"}]}]}}
JSON
                    : <<<'JSON'
[
  {
    "relation": ["delegate_permission/common.handle_all_urls"],
    "target": {
      "namespace": "android_app",
      "package_name": "com.example.app",
      "sha256_cert_fingerprints": [
        "14:6D:E9:DE:0F:45:79:F6:10:5A:12:60:2B:93:FC:7F:16:17:D6:31:02:61:00:EC:4F:60:9E:78:21:C6:0F:C0"
      ]
    }
  }
]
JSON;

                return [
                    'requested_url' => $url,
                    'final_url' => $url,
                    'status' => 200,
                    'headers' => ['content-type' => ['application/json']],
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
        $this->tools = new AppLinksTools(new AppLinksService(httpFetcher: $fetcher));
    }

    public function testValidateAppLinksToolSuccess(): void
    {
        $response = $this->tools->validateAppLinks('example.com', 'https://example.com/products/summer-sale');

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertTrue($data['result']['is_valid']);
        self::assertTrue($data['result']['opens_in_app']);
        self::assertSame('/products/*', $data['result']['matched_pattern']);
        self::assertNotEmpty($data['result']['diagnostics']);
    }

    public function testValidateAppLinksToolEmptyDomain(): void
    {
        $response = $this->tools->validateAppLinks('');

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertFalse($data['result']['is_valid']);
        self::assertContains('ERR_AASA_NOT_FOUND', array_column($data['result']['diagnostics'], 'code'));
    }
}
