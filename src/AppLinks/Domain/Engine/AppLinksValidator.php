<?php

declare(strict_types=1);

namespace App\AppLinks\Domain\Engine;

use App\AppLinks\Domain\Model\AppLinksDiagnostic;
use App\AppLinks\Domain\Model\AppLinksResult;

final class AppLinksValidator
{
    private const int MAX_AASA_SIZE_BYTES = 131072;
    private const string APP_ID_REGEX = '/^[A-Z0-9]{10}\.[a-zA-Z0-9_.-]+$/';
    private const string ANDROID_FINGERPRINT_REGEX = '/^([0-9A-F]{2}:){31}[0-9A-F]{2}$/';
    private const string ANDROID_REQUIRED_RELATION = 'delegate_permission/common.handle_all_urls';

    private const array DIAGNOSTIC_DEFINITIONS = [
        'ERR_AASA_NOT_FOUND' => [
            'severity' => 'error',
            'title' => 'Apple AASA File Unreachable',
            'description' => 'The apple-app-site-association file is missing or returns a non-200 HTTP status code.',
        ],
        'ERR_AASA_REDIRECT_FORBIDDEN' => [
            'severity' => 'error',
            'title' => 'HTTP Redirect Forbidden on AASA',
            'description' => 'Apple CDN and iOS strictly forbid HTTP 301/302 redirects when fetching AASA files.',
        ],
        'ERR_AASA_INVALID_JSON' => [
            'severity' => 'error',
            'title' => 'Malformed AASA JSON',
            'description' => 'The apple-app-site-association file is not valid JSON.',
        ],
        'ERR_AASA_MISSING_DETAILS' => [
            'severity' => 'error',
            'title' => 'AASA App Links Rules Missing',
            'description' => "The AASA document must contain a non-empty 'applinks.details' array.",
        ],
        'ERR_AASA_MISSING_APP_ID' => [
            'severity' => 'error',
            'title' => 'AASA App Identifier Missing',
            'description' => "Each AASA detail must declare at least one 'appID' or 'appIDs' value.",
        ],
        'ERR_AASA_MISSING_ROUTES' => [
            'severity' => 'error',
            'title' => 'AASA Route Rules Missing',
            'description' => "Each AASA detail must declare a non-empty 'components' or 'paths' array.",
        ],
        'ERR_AASA_INVALID_APP_ID' => [
            'severity' => 'error',
            'title' => 'Invalid Apple App ID Format',
            // phpcs:ignore Generic.Files.LineLength
            'description' => 'AppID must consist of a 10-character Team ID followed by a dot and Bundle Identifier (e.g. ABCDE12345.com.example.app).',
        ],
        'ERR_AASA_SIZE_EXCEEDED' => [
            'severity' => 'error',
            'title' => 'AASA Exceeds Maximum Size Limit',
            'description' => "File exceeds Apple's 128KB limit for uncompressed AASA manifests.",
        ],
        'ERR_ASSETLINKS_NOT_FOUND' => [
            'severity' => 'error',
            'title' => 'Android AssetLinks File Missing',
            'description' => 'The /.well-known/assetlinks.json file is unreachable or returns a non-200 HTTP status.',
        ],
        'ERR_ASSETLINKS_INVALID_JSON' => [
            'severity' => 'error',
            'title' => 'Malformed AssetLinks JSON',
            'description' => 'The assetlinks.json file is not valid JSON.',
        ],
        'ERR_ASSETLINKS_EMPTY' => [
            'severity' => 'error',
            'title' => 'AssetLinks Statements Missing',
            'description' => 'The assetlinks.json document must contain at least one statement.',
        ],
        'ERR_ASSETLINKS_INVALID_TARGET' => [
            'severity' => 'error',
            'title' => 'Android App Target Incomplete',
            'description' => "Each statement must identify an android_app package and at least one SHA-256 certificate fingerprint.",
        ],
        'ERR_NO_MANIFEST_INPUT' => [
            'severity' => 'error',
            'title' => 'No Manifest Provided',
            'description' => 'Offline validation requires at least one manifest.',
        ],
        'ERR_TEST_URL_INVALID' => [
            'severity' => 'error',
            'title' => 'Invalid Test URL',
            'description' => 'Enter a complete public HTTPS URL to verify live App Links hosting and routing.',
        ],
        'ERR_TEST_URL_DOMAIN_MISMATCH' => [
            'severity' => 'error',
            'title' => 'Test URL Does Not Match Manifest Host',
            'description' => 'Route matching is only valid when the tested URL uses the domain that served the manifests.',
        ],
        'ERR_ASSETLINKS_MISSING_RELATION' => [
            'severity' => 'error',
            'title' => 'Missing Required AssetLinks Relation',
            // phpcs:ignore Generic.Files.LineLength
            'description' => "Android App Links require 'delegate_permission/common.handle_all_urls' in the relation array.",
        ],
        'ERR_ASSETLINKS_INVALID_FINGERPRINT' => [
            'severity' => 'error',
            'title' => 'Invalid SHA-256 Fingerprint Format',
            'description' => 'Android certificate fingerprints must be 32 colon-separated two-digit uppercase hex values.',
        ],
        'WARN_LEGACY_AASA_PATHS' => [
            'severity' => 'warning',
            'title' => 'Legacy Paths Array Used',
            // phpcs:ignore Generic.Files.LineLength
            'description' => "Using legacy 'paths' array instead of modern iOS 13+ 'components' dictionary with pattern matching.",
        ],
        'WARN_CONTENT_TYPE_MISMATCH' => [
            'severity' => 'warning',
            'title' => 'Non-Standard Content-Type',
            'description' => 'Server returned text/plain or text/html instead of application/json.',
        ],
        'INFO_ROUTE_MATCHED_APP' => [
            'severity' => 'info',
            'title' => 'URL Matches an App Route Rule',
            // phpcs:ignore Generic.Files.LineLength
            'description' => 'The URL path matches an included AASA rule. Actual app opening also depends on the installed app, entitlements, association state, and device context.',
        ],
        'INFO_ROUTE_FALLS_BACK_WEB' => [
            'severity' => 'info',
            'title' => 'No Included AASA Route Match',
            'description' => 'No included AASA path rule matched, or an exclusion matched.',
        ],
    ];

