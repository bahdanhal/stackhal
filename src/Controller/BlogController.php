<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('blog/index.html.twig');
    }

    #[Route('/blog/bimi-not-working', name: 'blog_bimi_not_working', methods: ['GET'])]
    public function bimiNotWorking(): Response
    {
        return $this->render('blog/bimi-not-working.html.twig');
    }
}
