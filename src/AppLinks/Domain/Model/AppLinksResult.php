<?php

declare(strict_types=1);

namespace App\AppLinks\Domain\Model;

final readonly class AppLinksResult
{
    /**
     * @param list<AppLinksDiagnostic> $diagnostics
     * @param list<string> $aasaAppIds
     * @param list<string> $androidPackageNames
     */
    public function __construct(
        public bool $isValid,
        public ?bool $opensInApp = null,
        public ?string $matchedPattern = null,
        public bool $matchedExclusion = false,
        public array $diagnostics = [],
        public bool $aasaValid = true,
        public bool $assetLinksValid = true,
        public array $aasaAppIds = [],
        public array $androidPackageNames = [],
        public ?string $testUrl = null,
        public ?string $domain = null,
        public ?string $aasaRaw = null,
        public ?string $assetLinksRaw = null,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getErrorCodes(): array
    {
        $codes = [];
        foreach ($this->diagnostics as $diagnostic) {
            if ($diagnostic->severity === 'error') {
                $codes[] = $diagnostic->code;
            }
        }

        return $codes;
    }

    /**
     * @return list<string>
     */
    public function getWarningCodes(): array
    {
        $codes = [];
        foreach ($this->diagnostics as $diagnostic) {
            if ($diagnostic->severity === 'warning') {
                $codes[] = $diagnostic->code;
            }
        }

        return $codes;
    }

    /**
     * @return list<string>
     */
    public function getInfoCodes(): array
    {
        $codes = [];
        foreach ($this->diagnostics as $diagnostic) {
            if ($diagnostic->severity === 'info') {
                $codes[] = $diagnostic->code;
            }
        }

        return $codes;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'is_valid' => $this->isValid,
            'opens_in_app' => $this->opensInApp,
            'matched_pattern' => $this->matchedPattern,
            'matched_exclusion' => $this->matchedExclusion,
            'aasa_valid' => $this->aasaValid,
            'assetlinks_valid' => $this->assetLinksValid,
            'aasa_app_ids' => $this->aasaAppIds,
            'android_package_names' => $this->androidPackageNames,
            'test_url' => $this->testUrl,
            'domain' => $this->domain,
            'diagnostics' => array_map(
                static fn (AppLinksDiagnostic $d): array => $d->toArray(),
                $this->diagnostics
            ),
        ];
    }
}
