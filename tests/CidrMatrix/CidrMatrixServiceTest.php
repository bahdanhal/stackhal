<?php

declare(strict_types=1);

namespace App\Tests\CidrMatrix;

use App\CidrMatrix\Application\CidrMatrixService;
use PHPUnit\Framework\TestCase;

final class CidrMatrixServiceTest extends TestCase
{
    public function testExtractsCidrsFromTerraformAndJsonWithoutManualCleanup(): void
    {
        $input = <<<'TEXT'
resource "example" "network" {
  cidr_block = "10.10.0.0/16"
  routes = ["10.10.4.0/24", "2001:db8::/48"]
}
TEXT;

        $service = new CidrMatrixService();

        self::assertSame(
            ['10.10.0.0/16', '10.10.4.0/24', '2001:db8::/48'],
            $service->extractCidrs($input)
        );
    }

    public function testSuggestsPrefixOnlyForOneAddressFamily(): void
    {
        $service = new CidrMatrixService();

        self::assertSame(24, $service->suggestFreePrefix(['10.0.0.0/16', '10.0.4.0/24']));
        self::assertNull($service->suggestFreePrefix(['10.0.0.0/24', '2001:db8::/64']));
    }
}
