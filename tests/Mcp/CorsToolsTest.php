<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\Cors\Application\CorsSandboxService;
use App\Mcp\CorsTools;
use PHPUnit\Framework\TestCase;

final class CorsToolsTest extends TestCase
{
    private CorsTools $tools;

    protected function setUp(): void
    {
        $this->tools = new CorsTools(new CorsSandboxService());
    }

    public function testDiagnoseCorsPolicyToolSuccess(): void
    {
        $response = $this->tools->diagnoseCorsPolicy(
            request_origin: 'https://app.example.com',
            response_headers: [
                'Access-Control-Allow-Origin' => 'https://app.example.com',
                'Access-Control-Allow-Credentials' => 'true',
                'Vary' => 'Origin',
            ],
            with_credentials: true
        );

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertTrue($data['result']['is_valid']);
        self::assertNotEmpty($data['result']['diagnostics']);
    }

    public function testDiagnoseCorsPolicyToolWildcardError(): void
    {
        $response = $this->tools->diagnoseCorsPolicy(
            request_origin: 'https://app.example.com',
            response_headers: [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Credentials' => 'true',
            ],
            with_credentials: true
        );

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertFalse($data['result']['is_valid']);
        self::assertNotEmpty($data['result']['diagnostics']);
    }
}
