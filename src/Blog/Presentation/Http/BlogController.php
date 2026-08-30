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
        return $this->render('blog/index.html.twig', [
            'articles' => $articles->findPublished($request->getLocale()),
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
        $article = $articles->findPublishedBySlug($slug, $request->getLocale());
        if ($article === null) {
            throw new NotFoundHttpException('Blog article not found.');
        }

        return $this->render('blog/article.html.twig', ['article' => $article]);
    }
}
