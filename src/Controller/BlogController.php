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

    #[Route('/blog/nginx-to-caddy', name: 'blog_nginx_to_caddy', methods: ['GET'])]
    public function nginxToCaddy(): Response
    {
        return $this->render('blog/nginx-to-caddy.html.twig');
    }

    #[Route('/blog/dns-delegation-explained', name: 'blog_dns_delegation_explained', methods: ['GET'])]
    public function dnsDelegationExplained(): Response
    {
        return $this->render('blog/dns-delegation-explained.html.twig');
    }

    #[Route('/blog/pkpass-signature-errors', name: 'blog_pkpass_signature_errors', methods: ['GET'])]
    public function pkpassSignatureErrors(): Response
    {
        return $this->render('blog/pkpass-signature-errors.html.twig');
    }
}
