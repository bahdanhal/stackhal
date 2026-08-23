<?php

declare(strict_types=1);

namespace App\Tests\AppLinks;

use App\AppLinks\Domain\Engine\AppLinksValidator;
use PHPUnit\Framework\TestCase;

final class AppLinksValidatorTest extends TestCase
{
    private AppLinksValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new AppLinksValidator();
    }

    public function testValidAasaRouteMatching(): void
    {
        $manifest = [
            'applinks' => [
                'details' => [
                    [
                        'appIDs' => ['ABCDE12345.com.example.app'],
                        'components' => [
                            ['/' => '/product/*'],
                            ['/' => '/checkout/*', 'exclude' => true],
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->validator->validate($manifest, testUrl: 'https://example.com/product/iphone-16-pro');

        self::assertTrue($result->isValid);
        self::assertTrue($result->opensInApp);
        self::assertSame('/product/*', $result->matchedPattern);
        self::assertFalse($result->matchedExclusion);
        self::assertContains('INFO_ROUTE_MATCHED_APP', $result->getInfoCodes());
    }

    public function testAasaRouteExclusion(): void
    {
        $manifest = [
            'applinks' => [
                'details' => [
                    [
                        'appIDs' => ['ABCDE12345.com.example.app'],
                        'components' => [
                            ['/' => '/product/*'],
                            ['/' => '/checkout/*', 'exclude' => true],
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->validator->validate($manifest, testUrl: 'https://example.com/checkout/step1');

        self::assertTrue($result->isValid);
        self::assertFalse($result->opensInApp);
        self::assertTrue($result->matchedExclusion);
        self::assertSame('/checkout/*', $result->matchedPattern);
        self::assertContains('INFO_ROUTE_FALLS_BACK_WEB', $result->getInfoCodes());
    }

    public function testInvalidAppIdDetection(): void
    {
        $manifest = [
            'applinks' => [
                'details' => [
                    [
                        'appIDs' => ['invalid-app-id'],
                        'components' => [['/' => '/*']],
                    ],
                ],
            ],
        ];

        $result = $this->validator->validate($manifest);

        self::assertFalse($result->isValid);
        self::assertContains('ERR_AASA_INVALID_APP_ID', $result->getErrorCodes());
    }

    public function testAndroidAssetLinksValid(): void
    {
        $assetLinks = [
            [
                'relation' => ['delegate_permission/common.handle_all_urls'],
                'target' => [
                    'namespace' => 'android_app',
                    'package_name' => 'com.example.store',
                    'sha256_cert_fingerprints' => [
                        '14:6D:E9:DE:0F:45:79:F6:10:5A:12:60:2B:93:FC:7F:16:17:D6:31:02:61:00:EC:4F:60:9E:78:21:C6:0F:C0',
                    ],
                ],
            ],
        ];

        $result = $this->validator->validate('{}', $assetLinks);

        self::assertTrue($result->isValid);
        self::assertTrue($result->assetLinksValid);
        self::assertSame(['com.example.store'], $result->androidPackageNames);
    }

    public function testAndroidAssetLinksMissingRelationAndBadFingerprint(): void
    {
        $assetLinks = [
            [
                'relation' => ['other_permission'],
                'target' => [
                    'namespace' => 'android_app',
                    'package_name' => 'com.example.store',
                    'sha256_cert_fingerprints' => ['00:11:INVALID'],
                ],
            ],
        ];

        $result = $this->validator->validate('{}', $assetLinks);

        self::assertFalse($result->isValid);
        self::assertFalse($result->assetLinksValid);
        self::assertContains('ERR_ASSETLINKS_MISSING_RELATION', $result->getErrorCodes());
        self::assertContains('ERR_ASSETLINKS_INVALID_FINGERPRINT', $result->getErrorCodes());
    }
}
