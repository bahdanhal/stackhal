<?php

declare(strict_types=1);

namespace App\Blog\Presentation\Http;

use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AgentConversationsController extends AbstractController
{
    #[Route('/agent-conversations', name: 'agent_conversations', methods: ['GET'])]
    public function index(BlogArticleRepository $articles): Response
    {
        return $this->render('digest/index.html.twig', [
            'articles' => $articles->findPublished('en', BlogArticle::AGENT_CONVERSATIONS_CATEGORY),
        ]);
    }

    #[Route('/agent-conversations/feed.xml', name: 'agent_conversations_feed', methods: ['GET'])]
    public function feed(BlogArticleRepository $articles): Response
    {
        $editions = $articles->findPublished('en', BlogArticle::AGENT_CONVERSATIONS_CATEGORY);

        return $this->render('digest/feed.xml.twig', [
            'articles' => array_slice($editions, 0, 30),
        ], new Response('', Response::HTTP_OK, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300, must-revalidate',
        ]));
    }
}