    /**
     * @param array<string, mixed>|string $aasa
     * @param array<int, mixed>|string|null $assetLinks
     */
    public function validate(
        array|string $aasa,
        array|string|null $assetLinks = null,
        ?string $testUrl = null,
        ?string $domain = null,
        bool $requireAasa = false,
        bool $requireAssetLinks = false,
    ): AppLinksResult {
        /** @var list<AppLinksDiagnostic> $diagnostics */
        $diagnostics = [];
        $aasaAppIds = [];
        $androidPackageNames = [];
        $aasaValid = true;
        $assetLinksValid = true;

        $opensInApp = null;
        $matchedPattern = null;
        $matchedExclusion = false;
        $testUrlValid = $testUrl === null || $testUrl === ''
            ? true
            : $this->validateTestUrl($testUrl, $domain, $diagnostics);

        // Parse and validate Apple AASA
        $aasaProvided = is_array($aasa)
            ? $aasa !== []
            : !in_array(trim($aasa), ['', '{}'], true);
        if (!$aasaProvided && $assetLinks === null && !$requireAasa && !$requireAssetLinks) {
            $diagnostics[] = $this->createDiagnostic('ERR_NO_MANIFEST_INPUT');
        }
        $parsedAasa = !$aasaProvided
            ? null
            : (is_string($aasa) ? $this->parseJsonString($aasa, 'aasa', $diagnostics, $aasaValid) : $aasa);

        if ($parsedAasa !== null) {
            $aasaRaw = is_string($aasa) ? $aasa : json_encode($aasa, JSON_UNESCAPED_SLASHES);
            if (is_string($aasaRaw) && strlen($aasaRaw) > self::MAX_AASA_SIZE_BYTES) {
                $diagnostics[] = $this->createDiagnostic('ERR_AASA_SIZE_EXCEEDED');
                $aasaValid = false;
            }

            $this->validateAasaStructure($parsedAasa, $diagnostics, $aasaAppIds, $aasaValid);

            if ($aasaValid && $testUrl !== null && $testUrl !== '' && $testUrlValid) {
                $this->evaluateUrlRouting($parsedAasa, $testUrl, $opensInApp, $matchedPattern, $matchedExclusion, $diagnostics);
            }
        } elseif ($requireAasa) {
            $aasaValid = false;
            $diagnostics[] = $this->createDiagnostic('ERR_AASA_NOT_FOUND');
        }

        // Parse and validate Android AssetLinks if provided
        if ($assetLinks !== null) {
            $parsedAssetLinks = is_string($assetLinks)
                ? $this->parseJsonString($assetLinks, 'assetlinks', $diagnostics, $assetLinksValid)
                : $assetLinks;

            if ($parsedAssetLinks !== null) {
                $this->validateAssetLinksStructure($parsedAssetLinks, $diagnostics, $androidPackageNames, $assetLinksValid);
            }
        } elseif ($requireAssetLinks) {
            $assetLinksValid = false;
            $diagnostics[] = $this->createDiagnostic('ERR_ASSETLINKS_NOT_FOUND');
        }

        $hasErrors = false;
        foreach ($diagnostics as $diagnostic) {
            if ($diagnostic->severity === 'error') {
                $hasErrors = true;
                break;
            }
        }

        return new AppLinksResult(
            isValid: !$hasErrors && $aasaValid && $assetLinksValid,
            opensInApp: $opensInApp,
            matchedPattern: $matchedPattern,
            matchedExclusion: $matchedExclusion,
            diagnostics: $diagnostics,
            aasaValid: $aasaValid,
            assetLinksValid: $assetLinksValid,
            aasaAppIds: array_values(array_unique($aasaAppIds)),
            androidPackageNames: array_values(array_unique($androidPackageNames)),
            testUrl: $testUrl,
            domain: $domain,
            aasaRaw: is_string($aasa) ? $aasa : null,
            assetLinksRaw: is_string($assetLinks) ? $assetLinks : null,
        );
    }

