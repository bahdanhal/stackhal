<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Audit\Application\AuditLogger;
use App\Audit\Application\IssueGrouper;
use App\Shared\Application\HttpFetcher;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\StorageInterface;
use Twig\Environment;

final class AuditSampleTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    public function testLandingSamplesAreLocalizedAndDoNotRunAudits(): void
    {
        $kernel = self::bootKernel();
        $container = self::getContainer();
        $fetcher = $this->createMock(HttpFetcher::class);
        $fetcher->expects(self::never())->method('fetch');
        $fetcher->expects(self::never())->method('fetchMany');
        $container->set(HttpFetcher::class, $fetcher);
        $logger = $this->createMock(AuditLogger::class);
        $logger->expects(self::never())->method('newAuditId');
        $logger->expects(self::never())->method('log');
        $container->set(AuditLogger::class, $logger);
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects(self::never())->method('fetch');
        $storage->expects(self::never())->method('save');
        $limiter = new RateLimiterFactory([
            'id' => 'audit-sample-test', 'policy' => 'fixed_window', 'limit' => 10, 'interval' => '1 day',
        ], $storage);
        $container->set('limiter.audit', $limiter);

        foreach (['en' => '/seo-audit', 'pl' => '/pl/audyt-seo'] as $locale => $path) {
            $response = $kernel->handle(Request::create('https://stackhal.com' . $path));
            self::assertSame(200, $response->getStatusCode());
            $html = (string) $response->getContent();
            self::assertStringContainsString('https://example.test/old-guide', $html);
            self::assertStringContainsString('robots-missing', $html);
            self::assertStringContainsString('canonical-mismatch', $html);
            self::assertStringContainsString('content="index,follow"', $html);
            self::assertStringContainsString('href="https://stackhal.com' . $path . '"', $html);
            self::assertStringContainsString('hreflang="en"', $html);
            self::assertStringContainsString('hreflang="pl"', $html);
            self::assertStringContainsString('method="post" action="' . ($locale === 'pl' ? '/pl/audyt' : '/audit'), $html);
            self::assertStringContainsString($locale === 'pl' ? 'fikcyjna demonstracja' : 'fictional demonstration', $html);
            self::assertStringNotContainsString('duplicate content penalties', $html);
            self::assertStringNotContainsString('audit.sample.', $html);
            self::assertLessThan(strpos($html, 'seo-sample-title'), strpos($html, 'class="audit-form"'));
        }
    }

    public function testSharedRendererPreservesRealFindingEvidenceAndEmptyState(): void
    {
        self::bootKernel();
        /** @var Environment $twig */
        $twig = self::getContainer()->get('twig');
        $groups = (new IssueGrouper())->group([[
            'severity' => 'warning',
            'code' => 'canonical-mismatch',
            'title' => 'Canonical points elsewhere',
            'detail' => 'Real audit detail',
            'evidence' => ['canonical' => 'https://example.com/preferred'],
        ]]);
        $html = $twig->render('audit/_findings.html.twig', ['issueGroups' => $groups]);
        self::assertStringContainsString('Real audit detail', $html);
        self::assertStringContainsString('https://example.com/preferred', $html);
        self::assertStringNotContainsString('example.test', $html);
        $empty = $twig->render('audit/_findings.html.twig', ['issueGroups' => []]);
        self::assertStringContainsString('class="empty"', $empty);
    }
}
