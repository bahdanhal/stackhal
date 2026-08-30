<?php

declare(strict_types=1);

namespace App\ComposerLicense\Domain\Model;

final readonly class PackageAuditResult
{
    /**
     * @param list<string> $declaredLicenses
     * @param list<LicenseViolation> $violations
     */
    public function __construct(
        public string $packageName,
        public string $version,
        public array $declaredLicenses,
        public LicenseClassification $rootClassification,
        public int $totalDependencies,
        public bool $requiresReview,
        public string $verdict,
        public string $summary,
        public array $violations,
        public string $auditSource,
        public bool $isComplete = true,
        /** @var list<string> */
        public array $warnings = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'package_name' => $this->packageName,
            'version' => $this->version,
            'declared_licenses' => $this->declaredLicenses,
            'root_classification' => $this->rootClassification->value,
            'total_dependencies' => $this->totalDependencies,
            'requires_review' => $this->requiresReview,
            'verdict' => $this->verdict,
            'summary' => $this->summary,
            'violations_count' => count($this->violations),
            'violations' => array_map(static fn (LicenseViolation $v): array => $v->toArray(), $this->violations),
            'audit_source' => $this->auditSource,
            'is_complete' => $this->isComplete,
            'warnings' => $this->warnings,
        ];
    }
}
