<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260927120000 extends AbstractMigration
{
    private const SLUG = 'bmad-vs-gsd-ai-agent-frameworks-benchmark';

    private const OLD_DESCRIPTION = 'An engineering opinion on role-based and spec-driven agent workflows: when decomposition helps, where handoffs cost time, and how to verify outcomes without unsupported benchmark verdicts.';

    private const NEW_DESCRIPTION = 'Compare role-based and spec-driven AI coding workflows by task clarity, handoff cost, parallelism, and the evidence needed to review completed work.';

    /** @return array<string, string> */
    private static function copyReplacements(): array
    {
        return [
            '<p class="article-lead">Choosing an agent workflow is an engineering tradeoff, not a contest settled by a model leaderboard. My preference for bounded, spec-driven execution is an opinion about managing implementation work, not a measured BMAD-versus-GSD benchmark result.</p>' => '<p class="article-lead">BMAD and GSD organize AI-assisted software work around different needs. Role decomposition makes discovery, design, implementation, and review explicit; spec-driven execution puts the requested behavior and acceptance checks at the center. The better fit depends on task uncertainty, team structure, and the cost of coordination.</p>',
            '<div class="article-callout article-callout-accent"><strong>Correction: no measured winner</strong><span>This article does not publish a controlled comparison of BMAD and GSD. The earlier numerical model-score comparisons and claims of a framework\'s collapse or industry dominance were not supported by the cited evidence and have been removed.</span></div>' => '',
            '<h2>Compare workflows, not unrelated scores</h2>' => '<h2>Choose a workflow for the work</h2>',
            '<p>Model benchmarks evaluate particular systems under particular conditions. They cannot by themselves establish that one project-management workflow beats another. Changing the model, tools, task budget or evaluation changes what is being compared.</p>' => '<p>Benchmarks for models or agent harnesses do not directly measure the quality of a project workflow. To compare workflows fairly, use the same representative tasks, model, tools, time budget, and acceptance criteria, then track completion, defects, cost, and review effort across repeated runs.</p>',
            '<p>A defensible framework comparison would hold those conditions constant, publish task definitions and acceptance tests, repeat runs, and report failures, cost and review effort. No such experiment is provided here. The original article\'s generic vendor homepages and unrelated repository links did not substantiate its exact score claims.</p>' => '<p>For day-to-day decisions, inspect where work gets stuck: unclear requirements, missed interfaces, slow handoffs, or weak verification. That points to the process change worth trying.</p>',
            '<p class="article-sources">Engineering opinion, corrected September 17, 2026. The linked long-context paper supports only the limited research finding described above. This article makes no numerical model-leaderboard claim and reports no controlled framework comparison.</p>' => '<p class="article-sources">Further reading: <a href="https://arxiv.org/abs/2307.03172">Lost in the Middle: How Language Models Use Long Contexts</a>, which studies position-sensitive retrieval performance in long contexts.</p>',
        ];
    }

    public function getDescription(): string
    {
        return 'Replace meta-correction copy in the BMAD and GSD article while preserving other editorial changes';
    }

    public function up(Schema $schema): void
    {
        $article = $this->connection->fetchAssociative(
            'SELECT content_html, description FROM blog_articles WHERE slug = ? AND locale = ?',
            [self::SLUG, 'en']
        );
        if ($article === false || !is_string($article['content_html'])) {
            return;
        }

        $content = $article['content_html'];
        foreach (self::copyReplacements() as $old => $new) {
            $this->abortIf(substr_count($content, $old) !== 1, 'The BMAD/GSD article copy has changed; review the editorial update before applying it.');
            $content = str_replace($old, $new, $content);
        }

        $description = $article['description'] === self::OLD_DESCRIPTION ? self::NEW_DESCRIPTION : $article['description'];
        $this->addSql(
            'UPDATE blog_articles SET content_html = ?, description = ?, updated_at = CURRENT_TIMESTAMP WHERE slug = ? AND locale = ?',
            [$content, $description, self::SLUG, 'en'],
            [Types::TEXT, Types::TEXT, Types::STRING, Types::STRING]
        );
    }

    public function down(Schema $schema): void
    {
        // The migration preserves surrounding edits; rollback is intentionally non-destructive.
    }
}

// phpcs:enable Generic.Files.LineLength.TooLong
