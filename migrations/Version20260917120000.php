<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917120000 extends AbstractMigration
{
    private const BACKUP_TABLE = 'blog_editorial_backup_20260917';
    private const FIELDS = ['title', 'description', 'category', 'read_time_minutes', 'updated_at', 'content_html', 'cta_label', 'cta_path', 'visual_lines', 'how_to_steps'];

    /** @return array<string, array{title: string, description: string, category: string, read_time_minutes: int, content_html: string}> */
    public static function corrections(): array
    {
        return [
            'ai-coding-agents-deprecated-dependencies-census' => [
                'title' => 'Dependency Age in Newly Created Repositories: Observations and Limits',
                'description' => 'Reported dependency observations from a September 2026 repository sample, what manifests can establish, and why they do not prove AI causation or the compiler actually used.',
                'category' => 'Dependency maintenance',
                'read_time_minutes' => 4,
                'content_html' => <<<'HTML'
<p class="article-lead">A newly created repository can declare older dependencies. That is a useful maintenance question, but dependency age alone does not tell us who wrote the code, which runtime executed it, or whether it contains an exploitable vulnerability.</p>
<div class="article-callout article-callout-accent"><strong>Correction: observations are not causal evidence</strong><span>The earlier version called this an exhaustive census and attributed the findings to AI coding agents. Those conclusions were not established by the published evidence. This revision separates the reported observations from hypotheses and corrects the interpretation of Go version directives.</span></div>
<h2>What was reported</h2>
<p>The original article reported a September 4, 2026 sample of 34,187 repositories and 171,796 dependencies, including 575 Go repositories. These figures are retained as reported observations, not independently verified results. The underlying dataset and collection scripts have not been published with this article.</p>
<p>Coverage, selection criteria, collection failures, deduplication, dependency classification and version-resolution rules cannot currently be reproduced from the public material. A repository's creation date also does not establish when its code was written. The sample therefore cannot be described as every repository created that day, a global census, or a representative measurement of all development.</p>
<h2>A minimum Go version is not the compiler used</h2>
<p>The <code>go</code> directive in <code>go.mod</code> declares a minimum required Go version and affects language semantics. It does not identify the actual compiler used to build a project. A newer toolchain can build a module that declares an older minimum. Toolchain selection can also involve a <code>toolchain</code> directive and local configuration.</p>
<p>An old minimum requirement alone does not establish that an end-of-life compiler executed, that the deployed runtime is unsupported, or that the application is vulnerable. Establishing actual use requires build logs, CI configuration, toolchain output or deployed-runtime evidence. See the <a href="https://go.dev/doc/toolchain">official Go toolchain documentation</a>.</p>
<h2>What dependency age can and cannot tell us</h2>
<p>A manifest may contain a version range rather than an exact installed version. A lockfile provides a more specific resolution, but still does not prove what was deployed. Release age, maintenance status, known advisories and exploitability are separate properties. Security conclusions need the relevant package, resolved version, advisory and execution context.</p>
<p>The reported age distribution does not establish that AI agents created the repositories or caused their dependency choices. Training-data cutoffs, copied templates, compatibility constraints and human preferences are possible explanations, not measured causes here. The earlier "Cutoff Anchor Law" and compiler-friction explanation should be treated as untested hypotheses, not findings.</p>
<h2>How a reproducible follow-up would work</h2>
<p>A follow-up should publish selection queries, collection dates, scripts, aggregate data, failure counts and classification rules. It should distinguish declared requirements, resolved dependencies and observed toolchains. Comparing AI-assisted and other repositories would additionally require a defensible authorship classification and controls for ecosystem, project type and template reuse.</p>
<p>Until that evidence is available, this article is a limited field note. No screenshot substitutes for the dataset or establishes a causal relationship.</p>
<h2>Practical dependency maintenance</h2>
<ul class="article-checklist"><li>Inspect resolved dependencies and check relevant advisories rather than judging security by release age alone.</li><li>Record the toolchain actually used in CI and production, and keep it within its support policy.</li><li>Review updates for compatibility, then run tests against the intended deployment environment.</li><li>Apply the same verification to human-written and AI-assisted changes; neither authorship label establishes safety.</li></ul>
<p class="article-sources">Editorial correction, September 17, 2026. Numerical totals above are previously reported observations with unpublished underlying data. The Go documentation supports the toolchain distinction, not the repository counts or an AI-causation claim.</p>
HTML,
            ],
            'bmad-vs-gsd-ai-agent-frameworks-benchmark' => [
                'title' => 'BMAD vs GSD: An Engineering Tradeoff Guide',
                'description' => 'An engineering opinion on role-based and spec-driven agent workflows: when decomposition helps, where handoffs cost time, and how to verify outcomes without unsupported benchmark verdicts.',
                'category' => 'AI engineering opinion',
                'read_time_minutes' => 4,
                'content_html' => <<<'HTML'
<p class="article-lead">Choosing an agent workflow is an engineering tradeoff, not a contest settled by a model leaderboard. My preference for bounded, spec-driven execution is an opinion about managing implementation work, not a measured BMAD-versus-GSD benchmark result.</p>
<div class="article-callout article-callout-accent"><strong>Correction: no measured winner</strong><span>This article does not publish a controlled comparison of BMAD and GSD. The earlier numerical model-score comparisons and claims of a framework's collapse or industry dominance were not supported by the cited evidence and have been removed.</span></div>
<h2>Compare workflows, not unrelated scores</h2>
<p>Model benchmarks evaluate particular systems under particular conditions. They cannot by themselves establish that one project-management workflow beats another. Changing the model, tools, task budget or evaluation changes what is being compared.</p>
<p>A defensible framework comparison would hold those conditions constant, publish task definitions and acceptance tests, repeat runs, and report failures, cost and review effort. No such experiment is provided here. The original article's generic vendor homepages and unrelated repository links did not substantiate its exact score claims.</p>
<h2>Where role decomposition can help</h2>
<p>Separating requirements, architecture, implementation and review can make responsibilities explicit. It can be useful when the product problem is unclear, several independent workstreams exist, or a separate reviewer can challenge an implementation. Role names alone do not create independent judgment: each task still needs evidence, boundaries and a concrete output.</p>
<p>The risk is excessive handoff overhead. Repeated summaries can lose constraints, and agreement between agents does not validate a business rule. Those are failure modes to watch for, not proof that every role-based workflow fails.</p>
<h2>Why I prefer a short specification for bounded changes</h2>
<p>For a contained change, I prefer recording the goal, non-goals, affected interfaces and acceptance checks before implementation. A short versioned specification makes it easier to notice when a technically correct patch solves the wrong problem. It should be revised when new evidence changes the requirements, not treated as an immutable substitute for product discovery.</p>
<p>Read-only planning is useful when architecture or permissions matter. Small tasks need proportionate planning; requiring a large ceremony for a trivial fix can cost more than it helps.</p>
<h2>Context and parallel agents</h2>
<p>Bounded sub-agents can investigate independent tasks in parallel. Give each agent the relevant constraints, ownership and verification criteria, and integrate its result deliberately. Fresh context is not automatically better: losing necessary history can introduce mistakes too.</p>
<p><a href="https://arxiv.org/abs/2307.03172">Lost in the Middle: How Language Models Use Long Contexts</a> reports position-sensitive performance on the models and retrieval tasks studied. It motivates checking how context is organized; it does not prove that GSD wins, that all modern models behave identically, or that every long session should be discarded.</p>
<h2>A practical workflow to evaluate</h2>
<ol class="article-checklist"><li>Clarify the intended behavior and identify missing business rules with the person responsible for them.</li><li>Inspect the relevant code and record a proportionate plan, including non-goals and ownership boundaries.</li><li>Use parallel agents only for genuinely independent work; explicitly integrate shared interfaces and findings.</li><li>Run relevant tests, linters and build checks. Inspect the result against the requirement: a green suite may not cover the original failure.</li><li>Review safety and deployment evidence before declaring the change complete.</li></ol>
<p>Evaluate the workflow on your own representative tasks. A useful choice reduces rework and review effort without hiding failures. The right amount of decomposition depends on the project, available evidence and team responsibilities, not a universal verdict.</p>
<p class="article-sources">Engineering opinion, corrected September 17, 2026. The linked long-context paper supports only the limited research finding described above. This article makes no numerical model-leaderboard claim and reports no controlled framework comparison.</p>
HTML,
            ],
        ];
    }

    public function getDescription(): string
    {
        return 'Correct unsupported dependency and agent-workflow conclusions while preserving original article snapshots';
    }

    public function up(Schema $schema): void
    {
        // The census was edited outside seed migrations. Preserve exact stored public
        // article values instead of guessing its original content or timestamps.
        $this->addSql('CREATE TABLE ' . self::BACKUP_TABLE . ' AS SELECT slug, locale, ' . implode(', ', self::FIELDS) . ' FROM blog_articles WHERE 1 = 0');
        foreach (self::corrections() as $slug => $correction) {
            $this->addSql('INSERT INTO ' . self::BACKUP_TABLE . ' SELECT slug, locale, ' . implode(', ', self::FIELDS) . ' FROM blog_articles WHERE slug = ? AND locale = ?', [$slug, 'en']);
            $this->addSql(
                'UPDATE blog_articles SET title = ?, description = ?, category = ?, read_time_minutes = ?, updated_at = CURRENT_TIMESTAMP, content_html = ?, cta_label = ?, cta_path = ?, visual_lines = ?, how_to_steps = ? WHERE slug = ? AND locale = ?',
                [$correction['title'], $correction['description'], $correction['category'], $correction['read_time_minutes'], $correction['content_html'], '', '', '[]', '[]', $slug, 'en']
            );
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::corrections() as $slug => $correction) {
            $originalExists = $this->connection->fetchOne('SELECT slug FROM ' . self::BACKUP_TABLE . ' WHERE slug = ? AND locale = ?', [$slug, 'en']);
            if ($originalExists === false) {
                continue;
            }
            $current = $this->connection->fetchAssociative('SELECT title, description, category, read_time_minutes, content_html, cta_label, cta_path, visual_lines, how_to_steps FROM blog_articles WHERE slug = ? AND locale = ?', [$slug, 'en']);
            $this->abortIf(
                $current === false || $current['title'] !== $correction['title'] || $current['description'] !== $correction['description'] || $current['category'] !== $correction['category'] || (int) $current['read_time_minutes'] !== $correction['read_time_minutes'] || $current['content_html'] !== $correction['content_html'] || $current['cta_label'] !== '' || $current['cta_path'] !== '' || (string) $current['visual_lines'] !== '[]' || (string) $current['how_to_steps'] !== '[]',
                'The corrected article has since changed; restore its snapshot manually rather than overwriting newer edits.'
            );
            $assignments = array_map(static fn (string $field): string => $field . ' = (SELECT ' . $field . ' FROM ' . self::BACKUP_TABLE . ' WHERE slug = ? AND locale = ?)', self::FIELDS);
            $parameters = [];
            foreach (self::FIELDS as $field) {
                $parameters[] = $slug;
                $parameters[] = 'en';
            }
            $parameters[] = $slug;
            $parameters[] = 'en';
            $this->addSql('UPDATE blog_articles SET ' . implode(', ', $assignments) . ' WHERE slug = ? AND locale = ?', $parameters);
        }
        $this->addSql('DROP TABLE ' . self::BACKUP_TABLE);
    }
}

// phpcs:enable Generic.Files.LineLength.TooLong
