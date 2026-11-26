<?php

declare(strict_types=1);

namespace App\Tests\Blog;

use App\Blog\Application\BlogArticleRepository;
use App\Blog\Domain\BlogArticle;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class BlogIndexLocalizationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testEnglishIndexRendersMetadataAndLegacyPolishRedirects(): void
    {
        $kernel = self::bootKernel();
        self::getContainer()->set('logger', new NullLogger());
        $repository = $this->createStub(BlogArticleRepository::class);
        $repository->method('findPublished')->willReturn([$this->article()]);
        self::getContainer()->set(BlogArticleRepository::class, $repository);

        $response = $kernel->handle(Request::create('https://stackhal.com/blog'));
        self::assertSame(200, $response->getStatusCode());
        $html = (string) $response->getContent();
        foreach (
            [
                '<title>StackHal Blog · Practical Guides for Web Infrastructure',
                'content="Practical guides for email authentication, DNS, technical SEO',
                '<h1>Field notes<span>',
                '4 min read',
                'data-found-many="%count% notes found."',
            ] as $fragment
        ) {
            self::assertStringContainsString($fragment, $html);
        }
        self::assertStringNotContainsString('blog_index.', $html);
        self::assertStringNotContainsString('hreflang="pl"', $html);

        $polishRedirect = $kernel->handle(Request::create('https://stackhal.com/pl/blog'));
        self::assertSame(301, $polishRedirect->getStatusCode());
        self::assertSame('/blog', $polishRedirect->headers->get('Location'));
    }

    private function article(): BlogArticle
    {
        $date = new \DateTimeImmutable('2026-10-02T16:00:00Z');

        return new BlogArticle(
            'note-en',
            'DNS note',
            'A short description.',
            'DNS',
            4,
            $date,
            $date,
            '<h2>Section</h2><p>Body.</p>',
            '',
            '',
            '',
            [],
            [],
            'en',
            ''
        );
    }
}
