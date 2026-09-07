<?php

declare(strict_types=1);

namespace App\Blog\Presentation\Http;

use App\Blog\Application\BlogArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    #[Route(path: ['en' => '/blog', 'pl' => '/pl/blog'], name: 'blog_index', methods: ['GET'])]
    public function index(Request $request, BlogArticleRepository $articles): Response
    {
        $locale = $request->getLocale();
        $published = $articles->findPublished($locale);

        if ($locale !== 'en' && $published === []) {
            return $this->redirectToRoute('blog_index', ['_locale' => 'en'], Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->render('blog/index.html.twig', [
            'articles' => $published,
        ]);
    }

    #[Route(
        path: ['en' => '/blog/{slug}', 'pl' => '/pl/blog/{slug}'],
        name: 'blog_article',
        requirements: ['slug' => '[a-z0-9-]+'],
        methods: ['GET']
    )]
    public function article(string $slug, Request $request, BlogArticleRepository $articles): Response
    {
        $locale = $request->getLocale();
        $article = $articles->findPublishedBySlug($slug, $locale);

        if ($article === null) {
            if ($locale !== 'en') {
                $enArticle = $articles->findPublishedBySlug($slug, 'en');
                if ($enArticle !== null) {
                    if ($enArticle->getAlternateSlug() !== '') {
                        $plArticle = $articles->findPublishedBySlug($enArticle->getAlternateSlug(), $locale);
                        if ($plArticle !== null) {
                            return $this->redirectToRoute(
                                'blog_article',
                                ['slug' => $plArticle->getSlug(), '_locale' => $locale],
                                Response::HTTP_MOVED_PERMANENTLY
                            );
                        }
                    }

                    return $this->redirectToRoute(
                        'blog_article',
                        ['slug' => $slug, '_locale' => 'en'],
                        Response::HTTP_MOVED_PERMANENTLY
                    );
                }
            }

            throw new NotFoundHttpException('Blog article not found.');
        }

        return $this->render('blog/article.html.twig', ['article' => $article]);
    }
}
