<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Http;

use App\Blog\Application\BlogArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class PolishLegacyRedirectController extends AbstractController
{
    private const STATIC_REDIRECTS = [
        '/pl' => '/',
        '/pl/' => '/',
        '/pl/narzedzia' => '/',
        '/pl/audyt-geo' => '/geo-audit',
        '/pl/narzedzia/audyt-geo' => '/geo-audit',
        '/pl/audyt-seo' => '/seo-audit',
        '/pl/narzedzia/audyt-seo' => '/seo-audit',
        '/pl/inspektor-pkpass' => '/apple-pkpass-inspector',
        '/pl/narzedzia/inspektor-pkpass' => '/apple-pkpass-inspector',
        '/pl/synchronizacja-plikow-ai-studio' => '/ai-studio-local-file-sync',
        '/pl/rozmowy-agentow' => '/agent-conversations',
        '/pl/rozmowy-agentow/feed.xml' => '/agent-conversations/feed.xml',
        '/pl/blog' => '/blog',
    ];

    private const BLOG_SLUG_REDIRECTS = [
        'o-czym-rozmawiaja-agenci-2026-10-02' => 'what-agents-are-talking-about-2026-10-02',
    ];

    #[Route('/pl', name: 'legacy_pl_root', methods: ['GET'])]
    #[Route('/pl/', name: 'legacy_pl_home', methods: ['GET'])]
    #[Route('/pl/narzedzia', name: 'legacy_pl_tools', methods: ['GET'])]
    #[Route('/pl/audyt-geo', name: 'legacy_pl_geo_audit', methods: ['GET'])]
    #[Route('/pl/narzedzia/audyt-geo', name: 'legacy_pl_tools_geo_audit', methods: ['GET'])]
    #[Route('/pl/audyt-seo', name: 'legacy_pl_seo_audit', methods: ['GET'])]
    #[Route('/pl/narzedzia/audyt-seo', name: 'legacy_pl_tools_seo_audit', methods: ['GET'])]
    #[Route('/pl/inspektor-pkpass', name: 'legacy_pl_pkpass_inspector', methods: ['GET'])]
    #[Route('/pl/narzedzia/inspektor-pkpass', name: 'legacy_pl_tools_pkpass_inspector', methods: ['GET'])]
    #[Route('/pl/synchronizacja-plikow-ai-studio', name: 'legacy_pl_ai_studio_extension', methods: ['GET'])]
    #[Route('/pl/rozmowy-agentow', name: 'legacy_pl_agent_conversations', methods: ['GET'])]
    #[Route('/pl/rozmowy-agentow/feed.xml', name: 'legacy_pl_agent_conversations_feed', methods: ['GET'])]
    #[Route('/pl/blog', name: 'legacy_pl_blog_index', methods: ['GET'])]
    public function redirectStatic(Request $request): RedirectResponse
    {
        $target = self::STATIC_REDIRECTS[$request->getPathInfo()] ?? '/';

        return new RedirectResponse($target, Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route(
        '/pl/blog/{slug}',
        name: 'legacy_pl_blog_article',
        requirements: ['slug' => '[a-z0-9-]+'],
        methods: ['GET']
    )]
    public function redirectBlogArticle(string $slug, BlogArticleRepository $articles): RedirectResponse
    {
        $mappedSlug = self::BLOG_SLUG_REDIRECTS[$slug] ?? $slug;
        if ($articles->findPublishedBySlug($mappedSlug, 'en') !== null) {
            return new RedirectResponse('/blog/' . $mappedSlug, Response::HTTP_MOVED_PERMANENTLY);
        }

        $polishArticle = $articles->findPublishedBySlug($slug, 'pl');
        if ($polishArticle !== null && $polishArticle->getAlternateSlug() !== '') {
            $englishSlug = $polishArticle->getAlternateSlug();
            if ($articles->findPublishedBySlug($englishSlug, 'en') !== null) {
                return new RedirectResponse('/blog/' . $englishSlug, Response::HTTP_MOVED_PERMANENTLY);
            }
        }

        throw new NotFoundHttpException('Blog article not found.');
    }
}
