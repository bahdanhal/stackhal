<?php

declare(strict_types=1);

namespace App\Cors\Domain\Model;

final readonly class CorsAnalysisResult
{
    /**
     * @param list<CorsDiagnostic> $diagnostics
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $responseHeaders
     * @param array<string, int> $browserCeilings
     */
    public function __construct(
        public bool $isValid,
        public array $diagnostics = [],
        public array $requestHeaders = [],
        public array $responseHeaders = [],
        public array $browserCeilings = [
            'chromium' => 7200,
            'firefox' => 86400,
            'safari' => 600,
        ],
        public ?string $requestOrigin = null,
        public bool $withCredentials = false,
        public ?string $requestMethod = null,
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
            'request_origin' => $this->requestOrigin,
            'request_method' => $this->requestMethod,
            'with_credentials' => $this->withCredentials,
            'request_headers' => $this->requestHeaders,
            'response_headers' => $this->responseHeaders,
            'browser_ceilings' => $this->browserCeilings,
            'diagnostics' => array_map(
                static fn (CorsDiagnostic $d): array => $d->toArray(),
                $this->diagnostics
            ),
        ];
    }
}
