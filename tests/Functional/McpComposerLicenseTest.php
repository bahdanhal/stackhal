<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\ComposerLicense\Application\ComposerLicenseService;
use App\Mcp\ComposerLicenseTools;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class McpComposerLicenseTest extends TestCase
{
    public function testMcpAuditPackage(): void
    {
        $tools = new ComposerLicenseTools($this->serviceWithCleanPackage());
        /** @var array<string, mixed> $data */
        $data = json_decode($tools->auditPackageLicense('symfony/http-foundation'), true);

        self::assertSame('completed', $data['status']);
        self::assertSame('NO_COPYLEFT_RISK_SIGNAL', $data['result']['verdict']);
    }

    public function testMcpAuditsExactLockfile(): void
    {
        $tools = new ComposerLicenseTools($this->serviceWithCleanPackage());
        $lock = json_encode(['packages' => [[
            'name' => 'symfony/http-foundation',
            'version' => 'v7.2.0',
            'license' => ['MIT'],
            'require' => [],
        ]]], JSON_THROW_ON_ERROR);
        /** @var array<string, mixed> $data */
        $data = json_decode($tools->auditLockfile($lock), true);

        self::assertSame('completed', $data['status']);
        self::assertSame('locked_versions', $data['result']['audit_mode']);
    }

    private function serviceWithCleanPackage(): ComposerLicenseService
    {
        $metadata = json_encode(['packages' => ['symfony/http-foundation' => [[
            'version' => 'v7.3.0',
            'version_normalized' => '7.3.0.0',
            'license' => ['MIT'],
            'require' => [],
        ]]]], JSON_THROW_ON_ERROR);

        return new ComposerLicenseService(new MockHttpClient(new MockResponse($metadata, [
            'http_code' => 200,
            'response_headers' => ['content-type: application/json'],
        ])));
    }
}
