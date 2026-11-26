<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use App\Blog\Presentation\Http\BlogController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class BlogControllerTest extends TestCase
{
    public function testIndexRendersEnglishArticles(): void
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
            ->with('blog/index.html.twig', [
                'articles' => [$article],
            ])
            ->willReturn('<html>blog index</html>');

        $container = new Container();
        $container->set('twig', $twig);

        $controller = new BlogController();
        $controller->setContainer($container);

        $response = $controller->index($repo);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('<html>blog index</html>', $response->getContent());
    }

    public function testArticleThrows404WhenNotFound(): void
    {
        $repo = $this->createMock(BlogArticleRepository::class);
        $repo->expects(self::once())
            ->method('findPublishedBySlug')
            ->with('non-existent', 'en')
            ->willReturn(null);

        $controller = new BlogController();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Blog article not found.');

        $controller->article('non-existent', $repo);
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
            ->with('blog/article.html.twig', [
                'article' => $article,
            ])
            ->willReturn('<html>article content</html>');

        $container = new Container();
        $container->set('twig', $twig);

        $controller = new BlogController();
        $controller->setContainer($container);

        $response = $controller->article('available', $repo);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('<html>article content</html>', $response->getContent());
    }

    private function mockArticle(string $slug): BlogArticle
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
            'en',
            ''
        );
    }
}
