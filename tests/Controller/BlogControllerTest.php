<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use App\Blog\Presentation\Http\BlogController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class BlogControllerTest extends TestCase
{
    public function testIndexRedirectsToEnglishWhenPolishBlogHasNoArticles(): void
    {
        $repo = $this->createMock(BlogArticleRepository::class);
        $repo->expects(self::once())
            ->method('findPublished')
            ->with('pl')
            ->willReturn([]);

        $router = $this->createMock(RouterInterface::class);
        $router->expects(self::once())
            ->method('generate')
            ->with('blog_index', ['_locale' => 'en'])
            ->willReturn('/blog');

        $container = new Container();
        $container->set('router', $router);

        $controller = new BlogController();
        $controller->setContainer($container);

        $request = new Request();
        $request->setLocale('pl');

        $response = $controller->index($request, $repo);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertSame('/blog', $response->getTargetUrl());
    }

    public function testIndexRendersWhenLocaleHasArticles(): void
    {
        $article = $this->mockArticle('test-slug');
        $repo = $this->createMock(BlogArticleRepository::class);
        $repo->expects(self::once())
            ->method('findPublished')
            ->with('en')
            ->willReturn([$article]);

        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with('blog/index.html.twig', self::callback(static fn (array $data): bool => array_key_exists('articles', $data)))
            ->willReturn('<html>blog index</html>');

        $container = new Container();
        $container->set('twig', $twig);

        $controller = new BlogController();
        $controller->setContainer($container);

        $request = new Request();
        $request->setLocale('en');

        $response = $controller->index($request, $repo);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('<html>blog index</html>', $response->getContent());
    }

    public function testArticleRedirectsToEnglishWhenPolishArticleNotFoundButEnglishExists(): void
    {
        $enArticle = $this->mockArticle('pkpass-signature-errors');

        $repo = $this->createMock(BlogArticleRepository::class);
        $repo->expects(self::exactly(2))
            ->method('findPublishedBySlug')
            ->willReturnCallback(static function (string $slug, string $locale) use ($enArticle): ?BlogArticle {
                if ($slug === 'pkpass-signature-errors' && $locale === 'pl') {
                    return null;
                }
                if ($slug === 'pkpass-signature-errors' && $locale === 'en') {
                    return $enArticle;
                }
                return null;
            });

        $router = $this->createMock(RouterInterface::class);
        $router->expects(self::once())
            ->method('generate')
            ->with('blog_article', ['slug' => 'pkpass-signature-errors', '_locale' => 'en'])
            ->willReturn('/blog/pkpass-signature-errors');

        $container = new Container();
        $container->set('router', $router);

        $controller = new BlogController();
        $controller->setContainer($container);

        $request = new Request();
        $request->setLocale('pl');

        $response = $controller->article('pkpass-signature-errors', $request, $repo);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertSame('/blog/pkpass-signature-errors', $response->getTargetUrl());
    }

    public function testArticleRedirectsToPolishSlugWhenEnglishHasAlternateSlug(): void
    {
        $enArticle = $this->mockArticle('pkpass-signature-errors', 'en', 'bledy-podpisu-pkpass');
        $plArticle = $this->mockArticle('bledy-podpisu-pkpass', 'pl', 'pkpass-signature-errors');

        $repo = $this->createMock(BlogArticleRepository::class);
        $repo->expects(self::exactly(3))
            ->method('findPublishedBySlug')
            ->willReturnCallback(static function (string $slug, string $locale) use ($enArticle, $plArticle): ?BlogArticle {
                if ($slug === 'pkpass-signature-errors' && $locale === 'pl') {
                    return null;
                }
                if ($slug === 'pkpass-signature-errors' && $locale === 'en') {
                    return $enArticle;
                }
                if ($slug === 'bledy-podpisu-pkpass' && $locale === 'pl') {
                    return $plArticle;
                }
                return null;
            });

        $router = $this->createMock(RouterInterface::class);
        $router->expects(self::once())
            ->method('generate')
            ->with('blog_article', ['slug' => 'bledy-podpisu-pkpass', '_locale' => 'pl'])
            ->willReturn('/pl/blog/bledy-podpisu-pkpass');

        $container = new Container();
        $container->set('router', $router);

        $controller = new BlogController();
        $controller->setContainer($container);

        $request = new Request();
        $request->setLocale('pl');

        $response = $controller->article('pkpass-signature-errors', $request, $repo);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertSame('/pl/blog/bledy-podpisu-pkpass', $response->getTargetUrl());
    }

    public function testArticleThrows404WhenArticleDoesNotExistInAnyLocale(): void
    {
        $repo = $this->createMock(BlogArticleRepository::class);
        $repo->expects(self::exactly(2))
            ->method('findPublishedBySlug')
            ->willReturn(null);

        $controller = new BlogController();

        $request = new Request();
        $request->setLocale('pl');

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Blog article not found.');

        $controller->article('non-existent', $request, $repo);
    }

    public function testArticleRendersWhenFound(): void
    {
        $article = $this->mockArticle('available');
        $repo = $this->createMock(BlogArticleRepository::class);
        $repo->expects(self::once())
            ->method('findPublishedBySlug')
            ->with('available', 'en')
            ->willReturn($article);

        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with('blog/article.html.twig', ['article' => $article])
            ->willReturn('<html>article content</html>');

        $container = new Container();
        $container->set('twig', $twig);

        $controller = new BlogController();
        $controller->setContainer($container);

        $request = new Request();
        $request->setLocale('en');

        $response = $controller->article('available', $request, $repo);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('<html>article content</html>', $response->getContent());
    }

    private function mockArticle(string $slug, string $locale = 'en', string $alternateSlug = ''): BlogArticle
    {
        $now = new \DateTimeImmutable();
        return new BlogArticle(
            $slug,
            'Title',
            'Description',
            'Category',
            5,
            $now,
            $now,
            '<p>Content</p>',
            'CTA',
            '/cta',
            'visual',
            ['line1'],
            [['name' => 'step1', 'text' => 'text1']],
            $locale,
            $alternateSlug
        );
    }
}
