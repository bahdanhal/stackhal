<?php

declare(strict_types=1);

namespace App\ComposerLicense\Domain\Model;

final readonly class LockfileAuditResult
{
    /**
     * @param list<PackageAuditResult> $prodResults
     * @param list<PackageAuditResult> $devResults
     */
    public function __construct(
        public int $totalProdPackages,
        public int $totalDevPackages,
        public int $reviewProdCount,
        public int $reviewDevCount,
        public bool $productionRequiresReview,
        public string $overallVerdict,
        public string $auditMode,
        public string $scopeNote,
        public array $prodResults,
        public array $devResults,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'total_prod_packages' => $this->totalProdPackages,
            'total_dev_packages' => $this->totalDevPackages,
            'review_prod_count' => $this->reviewProdCount,
            'review_dev_count' => $this->reviewDevCount,
            'production_requires_review' => $this->productionRequiresReview,
            'overall_verdict' => $this->overallVerdict,
            'audit_mode' => $this->auditMode,
            'scope_note' => $this->scopeNote,
            'production_packages' => array_map(
                static fn (PackageAuditResult $r): array => $r->toArray(),
                $this->prodResults
            ),
            'dev_packages' => array_map(
                static fn (PackageAuditResult $r): array => $r->toArray(),
                $this->devResults
            ),
        ];
    }
}
