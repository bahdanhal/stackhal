<?php

declare(strict_types=1);

namespace App\Mcp;

use App\ComposerLicense\Application\ComposerLicenseService;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;

final readonly class ComposerLicenseTools
{
    public function __construct(private ComposerLicenseService $licenseService)
    {
    }

    #[McpTool(
        name: 'audit_composer_package_license',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Screen a PHP / Packagist dependency graph for license metadata and copyleft review signals. The result is not a legal conclusion.'
    )]
    public function auditPackageLicense(
        // phpcs:ignore Generic.Files.LineLength
        #[Schema(description: 'The Packagist package name in "vendor/package" format (e.g. "paypal/paypal-server-sdk", "barryvdh/laravel-dompdf").')]
        string $package,
        #[Schema(description: 'Optional Composer version constraint. Defaults to any stable release.')]
        string $version_constraint = '*',
    ): string {
        try {
            $result = $this->licenseService->auditPackage($package, $version_constraint);
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

    #[McpTool(
        name: 'audit_composer_lockfile',
        // phpcs:ignore Generic.Files.LineLength
        description: 'Screen composer.lock exact versions or estimate composer.json constraints for copyleft license signals in production and development dependencies.'
    )]
    public function auditLockfile(
        // phpcs:ignore Generic.Files.LineLength
        #[Schema(description: 'The raw JSON string content of composer.json or composer.lock.')]
        string $json_content,
    ): string {
        try {
            $result = $this->licenseService->auditLockfileContent($json_content);
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
