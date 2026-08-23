<?php

declare(strict_types=1);

namespace App\Cors\Application;

use App\Cors\Domain\Engine\CorsAnalyzer;
use App\Cors\Domain\Model\CorsAnalysisResult;

final readonly class CorsSandboxService
{
    private CorsAnalyzer $analyzer;

    public function __construct(?CorsAnalyzer $analyzer = null)
    {
        $this->analyzer = $analyzer ?? new CorsAnalyzer();
    }

    /**
     * @param array<string, mixed>|list<string> $responseHeaders
     * @param array<string, mixed>|list<string> $requestHeaders
     */
    public function analyze(
        string $requestOrigin,
        array $responseHeaders,
        bool $withCredentials = false,
        ?string $requestMethod = null,
        array $requestHeaders = [],
    ): CorsAnalysisResult {
        return $this->analyzer->analyze(
            requestOrigin: $requestOrigin,
            responseHeaders: $responseHeaders,
            withCredentials: $withCredentials,
            requestMethod: $requestMethod,
            requestHeaders: $requestHeaders,
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getPresets(): array
    {
        return [
            [
                'id' => 'authenticated_api_credentials',
                'name' => 'Authenticated Cookie/Session API',
                'request' => [
                    'origin' => 'https://app.example.com',
                    'method' => 'POST',
                    'with_credentials' => true,
                ],
                'expected_response_headers' => [
                    'Access-Control-Allow-Origin' => 'https://app.example.com',
                    'Access-Control-Allow-Credentials' => 'true',
                    'Vary' => 'Origin',
                ],
                'description' => 'Frontend SPA communicating with backend API using cookies or session tokens.',
            ],
            [
                'id' => 'public_static_assets_cdn',
                'name' => 'Public Static Web Fonts / Assets CDN',
                'request' => [
                    'origin' => 'https://example.org',
                    'method' => 'GET',
                    'with_credentials' => false,
                ],
                'expected_response_headers' => [
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                ],
                // phpcs:ignore Generic.Files.LineLength
                'description' => 'Public CDN assets (fonts, images, WebAssembly) accessible from any origin without credentials.',
            ],
        ];
    }
}
