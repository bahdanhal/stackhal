<?php

declare(strict_types=1);

namespace App\Mcp;

use App\Cors\Application\CorsSandboxService;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;

final readonly class CorsTools
{
    public function __construct(private CorsSandboxService $corsService)
    {
    }

    /**
     * @param array<string, mixed>|list<string> $response_headers
     */
    #[McpTool(
        name: 'diagnose_cors_policy',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Analyze CORS HTTP request and response headers for wildcard/credential security violations, missing Vary: Origin, preflight OPTIONS handling, and header exposure.'
    )]
    public function diagnoseCorsPolicy(
        #[Schema(description: 'The incoming HTTP request Origin header (e.g. "https://app.example.com").')]
        string $request_origin,
        // phpcs:ignore Generic.Files.LineLength
        #[Schema(description: 'The server HTTP response headers (as key-value map or list of "Header: Value" strings).')]
        array $response_headers,
        #[Schema(description: 'Whether the cross-origin request includes cookies or authorization credentials.')]
        ?bool $with_credentials = false,
    ): string {
        try {
            $result = $this->corsService->analyze(
                requestOrigin: $request_origin,
                responseHeaders: $response_headers,
                withCredentials: (bool) $with_credentials,
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
