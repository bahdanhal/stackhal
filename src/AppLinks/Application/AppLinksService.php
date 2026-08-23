<?php

declare(strict_types=1);

namespace App\AppLinks\Application;

use App\AppLinks\Domain\Engine\AppLinksValidator;
use App\AppLinks\Domain\Model\AppLinksResult;
use App\Shared\Infrastructure\Http\SafeHttpFetcher;

final readonly class AppLinksService
{
    private AppLinksValidator $validator;

    public function __construct(
        ?AppLinksValidator $validator = null,
        private ?SafeHttpFetcher $httpFetcher = null,
    ) {
        $this->validator = $validator ?? new AppLinksValidator();
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
            return $this->validator->validate('{}', null, $testUrl, $cleanDomain);
        }

        // Mock test scenarios or live fetch
        if ($this->httpFetcher === null) {
            $presets = $this->getPresets();
            $aasa = json_encode($presets[0]['aasa_content'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $assetLinksEncoded = json_encode($presets[1]['assetlinks_content'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $assetLinks = is_string($assetLinksEncoded) ? $assetLinksEncoded : null;

            return $this->validator->validate($aasa ?: '{}', $assetLinks, $testUrl, $cleanDomain);
        }

        $aasaUrl = 'https://' . $cleanDomain . '/.well-known/apple-app-site-association';
        $assetLinksUrl = 'https://' . $cleanDomain . '/.well-known/assetlinks.json';

        $aasaResponse = $this->httpFetcher->fetch($aasaUrl, maxRedirects: 0);
        $assetLinksResponse = $this->httpFetcher->fetch($assetLinksUrl, maxRedirects: 3);

        $aasaContent = $aasaResponse['body'];
        $assetLinksContent = $assetLinksResponse['body'];

        return $this->validator->validate(
            $aasaContent !== '' ? $aasaContent : '{}',
            $assetLinksContent !== '' ? $assetLinksContent : null,
            $testUrl,
            $cleanDomain
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
