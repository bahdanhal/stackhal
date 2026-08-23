<?php

declare(strict_types=1);

namespace App\Cors\Domain\Engine;

use App\Cors\Domain\Model\CorsAnalysisResult;
use App\Cors\Domain\Model\CorsDiagnostic;

final class CorsAnalyzer
{
    private const array BROWSER_MAX_AGE_CEILINGS = [
        'chromium' => 7200,
        'firefox' => 86400,
        'safari' => 600,
    ];

    private const array SAFELISTED_RESPONSE_HEADERS = [
        'cache-control',
        'content-language',
        'content-length',
        'content-type',
        'expires',
        'last-modified',
        'pragma',
        'vary',
        'access-control-allow-origin',
        'access-control-allow-methods',
        'access-control-allow-headers',
        'access-control-allow-credentials',
        'access-control-expose-headers',
        'access-control-max-age',
        'date',
        'server',
    ];

    private const array SAFELISTED_REQUEST_HEADERS = [
        'accept',
        'accept-language',
        'content-language',
        'content-type',
    ];

    private const array DIAGNOSTIC_DEFINITIONS = [
        'ERR_CORS_WILDCARD_WITH_CREDENTIALS' => [
            'severity' => 'error',
            'title' => 'Wildcard Origin Forbidden with Credentials',
            // phpcs:ignore Generic.Files.LineLength
            'description' => "The response specifies 'Access-Control-Allow-Origin: *' while 'Access-Control-Allow-Credentials' is set to 'true'. Browsers reject this configuration for security reasons.",
        ],
        'ERR_CORS_ORIGIN_MISMATCH' => [
            'severity' => 'error',
            'title' => 'Origin Mismatch',
            'description' => "The 'Access-Control-Allow-Origin' header value does not match the request 'Origin'.",
        ],
        'ERR_CORS_MISSING_ALLOW_ORIGIN' => [
            'severity' => 'error',
            'title' => 'Missing Access-Control-Allow-Origin',
            'description' => "The server responded to a cross-origin request without an 'Access-Control-Allow-Origin' header.",
        ],
        'ERR_CORS_METHOD_DISALLOWED' => [
            'severity' => 'error',
            'title' => 'HTTP Method Disallowed by CORS',
            'description' => "The requested HTTP method is not permitted in 'Access-Control-Allow-Methods'.",
        ],
        'ERR_CORS_HEADER_DISALLOWED' => [
            'severity' => 'error',
            'title' => 'Header Not Allowed by CORS',
            'description' => "One or more custom request headers are not included in 'Access-Control-Allow-Headers'.",
        ],
        'WARN_CORS_MISSING_VARY_ORIGIN' => [
            'severity' => 'warning',
            'title' => "Missing 'Vary: Origin' Header",
            // phpcs:ignore Generic.Files.LineLength
            'description' => "When dynamically reflecting request Origin, omitting 'Vary: Origin' causes cache poisoning across CDNs and browser caches.",
        ],
        'WARN_CORS_EXCESSIVE_MAX_AGE' => [
            'severity' => 'warning',
            'title' => 'Access-Control-Max-Age Exceeds Browser Ceiling',
            // phpcs:ignore Generic.Files.LineLength
            'description' => 'Max-Age exceeds browser maximum limits (Chromium caps at 2 hours / 7200s, Safari at 10 minutes / 600s).',
        ],
        'WARN_CORS_UNEXPOSED_CUSTOM_HEADERS' => [
            'severity' => 'warning',
            'title' => 'Custom Response Headers Not Exposed',
            // phpcs:ignore Generic.Files.LineLength
            'description' => "Custom response headers are present on the response but not declared in 'Access-Control-Expose-Headers', preventing frontend JavaScript from reading them.",
        ],
        'INFO_CORS_PREFLIGHT_OK' => [
            'severity' => 'info',
            'title' => 'Preflight OPTIONS Succeeded',
            'description' => 'Preflight handshake conforms to W3C Fetch specification.',
        ],
    ];

