<?php

declare(strict_types=1);

namespace App\ComposerLicense\Application;

use App\ComposerLicense\Domain\Model\LicenseClassification;
use App\ComposerLicense\Domain\Model\LicenseViolation;
use App\ComposerLicense\Domain\Model\LockfileAuditResult;
use App\ComposerLicense\Domain\Model\PackageAuditResult;
use Composer\Semver\Semver;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ComposerLicenseService
{
    public const MAX_MANIFEST_PACKAGES = 100;
    public const MAX_LOCK_PACKAGES = 500;

    /** @var array<string, array<string, mixed>> */
    private array $metadataCache = [];

    /** @var list<string> */
    private const STRONG_COPYLEFT = [
        'gpl-2.0', 'gpl-2.0+', 'gpl-2.0-only', 'gpl-2.0-or-later',
        'gpl-3.0', 'gpl-3.0+', 'gpl-3.0-only', 'gpl-3.0-or-later',
        'agpl-1.0', 'agpl-3.0', 'agpl-3.0-only', 'agpl-3.0-or-later',
        'sspl', 'osl-3.0', 'osl-2.1', 'eupl-1.1', 'eupl-1.2', 'cc-by-sa-4.0',
    ];

    /** @var list<string> */
    private const WEAK_COPYLEFT = [
        'lgpl-2.0', 'lgpl-2.0+', 'lgpl-2.1', 'lgpl-2.1+', 'lgpl-2.1-only', 'lgpl-2.1-or-later',
        'lgpl-3.0', 'lgpl-3.0+', 'lgpl-3.0-only', 'lgpl-3.0-or-later',
        'mpl-1.1', 'mpl-2.0', 'epl-1.0', 'epl-2.0', 'cddl-1.0', 'cddl-1.1',
    ];

    /** @var list<string> */
    private const PERMISSIVE = [
        'mit', 'apache-2.0', 'apache 2.0', 'bsd-2-clause', 'bsd-3-clause', 'bsd', 'isc',
        '0bsd', 'unlicense', 'cc0-1.0', 'cc0', 'zlib', 'wtfpl',
    ];

    public function __construct(private readonly ?HttpClientInterface $httpClient = null)
    {
    }

    public function auditPackage(
        string $packageName,
        string $versionConstraint = '*',
        int $maxDepth = 6,
    ): PackageAuditResult {
        $packageName = $this->normalizePackageName($packageName);
        $warnings = [];
        $isComplete = true;
        $tree = $this->resolvePackagistTree(
            $packageName,
            $versionConstraint,
            max(1, min(10, $maxDepth)),
            warnings: $warnings,
            isComplete: $isComplete,
        );

        return $this->createPackageResult($tree, 'packagist_constraint_resolution', $isComplete, $warnings);
    }

    public function auditLockfileContent(string $content): LockfileAuditResult
    {
        $data = json_decode($content, true);
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON. Provide composer.json or composer.lock content.');
        }
        if (isset($data['packages']) && is_array($data['packages'])) {
            return $this->auditExactLockfile($data);
        }
        if (isset($data['require']) && is_array($data['require'])) {
            return $this->auditManifest($data);
        }

        throw new \InvalidArgumentException('The JSON is neither a Composer manifest nor a Composer lock file.');
    }

    /**
     * @param list<string> $licenses
     */
    public function classifyLicenses(array $licenses): LicenseClassification
    {
        if ($licenses === []) {
            return LicenseClassification::UNKNOWN;
        }
        $hasPermissive = false;
        $strongCopyleft = false;
        $weakCopyleft = false;
        foreach ($licenses as $license) {
            $normalized = strtolower(trim($license));
            if ($normalized === 'proprietary') {
                return LicenseClassification::PROPRIETARY;
            }
            foreach (self::PERMISSIVE as $identifier) {
                if ($this->containsLicenseIdentifier($normalized, $identifier)) {
                    $hasPermissive = true;
                    break;
                }
            }
            foreach (self::STRONG_COPYLEFT as $identifier) {
                if ($this->containsLicenseIdentifier($normalized, $identifier)) {
                    $strongCopyleft = true;
                    break;
                }
            }
            foreach (self::WEAK_COPYLEFT as $identifier) {
                if ($this->containsLicenseIdentifier($normalized, $identifier)) {
                    $weakCopyleft = true;
                    break;
                }
            }
        }
        if ($hasPermissive) {
            return $strongCopyleft || $weakCopyleft
                ? LicenseClassification::DUAL_PERMISSIVE_OPTION
                : LicenseClassification::PERMISSIVE;
        }
        if ($strongCopyleft) {
            return LicenseClassification::STRONG_COPYLEFT;
        }
        if ($weakCopyleft) {
            return LicenseClassification::WEAK_COPYLEFT;
        }

        return LicenseClassification::UNKNOWN;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function auditExactLockfile(array $data): LockfileAuditResult
    {
        $productionPackages = $this->packageList($data['packages'] ?? []);
        $developmentPackages = $this->packageList($data['packages-dev'] ?? []);
        if (count($productionPackages) + count($developmentPackages) > self::MAX_LOCK_PACKAGES) {
            throw new \InvalidArgumentException(sprintf(
                'The lock file contains more than %d packages. Reduce the input or audit it locally.',
                self::MAX_LOCK_PACKAGES,
            ));
        }
        $packageIndex = [];
        foreach (array_merge($productionPackages, $developmentPackages) as $package) {
            $name = strtolower((string) ($package['name'] ?? ''));
            if ($name !== '') {
                $packageIndex[$name] = $package;
            }
        }
        $productionResults = $this->auditLockedPackageList($productionPackages, $packageIndex);
        $developmentResults = $this->auditLockedPackageList($developmentPackages, $packageIndex);

        return $this->createLockfileResult(
            $productionResults,
            $developmentResults,
            'locked_versions',
            'Exact package versions, declared licenses, and dependency links were read from composer.lock. '
                . 'The result is a compliance risk signal, not a legal conclusion.',
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function auditManifest(array $data): LockfileAuditResult
    {
        $productionRequirements = $this->manifestRequirements($data['require'] ?? []);
        $developmentRequirements = $this->manifestRequirements($data['require-dev'] ?? []);
        if (count($productionRequirements) + count($developmentRequirements) > self::MAX_MANIFEST_PACKAGES) {
            throw new \InvalidArgumentException(sprintf(
                'The manifest contains more than %d package requirements. Reduce the input or submit composer.lock.',
                self::MAX_MANIFEST_PACKAGES,
            ));
        }
        $productionResults = [];
        foreach ($productionRequirements as $name => $constraint) {
            $productionResults[] = $this->auditPackage($name, $constraint, 6);
        }
        $developmentResults = [];
        foreach ($developmentRequirements as $name => $constraint) {
            $developmentResults[] = $this->auditPackage($name, $constraint, 6);
        }

        return $this->createLockfileResult(
            $productionResults,
            $developmentResults,
            'manifest_estimate',
            'composer.json contains constraints rather than installed versions. The service selects compatible stable '
                . 'Packagist releases without running the Composer SAT solver; submit composer.lock for an exact audit.',
        );
    }

    /**
     * @param list<array<string, mixed>> $packages
     * @param array<string, array<string, mixed>> $packageIndex
     * @return list<PackageAuditResult>
     */
    private function auditLockedPackageList(array $packages, array $packageIndex): array
    {
        $results = [];
        foreach ($packages as $package) {
            $name = strtolower((string) ($package['name'] ?? ''));
            if ($name !== '') {
                $results[] = $this->createPackageResult(
                    $this->resolveLockedTree($name, $packageIndex),
                    'composer_lock',
                    true,
                    [],
                );
            }
        }

        return $results;
    }

    /**
     * @param list<PackageAuditResult> $productionResults
     * @param list<PackageAuditResult> $developmentResults
     */
    private function createLockfileResult(
        array $productionResults,
        array $developmentResults,
        string $auditMode,
        string $scopeNote,
    ): LockfileAuditResult {
        $reviewProdCount = count(array_filter(
            $productionResults,
            static fn (PackageAuditResult $result): bool => $result->requiresReview,
        ));
        $reviewDevCount = count(array_filter(
            $developmentResults,
            static fn (PackageAuditResult $result): bool => $result->requiresReview,
        ));
        $productionRequiresReview = $reviewProdCount > 0;

        return new LockfileAuditResult(
            totalProdPackages: count($productionResults),
            totalDevPackages: count($developmentResults),
            reviewProdCount: $reviewProdCount,
            reviewDevCount: $reviewDevCount,
            productionRequiresReview: $productionRequiresReview,
            overallVerdict: $productionRequiresReview
                ? 'PRODUCTION_LICENSE_REVIEW_RECOMMENDED'
                : ($reviewDevCount > 0 ? 'DEV_LICENSE_REVIEW_RECOMMENDED' : 'NO_COPYLEFT_RISK_SIGNAL'),
            auditMode: $auditMode,
            scopeNote: $scopeNote,
            prodResults: $productionResults,
            devResults: $developmentResults,
        );
    }

    /**
     * @param array<string, mixed> $tree
     * @param list<string> $warnings
     */
    private function createPackageResult(
        array $tree,
        string $auditSource,
        bool $isComplete,
        array $warnings,
    ): PackageAuditResult {
        /** @var list<string> $rootLicenses */
        $rootLicenses = is_array($tree['licenses'] ?? null) ? $tree['licenses'] : [];
        $rootClassification = $this->classifyLicenses($rootLicenses);
        $findings = [];
        $seen = [];
        $this->collectFindings($tree, $findings, $seen);
        $hasStrong = count(array_filter(
            $findings,
            static fn (LicenseViolation $finding): bool => $finding->classification === LicenseClassification::STRONG_COPYLEFT,
        )) > 0;
        $rootNeedsCompatibilityReview = in_array($rootClassification, [
            LicenseClassification::PERMISSIVE,
            LicenseClassification::DUAL_PERMISSIVE_OPTION,
            LicenseClassification::PROPRIETARY,
        ], true);
        $requiresReview = $rootClassification === LicenseClassification::UNKNOWN
            || $rootClassification->isCopyleft()
            || !$isComplete
            || ($rootNeedsCompatibilityReview && $findings !== []);

        if ($rootClassification === LicenseClassification::UNKNOWN) {
            $verdict = 'LICENSE_DATA_INCOMPLETE';
            $summary = 'The selected package release has no recognized license metadata; manual review is required.';
        } elseif (!$isComplete) {
            $verdict = 'DEPENDENCY_GRAPH_INCOMPLETE';
            $summary = 'Part of the dependency graph could not be resolved; do not treat this result as a clean bill of health.';
        } elseif ($rootNeedsCompatibilityReview && $findings !== []) {
            $verdict = $hasStrong ? 'STRONG_COPYLEFT_REVIEW' : 'WEAK_COPYLEFT_NOTICE_REVIEW';
            $summary = sprintf(
                'The package declares %s terms and has %d transitive copyleft license signal(s). '
                    . 'Review compatibility, distribution, linking, and notice obligations.',
                implode(', ', $rootLicenses),
                count($findings),
            );
        } elseif ($rootClassification->isCopyleft() && $findings !== []) {
            $verdict = 'COPYLEFT_ALIGNED';
            $summary = 'Copyleft dependencies were found and the root package is also copyleft; verify version-specific obligations.';
        } elseif ($rootClassification->isCopyleft()) {
            $verdict = 'COPYLEFT_LICENSE_REVIEW';
            $summary = 'The selected package declares a copyleft license; verify specific use, distribution, notice, and source obligations.';
        } else {
            $verdict = 'NO_COPYLEFT_RISK_SIGNAL';
            $summary = 'No recognized copyleft license signal was found in the resolved dependency graph.';
        }

        return new PackageAuditResult(
            packageName: (string) ($tree['name'] ?? ''),
            version: (string) ($tree['version'] ?? 'unknown'),
            declaredLicenses: $rootLicenses,
            rootClassification: $rootClassification,
            totalDependencies: $this->countUniqueDependencies($tree),
            requiresReview: $requiresReview,
            verdict: $verdict,
            summary: $summary,
            violations: $findings,
            auditSource: $auditSource,
            isComplete: $isComplete,
            warnings: array_values(array_unique($warnings)),
        );
    }

    /**
     * @param list<string> $path
     * @param array<string, bool> $visited
     * @param list<string> $warnings
     * @return array<string, mixed>
     */
    private function resolvePackagistTree(
        string $packageName,
        string $versionConstraint,
        int $maxDepth,
        int $currentDepth = 0,
        array $visited = [],
        array $path = [],
        array &$warnings = [],
        bool &$isComplete = true,
    ): array {
        $metadata = $this->fetchPackagistMetadata($packageName);
        $selected = $this->selectVersion($metadata, $packageName, $versionConstraint);
        $version = (string) ($selected['version'] ?? 'unknown');
        $licenses = $this->licenseList($selected['license'] ?? []);
        $currentPath = [...$path, $this->pathLabel($packageName, $version, $licenses)];
        $visited[$packageName] = true;
        $dependencies = [];
        $requirements = is_array($selected['require'] ?? null) ? $selected['require'] : [];

        if ($currentDepth >= $maxDepth && $requirements !== []) {
            $isComplete = false;
            $warnings[] = sprintf('Traversal stopped at depth %d below %s.', $maxDepth, $packageName);
        } else {
            foreach ($requirements as $dependencyName => $constraint) {
                $dependencyName = strtolower((string) $dependencyName);
                if (!$this->isPackageName($dependencyName) || isset($visited[$dependencyName])) {
                    continue;
                }
                try {
                    $dependencies[] = $this->resolvePackagistTree(
                        $dependencyName,
                        is_string($constraint) ? $constraint : '*',
                        $maxDepth,
                        $currentDepth + 1,
                        $visited,
                        $currentPath,
                        $warnings,
                        $isComplete,
                    );
                } catch (\RuntimeException $exception) {
                    $isComplete = false;
                    $warnings[] = $exception->getMessage();
                }
            }
        }

        return [
            'name' => $packageName,
            'version' => $version,
            'licenses' => $licenses,
            'path' => $currentPath,
            'dependencies' => $dependencies,
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $packageIndex
     * @param array<string, bool> $visited
     * @param list<string> $path
     * @return array<string, mixed>
     */
    private function resolveLockedTree(
        string $packageName,
        array $packageIndex,
        array $visited = [],
        array $path = [],
    ): array {
        $package = $packageIndex[$packageName] ?? null;
        if ($package === null) {
            return [
                'name' => $packageName,
                'version' => 'not-in-lock',
                'licenses' => [],
                'path' => $path,
                'dependencies' => [],
            ];
        }
        $version = (string) ($package['version'] ?? 'unknown');
        $licenses = $this->licenseList($package['license'] ?? []);
        $currentPath = [...$path, $this->pathLabel($packageName, $version, $licenses)];
        $visited[$packageName] = true;
        $dependencies = [];
        $requirements = is_array($package['require'] ?? null) ? $package['require'] : [];
        foreach (array_keys($requirements) as $dependencyName) {
            $dependencyName = strtolower((string) $dependencyName);
            if (
                !$this->isPackageName($dependencyName)
                || isset($visited[$dependencyName])
                || !isset($packageIndex[$dependencyName])
            ) {
                continue;
            }
            $dependencies[] = $this->resolveLockedTree($dependencyName, $packageIndex, $visited, $currentPath);
        }

        return [
            'name' => $packageName,
            'version' => $version,
            'licenses' => $licenses,
            'path' => $currentPath,
            'dependencies' => $dependencies,
        ];
    }

    /**
     * @param array<string, mixed> $node
     * @param list<LicenseViolation> $findings
     * @param array<string, bool> $seen
     */
    private function collectFindings(array $node, array &$findings, array &$seen): void
    {
        $dependencies = is_array($node['dependencies'] ?? null) ? $node['dependencies'] : [];
        foreach ($dependencies as $dependency) {
            if (!is_array($dependency)) {
                continue;
            }
            $name = (string) ($dependency['name'] ?? '');
            /** @var list<string> $licenses */
            $licenses = is_array($dependency['licenses'] ?? null) ? $dependency['licenses'] : [];
            $classification = $this->classifyLicenses($licenses);
            if ($classification->isCopyleft() && !isset($seen[$name])) {
                $seen[$name] = true;
                $path = is_array($dependency['path'] ?? null) ? $dependency['path'] : [];
                $findings[] = new LicenseViolation(
                    packageName: $name,
                    version: (string) ($dependency['version'] ?? 'unknown'),
                    license: implode(', ', $licenses),
                    classification: $classification,
                    dependencyPath: implode(' -> ', $path),
                    description: $classification === LicenseClassification::STRONG_COPYLEFT
                        ? 'Strong copyleft signal. Review derivative-work status and distribution or external-deployment obligations.'
                        : 'Weak copyleft signal. Review linking, replacement, source-offer, attribution, and notice obligations.',
                );
            }
            $this->collectFindings($dependency, $findings, $seen);
        }
    }

    /**
     * @param array<string, mixed> $node
     * @param array<string, bool> $seen
     */
    private function countUniqueDependencies(array $node, array &$seen = []): int
    {
        $dependencies = is_array($node['dependencies'] ?? null) ? $node['dependencies'] : [];
        foreach ($dependencies as $dependency) {
            if (is_array($dependency) && is_string($dependency['name'] ?? null)) {
                $seen[$dependency['name']] = true;
                $this->countUniqueDependencies($dependency, $seen);
            }
        }

        return count($seen);
    }

    /**
     * @param array<string, mixed> $metadata
     * @return array<string, mixed>
     */
    private function selectVersion(array $metadata, string $packageName, string $constraint): array
    {
        $versions = $metadata['packages'][$packageName] ?? [];
        if (!is_array($versions) || $versions === []) {
            throw new \RuntimeException(sprintf('Packagist returned no releases for %s.', $packageName));
        }
        foreach ($versions as $version) {
            if (!is_array($version)) {
                continue;
            }
            $normalizedVersion = (string) ($version['version_normalized'] ?? $version['version'] ?? '');
            $displayVersion = (string) ($version['version'] ?? '');
            if ($normalizedVersion === '' || preg_match('/(?:dev|alpha|beta|rc)/i', $displayVersion)) {
                continue;
            }
            try {
                if ($constraint === '' || $constraint === '*' || Semver::satisfies($normalizedVersion, $constraint)) {
                    return $version;
                }
            } catch (\UnexpectedValueException) {
                throw new \RuntimeException(sprintf(
                    'Unsupported version constraint "%s" for %s.',
                    $constraint,
                    $packageName,
                ));
            }
        }

        throw new \RuntimeException(sprintf(
            'No stable Packagist release of %s satisfies "%s".',
            $packageName,
            $constraint,
        ));
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchPackagistMetadata(string $packageName): array
    {
        if (isset($this->metadataCache[$packageName])) {
            return $this->metadataCache[$packageName];
        }
        [$vendor, $name] = explode('/', $packageName, 2);
        $url = sprintf('https://repo.packagist.org/p2/%s/%s.json', rawurlencode($vendor), rawurlencode($name));
        if ($this->httpClient === null) {
            throw new \RuntimeException('Packagist HTTP client is unavailable.');
        }
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => ['User-Agent' => 'StackhalComposerLicenseAudit/2.0'],
                'timeout' => 8,
            ]);
            $statusCode = $response->getStatusCode();
        } catch (\Throwable) {
            throw new \RuntimeException(sprintf('Could not retrieve Packagist metadata for %s.', $packageName));
        }
        if ($statusCode !== 200) {
            throw new \RuntimeException(sprintf('Packagist returned HTTP %d for %s.', $statusCode, $packageName));
        }
        try {
            $data = $response->toArray(false);
        } catch (\Throwable) {
            throw new \RuntimeException(sprintf('Packagist returned invalid metadata for %s.', $packageName));
        }
        $this->metadataCache[$packageName] = $data;

        return $data;
    }

    private function normalizePackageName(string $packageName): string
    {
        $packageName = strtolower(trim($packageName));
        if (!$this->isPackageName($packageName)) {
            throw new \InvalidArgumentException('Use a valid Composer package name in vendor/package format.');
        }

        return $packageName;
    }

    private function isPackageName(string $packageName): bool
    {
        return preg_match(
            '#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:(?:[_.]|-{1,2})?[a-z0-9]+)*$#',
            $packageName,
        ) === 1;
    }

    /**
     * @param mixed $requirements
     * @return array<string, string>
     */
    private function manifestRequirements(mixed $requirements): array
    {
        if (!is_array($requirements)) {
            return [];
        }
        $packages = [];
        foreach ($requirements as $name => $constraint) {
            $name = strtolower((string) $name);
            if ($this->isPackageName($name)) {
                $packages[$name] = is_string($constraint) ? $constraint : '*';
            }
        }

        return $packages;
    }

    /**
     * @param mixed $packages
     * @return list<array<string, mixed>>
     */
    private function packageList(mixed $packages): array
    {
        if (!is_array($packages)) {
            return [];
        }

        return array_values(array_filter($packages, static fn (mixed $package): bool => is_array($package)));
    }

    /**
     * @param mixed $licenses
     * @return list<string>
     */
    private function licenseList(mixed $licenses): array
    {
        if (is_string($licenses) && trim($licenses) !== '') {
            return [trim($licenses)];
        }
        if (!is_array($licenses)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $license): string => trim((string) $license),
            $licenses,
        )));
    }

    /**
     * @param list<string> $licenses
     */
    private function pathLabel(string $packageName, string $version, array $licenses): string
    {
        return sprintf('%s@%s (%s)', $packageName, $version, implode(', ', $licenses) ?: 'Unspecified');
    }

    private function containsLicenseIdentifier(string $license, string $identifier): bool
    {
        return preg_match(
            '/(?:^|[()\s,|])' . preg_quote($identifier, '/') . '(?:$|[()\s,|])/i',
            $license,
        ) === 1;
    }
}
