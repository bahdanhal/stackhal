<?php

declare(strict_types=1);

namespace App\Tests\Cors;

use App\Cors\Domain\Engine\CorsAnalyzer;
use PHPUnit\Framework\TestCase;

final class CorsAnalyzerTest extends TestCase
{
    private CorsAnalyzer $analyzer;

    protected function setUp(): void
    {
        $this->analyzer = new CorsAnalyzer();
    }

    public function testWildcardOriginWithCredentialsFails(): void
    {
        $result = $this->analyzer->analyze(
            requestOrigin: 'https://app.example.com',
            responseHeaders: [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Credentials' => 'true',
            ],
            withCredentials: true
        );

        self::assertFalse($result->isValid);
        self::assertContains('ERR_CORS_WILDCARD_WITH_CREDENTIALS', $result->getErrorCodes());
    }

    public function testMissingVaryOriginWarningWhenReflectingOrigin(): void
    {
        $result = $this->analyzer->analyze(
            requestOrigin: 'https://app.example.com',
            responseHeaders: [
                'Access-Control-Allow-Origin' => 'https://app.example.com',
                'Access-Control-Allow-Credentials' => 'true',
            ],
            withCredentials: true
        );

        self::assertTrue($result->isValid);
        self::assertContains('WARN_CORS_MISSING_VARY_ORIGIN', $result->getWarningCodes());
    }

    public function testValidPreflightWithCustomHeaders(): void
    {
        $result = $this->analyzer->analyze(
            requestOrigin: 'https://app.example.com',
            responseHeaders: [
                'Access-Control-Allow-Origin' => 'https://app.example.com',
                'Access-Control-Allow-Methods' => 'GET, POST, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'X-Custom-Auth, Content-Type',
                'Vary' => 'Origin',
            ],
            requestMethod: 'DELETE',
            requestHeaders: [
                'Access-Control-Request-Headers' => 'X-Custom-Auth',
            ]
        );

        self::assertTrue($result->isValid);
        self::assertEmpty($result->getErrorCodes());
        self::assertContains('INFO_CORS_PREFLIGHT_OK', $result->getInfoCodes());
    }

    public function testOriginMismatch(): void
    {
        $result = $this->analyzer->analyze(
            requestOrigin: 'https://attacker.com',
            responseHeaders: [
                'Access-Control-Allow-Origin' => 'https://legit.com',
            ]
        );

        self::assertFalse($result->isValid);
        self::assertContains('ERR_CORS_ORIGIN_MISMATCH', $result->getErrorCodes());
    }

    public function testMethodDisallowed(): void
    {
        $result = $this->analyzer->analyze(
            requestOrigin: 'https://app.example.com',
            responseHeaders: [
                'Access-Control-Allow-Origin' => 'https://app.example.com',
                'Access-Control-Allow-Methods' => 'GET, POST',
                'Vary' => 'Origin',
            ],
            requestMethod: 'DELETE'
        );

        self::assertFalse($result->isValid);
        self::assertContains('ERR_CORS_METHOD_DISALLOWED', $result->getErrorCodes());
    }
}
