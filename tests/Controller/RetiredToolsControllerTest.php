<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Shared\Presentation\Http\RetiredToolsController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class RetiredToolsControllerTest extends TestCase
{
    public function testReturns410GoneWithNoindexHeader(): void
    {
        $controller = new RetiredToolsController();
        $response = $controller();

        self::assertSame(Response::HTTP_GONE, $response->getStatusCode());
        self::assertSame('noindex, nofollow', $response->headers->get('X-Robots-Tag'));
    }
}
