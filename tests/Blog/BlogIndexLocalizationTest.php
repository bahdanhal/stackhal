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
    public function testIndexMetadataAndInterfaceFollowTheRequestLocale(): void
    {
        $kernel = self::bootKernel();
        self::getContainer()->set('logger', new NullLogger());
        $repository = $this->createStub(BlogArticleRepository::class);
        $repository->method('findPublished')->willReturnCallback(
            fn (?string $locale): array => [$this->article((string) $locale)]
        );
        self::getContainer()->set(BlogArticleRepository::class, $repository);

        $expected = [
            '/blog' => [
                '<title>StackHal Blog · Practical Guides for Web Infrastructure',
                'content="Practical guides for email authentication, DNS, technical SEO',
                '<h1>Field notes<span>',
                '4 min read',
                'data-found-many="%count% notes found."',
            ],
            '/pl/blog' => [
                '<title>Blog StackHal · Praktyczne poradniki o infrastrukturze sieciowej',
                'content="Praktyczne poradniki StackHal o uwierzytelnianiu poczty, DNS',
                '<h1>Notatki z praktyki<span>',
                '4 min czytania',
                'data-found-many="Znaleziono notatek: %count%."',
            ],
        ];

        foreach ($expected as $path => $fragments) {
            $response = $kernel->handle(Request::create('https://stackhal.com' . $path));
            self::assertSame(200, $response->getStatusCode());
            $html = (string) $response->getContent();
            foreach ($fragments as $fragment) {
                self::assertStringContainsString($fragment, $html, $path);
            }
            self::assertStringNotContainsString('blog_index.', $html, $path);
        }

        $polish = (string) $kernel->handle(Request::create('https://stackhal.com/pl/blog'))->getContent();
        foreach (['Field notes', 'Read the story', 'The archive', 'Find a note', 'Written by'] as $english) {
            self::assertStringNotContainsString($english, $polish);
        }
    }

    private function article(string $locale): BlogArticle
    {
        $date = new \DateTimeImmutable('2026-10-02T16:00:00Z');

        return new BlogArticle(
            'note-' . $locale,
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
            $locale,
            'note-' . ($locale === 'pl' ? 'en' : 'pl')
        );
    }
}
