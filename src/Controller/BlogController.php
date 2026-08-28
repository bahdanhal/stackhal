<?php

declare(strict_types=1);

namespace App\Controller;

use App\Blog\Infrastructure\DoctrineBlogArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog_index', methods: ['GET'])]
    public function index(DoctrineBlogArticleRepository $articles): Response
    {
        return $this->render('blog/index.html.twig', ['articles' => $articles->findPublished()]);
    }

    #[Route('/blog/{slug}', name: 'blog_article', requirements: ['slug' => '[a-z0-9-]+'], methods: ['GET'])]
    public function article(string $slug, DoctrineBlogArticleRepository $articles): Response
    {
        $article = $articles->findPublishedBySlug($slug);
        if ($article === null) {
            throw new NotFoundHttpException('Blog article not found.');
        }

        return $this->render('blog/article.html.twig', ['article' => $article]);
    }
}
