<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Admin\Presentation\Http\AdminBlogController;
use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use App\Entity\BlogArticleEntity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class AdminBlogControllerTest extends TestCase
{
    private string $secret = 'test-secret-key';

    public function testRedirectsToDashboardWhenUnauthenticated(): void
    {
        $repo = $this->createStub(BlogArticleRepository::class);
        $router = $this->createMock(RouterInterface::class);
        $router->expects(self::once())
            ->method('generate')
            ->with('stackhal_admin_dashboard')
            ->willReturn('/admin');

        $container = new Container();
        $container->set('router', $router);

        $controller = new AdminBlogController($repo, $this->secret);
        $controller->setContainer($container);

        $request = new Request();
        $response = $controller->list($request);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('/admin', $response->getTargetUrl());
    }

    public function testRendersListWhenAuthenticated(): void
    {
        $repo = $this->createMock(BlogArticleRepository::class);
        $repo->expects(self::once())
            ->method('findAllForAdmin')
            ->with(null)
            ->willReturn([]);

        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with('admin/blog/index.html.twig', self::callback(static fn (array $data): bool => array_key_exists('articles', $data)))
            ->willReturn('<html>list</html>');

        $container = new Container();
        $container->set('twig', $twig);

        $controller = new AdminBlogController($repo, $this->secret);
        $controller->setContainer($container);

        $request = new Request();
        $request->headers->set('X-Admin-Token', $this->secret);

        $response = $controller->list($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('<html>list</html>', $response->getContent());
    }

    public function testPreviewEndpointCalculatesReadability(): void
    {
        $repo = $this->createStub(BlogArticleRepository::class);
        $controller = new AdminBlogController($repo, $this->secret);

        $request = new Request([], ['content' => '<p>Simple text for testing. Easy to read and fast.</p>']);
        $request->headers->set('X-Admin-Token', $this->secret);

        $response = $controller->preview($request);
        self::assertSame(200, $response->getStatusCode());

        $data = json_decode((string) $response->getContent(), true);
        self::assertArrayHasKey('stats', $data);
        self::assertGreaterThanOrEqual(50, $data['stats']['flesch_reading_ease']);
    }
}