    /**
     * @param list<AppLinksDiagnostic> $diagnostics
     * @return array<mixed>|null
     */
    private function parseJsonString(string $json, string $type, array &$diagnostics, bool &$valid): ?array
    {
        $trimmed = trim($json);
        if ($trimmed === '') {
            return null;
        }

        try {
            $decoded = json_decode($trimmed, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($decoded)) {
                throw new \InvalidArgumentException('JSON must be an object or array.');
            }

            return $decoded;
        } catch (\Throwable) {
            $valid = false;
            $code = $type === 'aasa' ? 'ERR_AASA_INVALID_JSON' : 'ERR_ASSETLINKS_INVALID_JSON';
            $diagnostics[] = $this->createDiagnostic($code);

            return null;
        }
    }

    /**
     * @param array<string, mixed> $aasa
     * @param list<AppLinksDiagnostic> $diagnostics
     * @param list<string> $appIds
     */
    private function validateAasaStructure(array $aasa, array &$diagnostics, array &$appIds, bool &$valid): void
    {
        $details = $aasa['applinks']['details'] ?? null;
        if (!is_array($details) || $details === []) {
            $valid = false;
            $diagnostics[] = $this->createDiagnostic('ERR_AASA_MISSING_DETAILS');

            return;
        }

        foreach ($details as $detail) {
            if (!is_array($detail)) {
                $valid = false;
                $diagnostics[] = $this->createDiagnostic('ERR_AASA_MISSING_ROUTES');
                continue;
            }

            // Extract App IDs
            $ids = [];
            if (isset($detail['appIDs']) && is_array($detail['appIDs'])) {
                $ids = $detail['appIDs'];
            } elseif (isset($detail['appID']) && is_string($detail['appID'])) {
                $ids = [$detail['appID']];
            }

            if ($ids === []) {
                $valid = false;
                $diagnostics[] = $this->createDiagnostic('ERR_AASA_MISSING_APP_ID');
            }

            foreach ($ids as $appId) {
                if (is_string($appId)) {
                    $appIds[] = $appId;
                    if (preg_match(self::APP_ID_REGEX, $appId) !== 1) {
                        $valid = false;
                        $diagnostics[] = $this->createDiagnostic('ERR_AASA_INVALID_APP_ID');
                    }
                } else {
                    $valid = false;
                    $diagnostics[] = $this->createDiagnostic('ERR_AASA_INVALID_APP_ID');
                }
            }

            if (isset($detail['paths']) && !isset($detail['components'])) {
                $diagnostics[] = $this->createDiagnostic('WARN_LEGACY_AASA_PATHS');
            }

            $components = $detail['components'] ?? null;
            $paths = $detail['paths'] ?? null;
            if ((!is_array($components) || $components === []) && (!is_array($paths) || $paths === [])) {
                $valid = false;
                $diagnostics[] = $this->createDiagnostic('ERR_AASA_MISSING_ROUTES');
            }
        }
    }

