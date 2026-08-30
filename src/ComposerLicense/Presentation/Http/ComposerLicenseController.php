<?php

declare(strict_types=1);

namespace App\ComposerLicense\Presentation\Http;

use App\ComposerLicense\Application\ComposerLicenseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

final class ComposerLicenseController extends AbstractController
{
    private const MAX_REQUEST_BYTES = 1_000_000;

    public function __construct(
        private readonly ComposerLicenseService $licenseService,
        private readonly ?Environment $twig = null,
        private readonly ?RateLimiterFactory $licenseLimiter = null,
    ) {
    }

    #[Route(
        path: ['en' => '/composer-license-checker', 'pl' => '/pl/sprawdzanie-licencji-composer'],
        name: 'composer_license_checker',
        methods: ['GET'],
    )]
    public function index(): Response
    {
        if ($this->twig !== null) {
            return new Response($this->twig->render('tools/composer_license.html.twig'));
        }

        return $this->render('tools/composer_license.html.twig');
    }

    #[Route('/api/composer-license-checker/audit-package', name: 'api_composer_audit_package', methods: ['POST'])]
    public function auditPackage(Request $request): JsonResponse
    {
        if (($limited = $this->limitExceeded($request)) !== null) {
            return $limited;
        }
        if (strlen((string) $request->getContent()) > self::MAX_REQUEST_BYTES) {
            return $this->error('Request body exceeds the 1 MB limit.', Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }
        /** @var array<string, mixed> $data */
        $data = json_decode((string) $request->getContent(), true) ?? [];
        $packageName = (string) ($data['package'] ?? $request->request->get('package', ''));

        if (!preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:(?:[_.]|-{1,2})?[a-z0-9]+)*$#i', $packageName)) {
            return $this->error(
                'Please provide a valid package name in vendor/package format.',
                Response::HTTP_BAD_REQUEST,
            );
        }

        try {
            $result = $this->licenseService->auditPackage($packageName);
            return new JsonResponse([
                'status' => 'success',
                'result' => $result->toArray(),
            ]);
        } catch (\InvalidArgumentException $exception) {
            return $this->error($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\RuntimeException $exception) {
            return $this->error($exception->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    #[Route('/api/composer-license-checker/audit-lockfile', name: 'api_composer_audit_lockfile', methods: ['POST'])]
    public function auditLockfile(Request $request): JsonResponse
    {
        if (($limited = $this->limitExceeded($request)) !== null) {
            return $limited;
        }
        if (strlen((string) $request->getContent()) > self::MAX_REQUEST_BYTES) {
            return $this->error('Request body exceeds the 1 MB limit.', Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }
        /** @var array<string, mixed> $data */
        $data = json_decode((string) $request->getContent(), true) ?? [];
        $content = (string) ($data['content'] ?? $request->request->get('content', ''));

        if (trim($content) === '') {
            return $this->error('Please paste composer.json or composer.lock content.', Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->licenseService->auditLockfileContent($content);
            return new JsonResponse([
                'status' => 'success',
                'result' => $result->toArray(),
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    private function limitExceeded(Request $request): ?JsonResponse
    {
        if ($this->licenseLimiter === null) {
            return null;
        }
        $key = ($request->getClientIp() ?? 'unknown') . '|' . gmdate('Y-m-d');
        if ($this->licenseLimiter->create($key)->consume()->isAccepted()) {
            return null;
        }
        $response = $this->error('Daily Composer license audit limit reached.', Response::HTTP_TOO_MANY_REQUESTS);
        $response->headers->set('Retry-After', (string) max(1, strtotime('tomorrow UTC') - time()));

        return $response;
    }

    private function error(string $message, int $status): JsonResponse
    {
        return new JsonResponse(['status' => 'error', 'message' => $message], $status);
    }
}
