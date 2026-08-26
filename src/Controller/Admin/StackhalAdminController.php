<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Analytics\Application\TrafficAnalytics;
use App\Audit\Infrastructure\AuditLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StackhalAdminController extends AbstractController
{
    private const AUTH_COOKIE_NAME = 'stackhal_admin_auth';

    public function __construct(
        private readonly TrafficAnalytics $trafficAnalytics,
        private readonly AuditLogger $auditLogger,
        private readonly string $secret,
    ) {
    }

    #[Route('/admin', name: 'stackhal_admin_dashboard', methods: ['GET'])]
    public function index(Request $request): Response
    {
        if (!$this->isAuthenticated($request)) {
            return $this->render('admin/login.html.twig');
        }

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $sevenDaysAgo = $now->modify('-7 days');
        $thirtyDaysAgo = $now->modify('-30 days');

        $traffic = $this->trafficAnalytics->summary($now);
        $trafficPeak = max([1, ...array_column($traffic['daily'], 'page_views')]);
        $traffic['daily'] = array_map(static fn (array $day): array => [
            ...$day,
            'height_percent' => $day['page_views'] === 0
                ? 2
                : max(8, (int) round(($day['page_views'] / $trafficPeak) * 100)),
        ], $traffic['daily']);

        $events = $this->auditLogger->events();
        $runs = [];
        foreach (array_reverse($events) as $event) {
            $auditId = (string) ($event['audit_id'] ?? '');
            if ($auditId === '') {
                continue;
            }
            $runs[$auditId] ??= ['audit_id' => $auditId, 'status' => 'running'];
            if ($event['event'] === 'audit_requested') {
                $runs[$auditId]['requested_at'] = (string) $event['timestamp'];
                $runs[$auditId]['target'] = (string) ($event['target'] ?? '');
            }
            if ($event['event'] === 'audit_completed') {
                $runs[$auditId] = [
                    ...$runs[$auditId],
                    'status' => 'completed',
                    'completed_at' => (string) $event['timestamp'],
                    'target' => (string) ($event['target'] ?? $runs[$auditId]['target'] ?? ''),
                    'score' => (int) ($event['score'] ?? 0),
                    'pages_crawled' => (int) ($event['pages_crawled'] ?? 0),
                    'duration_ms' => (int) ($event['request_duration_ms'] ?? 0),
                    'cache_hit' => (bool) ($event['cache_hit'] ?? false),
                ];
            }
            if ($event['event'] === 'audit_failed') {
                $runs[$auditId] = [
                    ...$runs[$auditId],
                    'status' => 'failed',
                    'completed_at' => (string) $event['timestamp'],
                    'duration_ms' => (int) ($event['request_duration_ms'] ?? 0),
                    'error_type' => (string) ($event['error_type'] ?? ''),
                    'error' => (string) ($event['error'] ?? ''),
                ];
            }
        }

        $runs = array_values($runs);
        usort($runs, static fn (array $a, array $b): int => ($b['requested_at'] ?? '') <=> ($a['requested_at'] ?? ''));

        return $this->render('admin/dashboard.html.twig', [
            'traffic' => $traffic,
            'recent_audits' => array_slice($runs, 0, 30),
            'total_audits' => count($runs),
            'audits_last_7_days' => count(array_filter(
                $runs,
                static fn (array $run): bool => isset($run['requested_at'])
                    && new \DateTimeImmutable((string) $run['requested_at']) >= $sevenDaysAgo
            )),
        ]);
    }

    #[Route('/admin/login', name: 'stackhal_admin_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $password = trim((string) $request->request->get('password', ''));
        $adminToken = trim((string) ($_ENV['ADMIN_TOKEN'] ?? ($_ENV['MARKET_ADMIN_TOKEN'] ?? $this->secret)));
        $marketAdminToken = trim((string) ($_ENV['MARKET_ADMIN_TOKEN'] ?? ''));
        $secret = trim($this->secret);

        if (
            !$this->isHeaderAuthenticated($request)
            && !$this->isCsrfTokenValid('stackhal_admin_login', (string) $request->request->get('_token'))
        ) {
            return $this->render('admin/login.html.twig', [
                'error' => 'Invalid or expired CSRF token.',
            ]);
        }

        if (
            $password !== ''
            && (
                ($adminToken !== '' && hash_equals($adminToken, $password))
                || ($secret !== '' && hash_equals($secret, $password))
                || ($marketAdminToken !== '' && hash_equals($marketAdminToken, $password))
            )
        ) {
            $response = $this->redirectToRoute('stackhal_admin_dashboard');
            $authHash = hash_hmac('sha256', 'stackhal_admin_authenticated', $this->secret);
            $response->headers->setCookie(
                Cookie::create(
                    self::AUTH_COOKIE_NAME,
                    $authHash,
                    time() + (86400 * 30),
                    '/',
                    null,
                    $request->isSecure(),
                    true,
                    false,
                    Cookie::SAMESITE_LAX
                )
            );

            return $response;
        }

        return $this->render('admin/login.html.twig', [
            'error' => 'Invalid admin token or passphrase.',
        ]);
    }

    #[Route('/admin/logout', name: 'stackhal_admin_logout', methods: ['GET'])]
    public function logout(): Response
    {
        $response = $this->redirectToRoute('stackhal_admin_dashboard');
        $response->headers->clearCookie(self::AUTH_COOKIE_NAME, '/');

        return $response;
    }

    private function isHeaderAuthenticated(Request $request): bool
    {
        $token = $request->headers->get('X-Admin-Token');
        if ($token === null || $token === '') {
            $authHeader = (string) $request->headers->get('Authorization', '');
            if (preg_match('/^Bearer\s+(\S+)$/i', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }

        if ($token === null || trim($token) === '') {
            return false;
        }

        $cleanToken = trim($token);
        $adminToken = trim((string) ($_ENV['ADMIN_TOKEN'] ?? ($_ENV['MARKET_ADMIN_TOKEN'] ?? $this->secret)));
        $marketAdminToken = trim((string) ($_ENV['MARKET_ADMIN_TOKEN'] ?? ''));
        $secret = trim($this->secret);

        return ($adminToken !== '' && hash_equals($adminToken, $cleanToken))
            || ($secret !== '' && hash_equals($secret, $cleanToken))
            || ($marketAdminToken !== '' && hash_equals($marketAdminToken, $cleanToken));
    }

    private function isAuthenticated(Request $request): bool
    {
        if ($this->isHeaderAuthenticated($request)) {
            return true;
        }

        $cookie = (string) $request->cookies->get(self::AUTH_COOKIE_NAME, '');
        if ($cookie !== '' && trim($this->secret) !== '') {
            $expected = hash_hmac('sha256', 'stackhal_admin_authenticated', $this->secret);
            return hash_equals($expected, $cookie);
        }

        return false;
    }

    protected function isCsrfTokenValid(string $id, #[\SensitiveParameter] ?string $token): bool
    {
        if ($token === null || $token === '' || trim($this->secret) === '') {
            return false;
        }

        $expected = hash_hmac('sha256', 'csrf:' . $id, $this->secret);

        return hash_equals($expected, $token);
    }
}
