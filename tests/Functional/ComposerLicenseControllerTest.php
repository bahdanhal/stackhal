<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\ComposerLicense\Application\ComposerLicenseService;
use App\ComposerLicense\Presentation\Http\ComposerLicenseController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ComposerLicenseControllerTest extends TestCase
{
    private ComposerLicenseService $service;

    protected function setUp(): void
    {
        $metadata = json_encode(['packages' => ['symfony/http-foundation' => [[
            'version' => 'v7.3.0',
            'version_normalized' => '7.3.0.0',
            'license' => ['MIT'],
            'require' => [],
        ]]]], JSON_THROW_ON_ERROR);
        $this->service = new ComposerLicenseService(new MockHttpClient(new MockResponse($metadata, [
            'http_code' => 200,
            'response_headers' => ['content-type: application/json'],
        ])));
    }

    public function testApiAuditPackageBadRequest(): void
    {
        $controller = new ComposerLicenseController($this->service);
        $request = $this->jsonRequest(['package' => 'invalid-no-vendor']);

        self::assertSame(Response::HTTP_BAD_REQUEST, $controller->auditPackage($request)->getStatusCode());
    }

    public function testApiAuditPackageSuccess(): void
    {
        $controller = new ComposerLicenseController($this->service);
        $response = $controller->auditPackage($this->jsonRequest(['package' => 'symfony/http-foundation']));
        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('success', $data['status']);
        self::assertSame('NO_COPYLEFT_RISK_SIGNAL', $data['result']['verdict']);
    }

    public function testApiAuditsExactLockfile(): void
    {
        $controller = new ComposerLicenseController($this->service);
        $lock = json_encode(['packages' => [[
            'name' => 'symfony/http-foundation',
            'version' => 'v7.2.0',
            'license' => ['MIT'],
            'require' => [],
        ]], 'packages-dev' => []], JSON_THROW_ON_ERROR);
        $response = $controller->auditLockfile($this->jsonRequest(['content' => $lock]));
        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('locked_versions', $data['result']['audit_mode']);
        self::assertSame('v7.2.0', $data['result']['production_packages'][0]['version']);
    }

    public function testApiRejectsUnknownJsonShape(): void
    {
        $controller = new ComposerLicenseController($this->service);
        $response = $controller->auditLockfile($this->jsonRequest([
            'content' => '{"name":"example/project"}',
        ]));

        self::assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    /** @param array<string, mixed> $payload */
    private function jsonRequest(array $payload): Request
    {
        return new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );
    }
}
