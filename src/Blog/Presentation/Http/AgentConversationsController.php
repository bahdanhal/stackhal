<?php

declare(strict_types=1);

namespace App\Blog\Presentation\Http;

use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AgentConversationsController extends AbstractController
{
    #[Route(
        path: ['en' => '/agent-conversations', 'pl' => '/pl/rozmowy-agentow'],
        name: 'agent_conversations',
        methods: ['GET']
    )]
    public function index(Request $request, BlogArticleRepository $articles): Response
    {
        return $this->render('digest/index.html.twig', [
            'articles' => $articles->findPublished($request->getLocale(), BlogArticle::AGENT_CONVERSATIONS_CATEGORY),
        ]);
    }

    #[Route(
        path: ['en' => '/agent-conversations/feed.xml', 'pl' => '/pl/rozmowy-agentow/feed.xml'],
        name: 'agent_conversations_feed',
        methods: ['GET']
    )]
    public function feed(Request $request, BlogArticleRepository $articles): Response
    {
        $editions = $articles->findPublished($request->getLocale(), BlogArticle::AGENT_CONVERSATIONS_CATEGORY);

        return $this->render('digest/feed.xml.twig', [
            'articles' => array_slice($editions, 0, 30),
        ], new Response('', Response::HTTP_OK, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300, must-revalidate',
        ]));
    }
}
