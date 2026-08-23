<?php

declare(strict_types=1);

namespace App\Mcp;

use App\AppLinks\Application\AppLinksService;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;

final readonly class AppLinksTools
{
    public function __construct(private AppLinksService $appLinksService)
    {
    }

    #[McpTool(
        name: 'validate_app_links',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Inspect and validate Apple App Site Association (apple-app-site-association) and Android Digital Asset Links (assetlinks.json) files, HTTPS hosting rules, and test URL path routing.'
    )]
    public function validateAppLinks(
        // phpcs:ignore Generic.Files.LineLength
        #[Schema(description: 'The target domain name (e.g. "example.com") hosting universal link and asset link files.')]
        string $domain,
        // phpcs:ignore Generic.Files.LineLength
        #[Schema(description: 'Optional web URL path to test against Apple AASA components and exclusion routing patterns.')]
        ?string $test_url = null,
    ): string {
        try {
            $result = $this->appLinksService->validateDomain(
                domain: $domain,
                testUrl: $test_url,
            );

            return $this->json([
                'status' => 'completed',
                'result' => $result->toArray(),
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function json(array $data): string
    {
        return json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }
}