    /**
     * @param array<int, mixed>|array<string, mixed> $assetLinks
     * @param list<AppLinksDiagnostic> $diagnostics
     * @param list<string> $packageNames
     */
    private function validateAssetLinksStructure(array $assetLinks, array &$diagnostics, array &$packageNames, bool &$valid): void
    {
        $statements = isset($assetLinks[0]) ? $assetLinks : [$assetLinks];

        if ($assetLinks === []) {
            $valid = false;
            $diagnostics[] = $this->createDiagnostic('ERR_ASSETLINKS_EMPTY');

            return;
        }

        foreach ($statements as $statement) {
            if (!is_array($statement)) {
                $valid = false;
                $diagnostics[] = $this->createDiagnostic('ERR_ASSETLINKS_INVALID_TARGET');
                continue;
            }

            // Check relation
            $relation = $statement['relation'] ?? [];
            $relations = is_array($relation) ? $relation : [$relation];
            $hasHandleAll = in_array(self::ANDROID_REQUIRED_RELATION, $relations, true);

            if (!$hasHandleAll) {
                $valid = false;
                $diagnostics[] = $this->createDiagnostic('ERR_ASSETLINKS_MISSING_RELATION');
            }

            // Check target package and fingerprints
            $target = $statement['target'] ?? [];
            if (is_array($target)) {
                if (isset($target['package_name']) && is_string($target['package_name'])) {
                    $packageNames[] = $target['package_name'];
                }

                $fingerprints = $target['sha256_cert_fingerprints'] ?? [];
                $fingerprintsList = is_array($fingerprints) ? $fingerprints : [$fingerprints];

                if (
                    ($target['namespace'] ?? null) !== 'android_app'
                    || !isset($target['package_name'])
                    || !is_string($target['package_name'])
                    || trim($target['package_name']) === ''
                    || $fingerprintsList === []
                ) {
                    $valid = false;
                    $diagnostics[] = $this->createDiagnostic('ERR_ASSETLINKS_INVALID_TARGET');
                }

                foreach ($fingerprintsList as $fp) {
                    if (!is_string($fp) || preg_match(self::ANDROID_FINGERPRINT_REGEX, $fp) !== 1) {
                        $valid = false;
                        $diagnostics[] = $this->createDiagnostic('ERR_ASSETLINKS_INVALID_FINGERPRINT');
                    }
                }
            } else {
                $valid = false;
                $diagnostics[] = $this->createDiagnostic('ERR_ASSETLINKS_INVALID_TARGET');
            }
        }
    }

