<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\AppLinks\Application\AppLinksService;
use App\Mcp\AppLinksTools;
use PHPUnit\Framework\TestCase;

final class AppLinksToolsTest extends TestCase
{
    private AppLinksTools $tools;

    protected function setUp(): void
    {
        $this->tools = new AppLinksTools(new AppLinksService());
    }

    public function testValidateAppLinksToolSuccess(): void
    {
        $response = $this->tools->validateAppLinks('example.com', 'https://example.com/products/summer-sale');

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertTrue($data['result']['is_valid']);
        self::assertTrue($data['result']['opens_in_app']);
        self::assertSame('/products/*', $data['result']['matched_pattern']);
        self::assertNotEmpty($data['result']['diagnostics']);
    }

    public function testValidateAppLinksToolEmptyDomain(): void
    {
        $response = $this->tools->validateAppLinks('');

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('completed', $data['status']);
        self::assertTrue($data['result']['is_valid']);
    }
}