    /**
     * @param array<string, mixed>|list<string> $requestHeaders
     * @param array<string, mixed>|list<string> $responseHeaders
     */
    public function analyze(
        string $requestOrigin,
        array $responseHeaders,
        bool $withCredentials = false,
        ?string $requestMethod = null,
        array $requestHeaders = [],
    ): CorsAnalysisResult {
        /** @var list<CorsDiagnostic> $diagnostics */
        $diagnostics = [];

        $normalizedReqHeaders = $this->normalizeHeaders($requestHeaders);
        $normalizedResHeaders = $this->normalizeHeaders($responseHeaders);

        $origin = trim($requestOrigin);
        if ($origin === '' && isset($normalizedReqHeaders['origin'])) {
            $origin = $normalizedReqHeaders['origin'];
        }

        $method = $requestMethod !== null && $requestMethod !== ''
            ? strtoupper(trim($requestMethod))
            : strtoupper($normalizedReqHeaders['access-control-request-method'] ?? '');

        // Check Access-Control-Allow-Origin
        $allowOrigin = $normalizedResHeaders['access-control-allow-origin'] ?? null;
        $allowCredentials = strtolower($normalizedResHeaders['access-control-allow-credentials'] ?? '') === 'true'
            || $withCredentials;

        if ($allowOrigin === null || trim($allowOrigin) === '') {
            $diagnostics[] = $this->createDiagnostic('ERR_CORS_MISSING_ALLOW_ORIGIN');
        } else {
            $trimmedAllowOrigin = trim($allowOrigin);

            if ($trimmedAllowOrigin === '*' && $allowCredentials) {
                $diagnostics[] = $this->createDiagnostic('ERR_CORS_WILDCARD_WITH_CREDENTIALS');
            } elseif ($trimmedAllowOrigin !== '*' && strcasecmp($trimmedAllowOrigin, $origin) !== 0) {
                $diagnostics[] = $this->createDiagnostic('ERR_CORS_ORIGIN_MISMATCH');
            }

            // Check Vary: Origin when dynamically echoing specific origin
            if ($trimmedAllowOrigin !== '*' && strcasecmp($trimmedAllowOrigin, $origin) === 0) {
                $varyHeader = $normalizedResHeaders['vary'] ?? '';
                $varyList = array_map('strtolower', array_map('trim', explode(',', $varyHeader)));
                if (!in_array('origin', $varyList, true) && !in_array('*', $varyList, true)) {
                    $diagnostics[] = $this->createDiagnostic('WARN_CORS_MISSING_VARY_ORIGIN');
                }
            }
        }

        // Check Access-Control-Allow-Methods
        if ($method !== '') {
            $allowMethodsHeader = $normalizedResHeaders['access-control-allow-methods'] ?? null;
            if ($allowMethodsHeader !== null) {
                $allowedMethods = array_map('strtoupper', array_map('trim', explode(',', $allowMethodsHeader)));
                if (!in_array('*', $allowedMethods, true) && !in_array($method, $allowedMethods, true)) {
                    $diagnostics[] = $this->createDiagnostic('ERR_CORS_METHOD_DISALLOWED');
                }
            }
        }

        // Check Access-Control-Allow-Headers
        $reqCustomHeaders = $this->extractCustomRequestHeaders($normalizedReqHeaders);
        if (!empty($reqCustomHeaders)) {
            $allowHeadersHeader = $normalizedResHeaders['access-control-allow-headers'] ?? null;
            if ($allowHeadersHeader === null) {
                $diagnostics[] = $this->createDiagnostic('ERR_CORS_HEADER_DISALLOWED');
            } else {
                $allowedHeaders = array_map('strtolower', array_map('trim', explode(',', $allowHeadersHeader)));
                if (!in_array('*', $allowedHeaders, true)) {
                    foreach ($reqCustomHeaders as $customHeader) {
                        if (!in_array(strtolower($customHeader), $allowedHeaders, true)) {
                            $diagnostics[] = $this->createDiagnostic('ERR_CORS_HEADER_DISALLOWED');
                            break;
                        }
                    }
                }
            }
        }

        // Check Access-Control-Max-Age
        if (isset($normalizedResHeaders['access-control-max-age'])) {
            $maxAge = (int) $normalizedResHeaders['access-control-max-age'];
            if ($maxAge > self::BROWSER_MAX_AGE_CEILINGS['chromium'] || $maxAge > self::BROWSER_MAX_AGE_CEILINGS['safari']) {
                $diagnostics[] = $this->createDiagnostic('WARN_CORS_EXCESSIVE_MAX_AGE');
            }
        }

        // Check Access-Control-Expose-Headers
        $customResponseHeaders = [];
        $exposeHeaders = array_map('strtolower', array_map('trim', explode(',', $normalizedResHeaders['access-control-expose-headers'] ?? '')));
        foreach ($normalizedResHeaders as $headerName => $headerValue) {
            if (!in_array($headerName, self::SAFELISTED_RESPONSE_HEADERS, true)) {
                if (!in_array('*', $exposeHeaders, true) && !in_array($headerName, $exposeHeaders, true)) {
                    $customResponseHeaders[] = $headerName;
                }
            }
        }
        if (!empty($customResponseHeaders)) {
            $diagnostics[] = $this->createDiagnostic('WARN_CORS_UNEXPOSED_CUSTOM_HEADERS');
        }

        // Check if Preflight Succeeded
        $hasErrors = false;
        foreach ($diagnostics as $d) {
            if ($d->severity === 'error') {
                $hasErrors = true;
                break;
            }
        }

        if (!$hasErrors) {
            $diagnostics[] = $this->createDiagnostic('INFO_CORS_PREFLIGHT_OK');
        }

        return new CorsAnalysisResult(
            isValid: !$hasErrors,
            diagnostics: $diagnostics,
            requestHeaders: $normalizedReqHeaders,
            responseHeaders: $normalizedResHeaders,
            browserCeilings: self::BROWSER_MAX_AGE_CEILINGS,
            requestOrigin: $origin,
            withCredentials: $withCredentials,
            requestMethod: $method !== '' ? $method : null,
        );
    }

