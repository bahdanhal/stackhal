<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\ComposerLicense\Application\ComposerLicenseService;
use App\ComposerLicense\Domain\Model\LicenseClassification;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class ComposerLicenseServiceTest extends TestCase
{
    public function testClassifiesLicenseFamiliesWithoutTreatingLesserGplAsStrong(): void
    {
        $service = new ComposerLicenseService();

        self::assertSame(LicenseClassification::PERMISSIVE, $service->classifyLicenses(['MIT']));
        self::assertSame(LicenseClassification::STRONG_COPYLEFT, $service->classifyLicenses(['GPL-3.0-only']));
        self::assertSame(LicenseClassification::STRONG_COPYLEFT, $service->classifyLicenses(['OSL-3.0']));
        self::assertSame(LicenseClassification::WEAK_COPYLEFT, $service->classifyLicenses(['LGPL-3.0-or-later']));
        self::assertSame(LicenseClassification::WEAK_COPYLEFT, $service->classifyLicenses(['MPL-2.0']));
        self::assertSame(LicenseClassification::PROPRIETARY, $service->classifyLicenses(['proprietary']));
        self::assertSame(LicenseClassification::UNKNOWN, $service->classifyLicenses([]));
    }

    public function testAuditsExactLockedVersionsWithoutCallingPackagist(): void
    {
        $service = new ComposerLicenseService();
        $lock = json_encode([
            'packages' => [
                [
                    'name' => 'example/root',
                    'version' => '1.2.3',
                    'license' => ['MIT'],
                    'require' => ['example/dependency' => '^3.0'],
                ],
                [
                    'name' => 'example/dependency',
                    'version' => '3.1.7',
                    'license' => ['OSL-3.0'],
                    'require' => [],
                ],
            ],
            'packages-dev' => [],
        ], JSON_THROW_ON_ERROR);

        $result = $service->auditLockfileContent($lock);

        self::assertSame('locked_versions', $result->auditMode);
        self::assertSame('1.2.3', $result->prodResults[0]->version);
        self::assertTrue($result->prodResults[0]->requiresReview);
        self::assertSame('STRONG_COPYLEFT_REVIEW', $result->prodResults[0]->verdict);
        self::assertSame('3.1.7', $result->prodResults[0]->violations[0]->version);
    }

    public function testManifestUsesVersionConstraintAndLabelsEstimate(): void
    {
        $client = new MockHttpClient(function (string $method, string $url): MockResponse {
            if (str_ends_with($url, '/example/root.json')) {
                return $this->metadataResponse('example/root', [
                    $this->release('2.0.0', ['MIT']),
                    $this->release('1.5.0', ['MIT'], ['example/dependency' => '^3.0']),
                ]);
            }

            return $this->metadataResponse('example/dependency', [
                $this->release('3.1.7', ['OSL-3.0']),
            ]);
        });
        $service = new ComposerLicenseService($client);
        $manifest = json_encode(['require' => ['example/root' => '^1.0']], JSON_THROW_ON_ERROR);

        $result = $service->auditLockfileContent($manifest);

        self::assertSame('manifest_estimate', $result->auditMode);
        self::assertSame('1.5.0', $result->prodResults[0]->version);
        self::assertSame('STRONG_COPYLEFT_REVIEW', $result->prodResults[0]->verdict);
    }

    public function testDirectCopyleftPackageRequiresReviewWithoutDependencies(): void
    {
        $service = new ComposerLicenseService();
        $lock = json_encode([
            'packages' => [[
                'name' => 'example/copyleft-library',
                'version' => '3.1.7',
                'license' => ['OSL-3.0'],
                'require' => [],
            ]],
            'packages-dev' => [],
        ], JSON_THROW_ON_ERROR);

        $result = $service->auditLockfileContent($lock);

        self::assertTrue($result->prodResults[0]->requiresReview);
        self::assertSame('COPYLEFT_LICENSE_REVIEW', $result->prodResults[0]->verdict);
    }

    public function testUnresolvableRootPackageFailsInsteadOfReturningClean(): void
    {
        $service = new ComposerLicenseService(new MockHttpClient(new MockResponse('', ['http_code' => 404])));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('HTTP 404');
        $service->auditPackage('missing/package');
    }

    public function testUnresolvableDependencyMarksGraphIncomplete(): void
    {
        $client = new MockHttpClient([
            $this->metadataResponse('example/root', [
                $this->release('1.0.0', ['MIT'], ['missing/dependency' => '^1.0']),
            ]),
            new MockResponse('', ['http_code' => 404]),
        ]);

        $result = (new ComposerLicenseService($client))->auditPackage('example/root');

        self::assertFalse($result->isComplete);
        self::assertTrue($result->requiresReview);
        self::assertSame('DEPENDENCY_GRAPH_INCOMPLETE', $result->verdict);
    }

    public function testRejectsInvalidJsonAndOversizedManifest(): void
    {
        $service = new ComposerLicenseService();
        try {
            $service->auditLockfileContent('{ invalid json');
            self::fail('Invalid JSON should fail.');
        } catch (\InvalidArgumentException $exception) {
            self::assertStringContainsString('Invalid JSON', $exception->getMessage());
        }

        $requirements = [];
        for ($index = 0; $index <= ComposerLicenseService::MAX_MANIFEST_PACKAGES; ++$index) {
            $requirements['vendor/package-' . $index] = '^1.0';
        }
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('more than');
        $service->auditLockfileContent(json_encode(['require' => $requirements], JSON_THROW_ON_ERROR));
    }

    /**
     * @param list<array<string, mixed>> $releases
     */
    private function metadataResponse(string $name, array $releases): MockResponse
    {
        return new MockResponse(json_encode(['packages' => [$name => $releases]], JSON_THROW_ON_ERROR), [
            'http_code' => 200,
            'response_headers' => ['content-type: application/json'],
        ]);
    }

    /**
     * @param list<string> $licenses
     * @param array<string, string> $requirements
     * @return array<string, mixed>
     */
    private function release(string $version, array $licenses, array $requirements = []): array
    {
        return [
            'version' => $version,
            'version_normalized' => $version . '.0',
            'license' => $licenses,
            'require' => $requirements,
        ];
    }
}
