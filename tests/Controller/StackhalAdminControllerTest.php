<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Analytics\Application\TrafficAnalytics;
use App\Analytics\Domain\PageViewRepository;
use App\Audit\Infrastructure\AuditLogger;
use App\Admin\Presentation\Http\StackhalAdminController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class StackhalAdminControllerTest extends TestCase
{
    private string $tempDir;
    private AuditLogger $auditLogger;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/stackhal-admin-test-' . bin2hex(random_bytes(4));
        $this->auditLogger = new AuditLogger($this->tempDir, 7);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tempDir . '/*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->tempDir);
    }

    public function testRendersLoginWhenUnauthenticated(): void
    {
        $secret = 'test-secret-key';

        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with('admin/login.html.twig', self::isArray())
            ->willReturn('<html>login</html>');

        $container = new Container();
        $container->set('twig', $twig);

        $controller = new StackhalAdminController(
            $this->trafficAnalytics(),
            $this->auditLogger,
            $secret,
        );
        $controller->setContainer($container);

        $request = new Request();
        $response = $controller->index($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('<html>login</html>', $response->getContent());
    }

    public function testAuthenticatesWithValidLoginPassword(): void
    {
        $secret = 'test-secret-key';

        $router = $this->createStub(\Symfony\Component\Routing\Generator\UrlGeneratorInterface::class);
        $router->method('generate')->willReturn('/admin');

        $container = new Container();
        $container->set('router', $router);

        $controller = new StackhalAdminController(
            $this->trafficAnalytics(),
            $this->auditLogger,
            $secret,
        );
        $controller->setContainer($container);

        $validToken = hash_hmac('sha256', 'csrf:stackhal_admin_login', $secret);
        $request = new Request(request: ['password' => 'test-secret-key', '_token' => $validToken]);
        $response = $controller->login($request);

        self::assertSame(302, $response->getStatusCode());
        $cookies = $response->headers->getCookies();
        self::assertNotEmpty($cookies);
        self::assertSame('stackhal_admin_auth', $cookies[0]->getName());
    }

    public function testRejectsEmptyPasswordLogin(): void
    {
        $secret = 'test-secret-key';

        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with('admin/login.html.twig', self::callback(static function (array $context): bool {
                return isset($context['error']) && str_contains($context['error'], 'Invalid admin token');
            }))
            ->willReturn('<html>login error</html>');

        $container = new Container();
        $container->set('twig', $twig);

        $controller = new StackhalAdminController(
            $this->trafficAnalytics(),
            $this->auditLogger,
            $secret,
        );
        $controller->setContainer($container);

        $validToken = hash_hmac('sha256', 'csrf:stackhal_admin_login', $secret);
        $request = new Request(request: ['password' => '', '_token' => $validToken]);
        $response = $controller->login($request);

        self::assertSame(200, $response->getStatusCode());
    }

    public function testRejectsLoginWithInvalidCsrfToken(): void
    {
        $secret = 'test-secret-key';

        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with('admin/login.html.twig', self::callback(static function (array $context): bool {
                return isset($context['error']) && str_contains($context['error'], 'CSRF');
            }))
            ->willReturn('<html>login error</html>');

        $container = new Container();
        $container->set('twig', $twig);

        $controller = new StackhalAdminController(
            $this->trafficAnalytics(),
            $this->auditLogger,
            $secret,
        );
        $controller->setContainer($container);

        $request = new Request(request: ['password' => 'test-secret-key', '_token' => 'invalid-token']);
        $response = $controller->login($request);

        self::assertSame(200, $response->getStatusCode());
    }

    public function testRendersDashboardWhenAuthenticated(): void
    {
        $this->auditLogger->log('audit_requested', [
            'audit_id' => 'audit_1',
            'target' => 'example.com',
        ]);
        $this->auditLogger->log('audit_completed', [
            'audit_id' => 'audit_1',
            'score' => 95,
            'pages_crawled' => 10,
            'request_duration_ms' => 500,
            'cache_hit' => false,
        ]);
        $secret = 'test-secret-key';

        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with(
                'admin/dashboard.html.twig',
                self::callback(static function (array $context): bool {
                    return $context['total_audits'] === 1
                        && count($context['recent_audits']) === 1
                        && $context['traffic']['last_7_days']['page_views'] === 0;
                }),
            )
            ->willReturn('<html>dashboard</html>');

        $container = new Container();
        $container->set('twig', $twig);

        $controller = new StackhalAdminController(
            $this->trafficAnalytics(),
            $this->auditLogger,
            $secret,
        );
        $controller->setContainer($container);

        $authCookie = hash_hmac('sha256', 'stackhal_admin_authenticated', $secret);
        $request = new Request(cookies: ['stackhal_admin_auth' => $authCookie]);
        $response = $controller->index($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('<html>dashboard</html>', $response->getContent());
    }

    public function testLogoutClearsCookie(): void
    {
        $secret = 'test-secret-key';

        $router = $this->createStub(\Symfony\Component\Routing\Generator\UrlGeneratorInterface::class);
        $router->method('generate')->willReturn('/admin');

        $container = new Container();
        $container->set('router', $router);

        $controller = new StackhalAdminController(
            $this->trafficAnalytics(),
            $this->auditLogger,
            $secret,
        );
        $controller->setContainer($container);

        $response = $controller->logout();
        self::assertSame(302, $response->getStatusCode());
    }

    private function trafficAnalytics(): TrafficAnalytics
    {
        $pageViews = $this->createStub(PageViewRepository::class);
        $pageViews->method('since')->willReturn([]);
        $pageViews->method('summary')->willReturn([
            'privacy' => 'Cookie-free aggregates.',
            'last_7_days' => ['page_views' => 0, 'unique_visitors' => 0, 'sources' => [], 'referring_domains' => [], 'top_paths' => []],
            'last_30_days' => ['page_views' => 0, 'unique_visitors' => 0, 'sources' => [], 'referring_domains' => [], 'top_paths' => []],
            'daily' => [],
        ]);

        return new TrafficAnalytics($pageViews);
    }
}
