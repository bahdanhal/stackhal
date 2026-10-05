<?php

declare(strict_types=1);

namespace App\AppLinks\Application;

use App\AppLinks\Domain\Engine\AppleCdnComparator;
use App\AppLinks\Domain\Engine\AppLinksValidator;
use App\AppLinks\Domain\Model\AppleCdnReport;
use App\AppLinks\Domain\Model\AppLinksDiagnostic;
use App\AppLinks\Domain\Model\AppLinksResult;
use App\Shared\Application\HttpFetcher;

final readonly class AppLinksService
{
    private AppLinksValidator $validator;
    private AppleCdnComparator $cdnComparator;

    public function __construct(
        ?AppLinksValidator $validator = null,
        private ?HttpFetcher $httpFetcher = null,
        ?AppleCdnComparator $cdnComparator = null,
    ) {
        $this->validator = $validator ?? new AppLinksValidator();
        $this->cdnComparator = $cdnComparator ?? new AppleCdnComparator();
    }

    /**
     * @param array<string, mixed>|string $aasa
     * @param array<int, mixed>|string|null $assetLinks
     */
    public function validate(
        array|string $aasa,
        array|string|null $assetLinks = null,
        ?string $testUrl = null,
        ?string $domain = null,
    ): AppLinksResult {
        return $this->validator->validate($aasa, $assetLinks, $testUrl, $domain);
    }

    public function validateDomain(string $domain, ?string $testUrl = null): AppLinksResult
    {
        $cleanDomain = strtolower(trim($domain, " /\t\n\r\0\x0B"));
        if ($cleanDomain === '') {
            return $this->validator->validate('', null, $testUrl, $cleanDomain, true, true);
        }

        if ($this->httpFetcher === null) {
            return $this->validator->validate('', null, $testUrl, $cleanDomain, true, true);
        }

        $aasaUrl = 'https://' . $cleanDomain . '/.well-known/apple-app-site-association';
        $assetLinksUrl = 'https://' . $cleanDomain . '/.well-known/assetlinks.json';

        try {
            $aasaResponse = $this->httpFetcher->fetch($aasaUrl, maxRedirects: 0);
            $assetLinksResponse = $this->httpFetcher->fetch($assetLinksUrl, maxRedirects: 3);
        } catch (\Throwable) {
            return $this->validator->validate('', null, $testUrl, $cleanDomain, true, true);
        }

        $aasaContent = $aasaResponse['status'] === 200 && $aasaResponse['error'] === null
            ? $aasaResponse['body']
            : '';
        $assetLinksContent = $assetLinksResponse['status'] === 200 && $assetLinksResponse['error'] === null
            ? $assetLinksResponse['body']
            : null;

        $result = $this->validator->validate(
            $aasaContent,
            $assetLinksContent !== '' ? $assetLinksContent : null,
            $testUrl,
            $cleanDomain,
            true,
            true
        );

        $extraDiagnostics = [];
        if ($aasaResponse['status'] >= 300 && $aasaResponse['status'] < 400) {
            $extraDiagnostics[] = new AppLinksDiagnostic(
                code: 'ERR_AASA_REDIRECT_FORBIDDEN',
                severity: 'error',
                title: 'HTTP Redirect Forbidden on AASA',
                description: 'The AASA well-known URL redirected instead of returning the file directly.'
            );
        }
        foreach ([$aasaResponse, $assetLinksResponse] as $response) {
            $contentType = strtolower(trim(explode(';', $response['content_type'])[0]));
            if ($response['status'] === 200 && !in_array($contentType, ['application/json', 'application/pkcs7-mime'], true)) {
                $extraDiagnostics[] = new AppLinksDiagnostic(
                    code: 'WARN_CONTENT_TYPE_MISMATCH',
                    severity: 'warning',
                    title: 'Non-Standard Content-Type',
                    description: sprintf('The manifest was served as "%s" instead of a recognized JSON content type.', $contentType)
                );
            }
        }

        $cdnReport = $this->appleCdnReport($cleanDomain, $aasaContent !== '', $aasaContent);
        if ($cdnReport !== null) {
            $extraDiagnostics = [...$extraDiagnostics, ...$cdnReport->diagnostics];
        }

        if ($extraDiagnostics === []) {
            return $result;
        }

        return new AppLinksResult(
            isValid: $result->isValid && !array_any(
                $extraDiagnostics,
                static fn (AppLinksDiagnostic $diagnostic): bool => $diagnostic->severity === 'error'
            ),
            opensInApp: $result->opensInApp,
            matchedPattern: $result->matchedPattern,
            matchedExclusion: $result->matchedExclusion,
            diagnostics: [...$result->diagnostics, ...$extraDiagnostics],
            aasaValid: $result->aasaValid,
            assetLinksValid: $result->assetLinksValid,
            aasaAppIds: $result->aasaAppIds,
            androidPackageNames: $result->androidPackageNames,
            testUrl: $result->testUrl,
            domain: $result->domain,
            aasaRaw: $result->aasaRaw,
            assetLinksRaw: $result->assetLinksRaw,
            appleCdn: $cdnReport,
        );
    }

    private function appleCdnReport(string $domain, bool $originServed, string $originBody): ?AppleCdnReport
    {
        $cdnUrl = $this->cdnComparator->cdnUrl($domain);
        if ($cdnUrl === null || $this->httpFetcher === null) {
            return null;
        }

        $originUrl = $this->cdnComparator->originUrl($domain);

        try {
            $response = $this->httpFetcher->fetch($cdnUrl, maxRedirects: 0);
        } catch (\Throwable $exception) {
            return $this->cdnComparator->compare($cdnUrl, $originUrl, $originServed, $originBody, 0, '', [], $exception->getMessage());
        }

        // Apple may have followed a redirect or used the legacy root path; compare against the URL it reports.
        $alternate = !$originServed && $response['status'] === 200 && $response['error'] === null
            ? $this->cdnComparator->alternateSource($domain, $response['headers'])
            : null;
        if ($alternate !== null) {
            try {
                $alternateResponse = $this->httpFetcher->fetch($alternate, maxRedirects: 0);
            } catch (\Throwable) {
                $alternateResponse = null;
            }
            if (
                $alternateResponse !== null
                && $alternateResponse['status'] === 200
                && $alternateResponse['error'] === null
                && $alternateResponse['body'] !== ''
            ) {
                $originUrl = $alternate;
                $originServed = true;
                $originBody = $alternateResponse['body'];
            }
        }

        return $this->cdnComparator->compare(
            $cdnUrl,
            $originUrl,
            $originServed,
            $originBody,
            $response['status'],
            $response['body'],
            $response['headers'],
            $response['error'],
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getPresets(): array
    {
        return [
            [
                'id' => 'ecommerce_app_routing',
                'name' => 'E-Commerce App (Product & Category in App, Checkout in Web)',
                'aasa_content' => [
                    'applinks' => [
                        'apps' => [],
                        'details' => [
                            [
                                'appIDs' => ['ABCDE12345.com.example.store'],
                                'components' => [
                                    ['/' => '/products/*', 'comment' => 'Open product pages in app'],
                                    ['/' => '/categories/*', 'comment' => 'Open category listings in app'],
                                    // phpcs:ignore Generic.Files.LineLength
                                    ['/' => '/checkout/*', 'exclude' => true, 'comment' => 'Keep checkout web-based for security'],
                                    ['/' => '/login*', 'exclude' => true, 'comment' => 'Keep OAuth and login in browser'],
                                ],
                            ],
                        ],
                    ],
                ],
                'test_url' => 'https://example.com/products/wireless-headphones',
            ],
            [
                'id' => 'android_verified_package',
                'name' => 'Android Digital Asset Links Verified Package',
                'assetlinks_content' => [
                    [
                        'relation' => ['delegate_permission/common.handle_all_urls'],
                        'target' => [
                            'namespace' => 'android_app',
                            'package_name' => 'com.example.store',
                            'sha256_cert_fingerprints' => [
                                // phpcs:ignore Generic.Files.LineLength
                                '14:6D:E9:DE:0F:45:79:F6:10:5A:12:60:2B:93:FC:7F:16:17:D6:31:02:61:00:EC:4F:60:9E:78:21:C6:0F:C0',
                            ],
                        ],
                    ],
                ],
                'test_url' => 'https://example.com/products/summer-sale',
            ],
        ];
    }
}