    /**
     * @param list<AppLinksDiagnostic> $diagnostics
     */
    private function validateTestUrl(string $testUrl, ?string $domain, array &$diagnostics): bool
    {
        $parts = parse_url($testUrl);
        if (!is_array($parts) || ($parts['scheme'] ?? null) !== 'https' || !isset($parts['host'])) {
            $diagnostics[] = $this->createDiagnostic('ERR_TEST_URL_INVALID');

            return false;
        }

        if ($domain !== null && $domain !== '' && strtolower($parts['host']) !== strtolower($domain)) {
            $diagnostics[] = $this->createDiagnostic('ERR_TEST_URL_DOMAIN_MISMATCH');

            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed> $aasa
     * @param list<AppLinksDiagnostic> $diagnostics
     * @param-out bool $opensInApp
     * @param-out string|null $matchedPattern
     * @param-out bool $matchedExclusion
     */
    private function evaluateUrlRouting(
        array $aasa,
        string $testUrl,
        ?bool &$opensInApp,
        ?string &$matchedPattern,
        bool &$matchedExclusion,
        array &$diagnostics,
    ): void {
        $path = (string) parse_url($testUrl, PHP_URL_PATH);
        if ($path === '') {
            $path = '/';
        }

        $details = $aasa['applinks']['details'] ?? [];
        if (!is_array($details)) {
            $opensInApp = false;
            $diagnostics[] = $this->createDiagnostic('INFO_ROUTE_FALLS_BACK_WEB');

            return;
        }

        foreach ($details as $detail) {
            if (!is_array($detail)) {
                continue;
            }

            $components = $detail['components'] ?? null;
            if (is_array($components)) {
                foreach ($components as $comp) {
                    if (!is_array($comp) || !isset($comp['/']) || !is_string($comp['/'])) {
                        continue;
                    }

                    $pattern = $comp['/'];
                    $isExcluded = (bool) ($comp['exclude'] ?? false);

                    if ($this->matchPathPattern($path, $pattern)) {
                        $matchedPattern = $pattern;
                        if ($isExcluded) {
                            $opensInApp = false;
                            $matchedExclusion = true;
                            $diagnostics[] = $this->createDiagnostic('INFO_ROUTE_FALLS_BACK_WEB');
                        } else {
                            $opensInApp = true;
                            $matchedExclusion = false;
                            $diagnostics[] = $this->createDiagnostic('INFO_ROUTE_MATCHED_APP');
                        }

                        return;
                    }
                }
            }

            // Fallback: legacy paths
            $paths = $detail['paths'] ?? null;
            if (is_array($paths)) {
                foreach ($paths as $p) {
                    if (!is_string($p)) {
                        continue;
                    }
                    $isExcluded = str_starts_with($p, 'NOT ');
                    $pattern = $isExcluded ? substr($p, 4) : $p;

                    if ($this->matchPathPattern($path, $pattern)) {
                        $matchedPattern = $pattern;
                        if ($isExcluded) {
                            $opensInApp = false;
                            $matchedExclusion = true;
                            $diagnostics[] = $this->createDiagnostic('INFO_ROUTE_FALLS_BACK_WEB');
                        } else {
                            $opensInApp = true;
                            $matchedExclusion = false;
                            $diagnostics[] = $this->createDiagnostic('INFO_ROUTE_MATCHED_APP');
                        }

                        return;
                    }
                }
            }
        }

        // If no matching rule was found, default to browser fallback
        $opensInApp = false;
        $diagnostics[] = $this->createDiagnostic('INFO_ROUTE_FALLS_BACK_WEB');
    }

    private function matchPathPattern(string $path, string $pattern): bool
    {
        // Wildcard * matches 0 or more characters, ? matches 1 character
        $escaped = preg_quote($pattern, '#');
        $regex = '^' . str_replace(['\\*', '\\?'], ['.*', '.'], $escaped) . '$';

        return (bool) preg_match('#' . $regex . '#', $path);
    }

    private function createDiagnostic(string $code): AppLinksDiagnostic
    {
        $def = self::DIAGNOSTIC_DEFINITIONS[$code] ?? [
            'severity' => 'info',
            'title' => $code,
            'description' => $code,
        ];

        return new AppLinksDiagnostic(
            code: $code,
            severity: $def['severity'],
            title: $def['title'],
            description: $def['description'],
        );
    }
}
