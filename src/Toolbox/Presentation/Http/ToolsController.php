<?php

declare(strict_types=1);

namespace App\Toolbox\Presentation\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ToolsController extends AbstractController
{
    #[Route('/', name: 'landing', methods: ['GET'])]
    public function landing(): Response
    {
        return $this->render('toolbox/home.html.twig');
    }

    #[Route('/tools', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->redirectToRoute('landing', [], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/ai-studio-local-file-sync', name: 'ai_studio_extension', methods: ['GET'])]
    public function aiStudioExtension(): Response
    {
        return $this->render('products/ai_studio_extension.html.twig');
    }

    #[Route('/apple-pkpass-inspector', name: 'pkpass_inspector', methods: ['GET'])]
    public function pkpassInspector(): Response
    {
        return $this->render('tools/pkpass_inspector.html.twig');
    }

    #[Route('/tools/apple-pkpass-inspector', name: 'legacy_pkpass_inspector', methods: ['GET'])]
    public function legacyPkpassInspector(): Response
    {
        return $this->redirectToRoute('pkpass_inspector', [], Response::HTTP_MOVED_PERMANENTLY);
    }
}