    /**
     * @param array<string, mixed>|list<string> $headers
     * @return array<string, string>
     */
    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $key => $value) {
            if (is_int($key) && is_string($value)) {
                // Header in "Header-Name: Value" format
                $parts = explode(':', $value, 2);
                if (count($parts) === 2) {
                    $normalized[strtolower(trim($parts[0]))] = trim($parts[1]);
                } else {
                    $normalized[strtolower(trim($value))] = '';
                }
            } elseif (is_string($key)) {
                $normalized[strtolower(trim($key))] = is_string($value) ? trim($value) : (string) json_encode($value);
            }
        }

        return $normalized;
    }

    /**
     * @param array<string, string> $reqHeaders
     * @return list<string>
     */
    private function extractCustomRequestHeaders(array $reqHeaders): array
    {
        $custom = [];
        if (isset($reqHeaders['access-control-request-headers'])) {
            $headers = explode(',', $reqHeaders['access-control-request-headers']);
            foreach ($headers as $h) {
                $trimmed = trim($h);
                if ($trimmed !== '') {
                    $custom[] = $trimmed;
                }
            }
        }

        foreach ($reqHeaders as $name => $val) {
            if (
                !in_array($name, self::SAFELISTED_REQUEST_HEADERS, true)
                && !str_starts_with($name, 'access-control-')
                && $name !== 'origin'
                && $name !== 'host'
            ) {
                $custom[] = $name;
            }
        }

        return array_values(array_unique($custom));
    }

    private function createDiagnostic(string $code): CorsDiagnostic
    {
        $def = self::DIAGNOSTIC_DEFINITIONS[$code] ?? [
            'severity' => 'info',
            'title' => $code,
            'description' => $code,
        ];

        return new CorsDiagnostic(
            code: $code,
            severity: $def['severity'],
            title: $def['title'],
            description: $def['description'],
        );
    }
}
