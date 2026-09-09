<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260909134500 extends AbstractMigration
{
    private const SLUG = 'debugging-is-almost-dead-in-2026';

    private const FIGURES = [
        'That last part matters more' => <<<'FIGURE1'
<figure id="debugging-fix-verification" class="article-explanation-figure" style="margin:32px 0"><a href="/images/blog/debugging-fix-verification.svg" target="_blank" rel="noopener"><picture><source media="(max-width: 600px)" srcset="/images/blog/debugging-fix-verification-mobile.svg"><img src="/images/blog/debugging-fix-verification.svg" alt="Illustrative test: removing the crash is insufficient if the request is no longer processed." style="display:block;width:100%;height:auto" loading="lazy"></picture></a><figcaption style="font-size:14px;line-height:1.6;margin-top:12px;color:#465347">Illustrative example. The same failing request must stop crashing and still produce its intended result; the existing tests must also pass.</figcaption></figure>
FIGURE1,
        'Take a hypothetical payment' => <<<'FIGURE2'
<figure id="debugging-payment-evidence" class="article-explanation-figure" style="margin:32px 0"><a href="/images/blog/debugging-payment-evidence.svg" target="_blank" rel="noopener"><picture><source media="(max-width: 600px)" srcset="/images/blog/debugging-payment-evidence-mobile.svg"><img src="/images/blog/debugging-payment-evidence.svg" alt="Hypothetical payment investigation: connect payment, callback, queue and order evidence." style="display:block;width:100%;height:auto" loading="lazy"></picture></a><figcaption style="font-size:14px;line-height:1.6;margin-top:12px;color:#465347">The hypothetical payment case from this paragraph. HTTP 200 confirms the callback response, not a committed order. The broken step remains to be established.</figcaption></figure>
FIGURE2,
        'The same applies to an intermittent race' => <<<'FIGURE3'
<figure id="debugging-race-reproducer" class="article-explanation-figure" style="margin:32px 0"><a href="/images/blog/debugging-race-reproducer.svg?v=2" target="_blank" rel="noopener"><picture><source media="(max-width: 600px)" srcset="/images/blog/debugging-race-reproducer-mobile.svg?v=2"><img src="/images/blog/debugging-race-reproducer.svg?v=2" alt="Illustrative lost update: two workers read zero and both write one, producing one instead of two." style="display:block;width:100%;height:auto" loading="lazy"></picture></a><figcaption style="font-size:14px;line-height:1.6;margin-top:12px;color:#465347">One concrete race example. Pause worker A after its read, let worker B read and write, then resume A. The test makes the otherwise rare ordering repeatable.</figcaption></figure>
FIGURE3,
    ];

    public function getDescription(): string
    {
        return 'Add responsive debugging diagrams to their corresponding article paragraphs';
    }

    public function up(Schema $schema): void
    {
        $content = $this->articleContent();
        if ($content === null) {
            return;
        }

        foreach (self::FIGURES as $anchor => $figure) {
            if (str_contains($content, $figure)) {
                continue;
            }

            $pattern = '~(<p\b[^>]*>' . preg_quote($anchor, '~') . '.*?</p>)~s';
            $updated = preg_replace_callback(
                $pattern,
                static fn (array $match): string => $match[0] . $figure,
                $content,
                1,
                $count
            );
            $this->abortIf($updated === null || $count !== 1, 'The target article paragraph has changed: ' . $anchor);
            $content = $updated;
        }

        $this->writeContent($content);
    }

    public function down(Schema $schema): void
    {
        $content = $this->articleContent();
        if ($content !== null) {
            $this->writeContent(str_replace(array_values(self::FIGURES), '', $content));
        }
    }

    private function articleContent(): ?string
    {
        $content = $this->connection->fetchOne(
            'SELECT content_html FROM blog_articles WHERE slug = ? AND locale = ?',
            [self::SLUG, 'en']
        );

        return is_string($content) ? $content : null;
    }

    private function writeContent(string $content): void
    {
        $this->addSql(
            'UPDATE blog_articles SET content_html = ? WHERE slug = ? AND locale = ?',
            [$content, self::SLUG, 'en'],
            [Types::TEXT, Types::STRING, Types::STRING]
        );
    }
}

// phpcs:enable Generic.Files.LineLength.TooLong
