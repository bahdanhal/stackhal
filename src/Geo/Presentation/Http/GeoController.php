<?php

declare(strict_types=1);

namespace App\Geo\Presentation\Http;

use App\Geo\Application\GeoAnalyzer;
use App\Shared\Application\DailyQuota;
use App\Shared\Domain\UnsafeUrlException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class GeoController extends AbstractController
{
    public function __construct(
        private readonly GeoAnalyzer $analyzer,
        private readonly DailyQuota $auditQuota,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/geo-audit', name: 'geo_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('geo/home.html.twig');
    }

    #[Route('/tools/geo-audit', name: 'legacy_geo_home', methods: ['GET'])]
    public function legacyHome(): Response
    {
        return $this->redirectToRoute('geo_home', [], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/geo-audit', name: 'geo_audit', methods: ['POST'])]
    public function audit(Request $request): Response
    {
        $decision = $this->auditQuota->consume($request->getClientIp() ?? 'unknown');
        if (!$decision->accepted) {
            return $this->render('geo/home.html.twig', [
                'url' => trim((string) $request->request->get('url')),
                'error' => $this->translator->trans('audit.limit.message'),
                'limit_exhausted' => true,
            ], new Response(status: 429, headers: ['Retry-After' => (string) $decision->retryAfterSeconds]));
        }

        $url = trim((string) $request->request->get('url'));
        try {
            return $this->render('geo/report.html.twig', [
                'report' => $this->analyzer->analyze($url, $request->request->getBoolean('refresh')),
            ]);
        } catch (UnsafeUrlException | \RuntimeException $exception) {
            return $this->render('geo/home.html.twig', [
                'url' => $url,
                'error' => $exception->getMessage(),
            ], new Response(status: 422));
        }
    }
}
