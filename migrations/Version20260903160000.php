<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260903160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Publish BMAD vs GSD on Claude Fable 5.1 and GPT-5.6 Sol Terminal-Bench 4.0 analysis';
    }

    public function up(Schema $schema): void
    {
        $contentHtml = <<<'HTML'
<p class="article-lead">AI coding changed forever in late 2026. On September 1, Anthropic launched Claude Fable 5.1. OpenAI rolled out GPT-5.6 Sol with native sub-agents. Both models offer million-token windows and deep reasoning. Yet engineers still debate how to bridge business ideas and code: do we need virtual teams like BMAD, or clean specs like GSD?</p>

<div class="article-callout article-callout-accent"><strong>The September 2026 verdict</strong><span>Raw token limits are solved. The real challenge is bridging business goals to technical code without silent drift. Multi-agent persona chains like BMAD became costly agile theater, absent from modern leaderboards. Meanwhile, GSD evolved into the industry standard: Spec-Driven Development (SDD) paired with Plan Mode, ephemeral sub-agents, and strict shell gates.</span></div>

<h2>The new baseline: Fable 5.1 and GPT-5.6 Sol</h2>
<p>In early September 2026, frontier models reached a new milestone. They no longer need roleplay prompts to plan complex software. Their reasoning runs directly inside the model.</p>
<p>Consider the two flagship releases:</p>
<ul class="article-checklist">
  <li><strong>Claude Fable 5.1:</strong> Anthropic cut cache read costs by 75% for agent loops. It scores 55.8% on Terminal-Bench 4.0, up from 42% in Fable 5.</li>
  <li><strong>GPT-5.6 Sol:</strong> OpenAI introduced Ultra Mode to run parallel sub-agents. It scores 37.3% on Terminal-Bench 4.0 in Codex.</li>
</ul>
<p>Both labs built their tools around the same core idea: fast, isolated sub-agents that run real shell commands.</p>

<h2>What late 2026 benchmarks actually prove</h2>
<p>Tests in 2026 no longer judge models on toy scripts. The field moved to full agent stack benchmarks:</p>
<ul class="article-checklist">
  <li><strong>Terminal-Bench 4.0:</strong> Calibrated by Stanford, Harbor, and the Laude Institute. It measures agents driving real shells with 8-hour task caps. Claude Code and Codex dominate this board.</li>
  <li><strong>Artificial Analysis Index:</strong> Their 2026 data shows that the agent harness shapes cost and success as much as the model itself.</li>
  <li><strong>BenchLM BenchAlign 5.2:</strong> Tracks over 400 benchmarks. Fable 5.1 leads in capability-per-dollar due to prompt caching.</li>
</ul>
<p>Across all these leaderboards, one fact stands out: no top-ranked system uses simulated human meetings.</p>

<h2>BMAD in late 2026: The rise and fall of agile theater</h2>
<p>BMAD was built when models could not plan on their own. It mimicked a full tech team of Product Managers, Architects, Leads, and Testers.</p>
<p>On modern models, this setup creates three fatal flaws:</p>
<ul class="article-checklist">
  <li><strong>Massive token waste:</strong> Passing long Markdown documents between fake personas burns 100,000 tokens before any code runs.</li>
  <li><strong>Echo chamber consensus:</strong> Research by Wu in 2025 showed that agent debates cause peer pressure. If one agent assumes a bad schema, the rest agree.</li>
  <li><strong>Crushed model reasoning:</strong> Modern frontier models plan best using native reasoning tokens. Forcing them into rigid roleplay prompts hurts their natural problem solving.</li>
</ul>
<p>BMAD was a stopgap hack, and once models learned to reason, fake office red tape became pure dead weight.</p>

<h2>Specs vs code: The true missing link</h2>
<p>Why did engineers like BMAD at first? Because building software has two parts: product discovery and writing code.</p>
<p>Specs define what to build. Code defines how to build it. A vague user request can easily mislead an agent:</p>
<ol class="article-checklist">
  <li><strong>The trap of raw vibe coding:</strong> If you give a frontier model a vague prompt, it starts coding immediately. It may write beautiful tests for the wrong architecture.</li>
  <li><strong>The failure of agent meetings:</strong> Agents talking to each other cannot discover missing business rules. Those rules live in your head, not in model weights.</li>
  <li><strong>The 2026 solution: Plan Mode:</strong> Modern tools enter a read-only research state. The agent maps the codebase and presents explicit architectural choices for your review.</li>
</ol>
<p>By using Plan Mode first, you settle key trade-offs before a single line of code changes.</p>

<h2>GSD: How a community tool became standard practice</h2>
<p>GSD started as an open-source framework for lean, spec-driven execution. In late 2026, the big AI labs adopted its exact philosophy under the name Spec-Driven Development.</p>
<p>This workflow relies on three core rules:</p>
<ul class="article-checklist">
  <li><strong>The Markdown spec anchor:</strong> You write a short, clear spec file in git. This file acts as the single source of truth.</li>
  <li><strong>Ephemeral sub-agents:</strong> Each task runs in a fresh sub-agent. When the task ends, its bulky history is dropped to stop attention drift.</li>
  <li><strong>Deterministic CLI gates:</strong> The model never self-reports success. It must run real compilers, linters, and unit tests in the terminal.</li>
</ul>
<p>OpenAI copied this pattern in GPT-5.6 Sol Ultra Mode. Anthropic optimized Fable 5.1 cache pricing for this exact loop. GSD won because it matches how modern model attention works.</p>

<div class="article-screen">
  <div class="screen-chrome"><span class="screen-dot"></span><span class="screen-dot"></span><span class="screen-dot"></span><span>agent-architecture-matrix-2026</span></div>
  <div class="screen-body">
    <div class="screen-title"><span>Late 2026 Agent Architecture Matrix</span><span class="screen-status">VERIFIED BENCHMARKS</span></div>
    <div class="screen-grid">
      <div><small>FABLE 5.1 (CLAUDE CODE)</small><strong>55.8% Pass Rate</strong><span class="screen-good">Terminal-Bench 4.0</span></div>
      <div><small>GPT-5.6 SOL (CODEX)</small><strong>37.3% Pass Rate</strong><span class="screen-good">Terminal-Bench 4.0</span></div>
      <div><small>BMAD ROLEPLAY</small><strong>High Overhead</strong><span class="screen-good">Zero presence in top tiers</span></div>
      <div><small>SDD + PLAN MODE</small><strong>Direct CLI Execution</strong><span class="screen-good">Standard in top harnesses</span></div>
    </div>
  </div>
  <span class="screen-caption">Evaluated across official Terminal-Bench 4.0 and Artificial Analysis benchmarks in late 2026.</span>
</div>

<h2>The late 2026 engineering playbook</h2>
<p>To get peak results from Fable 5.1, GPT-5.6 Sol, or Gemini 3.8, use these four rules:</p>
<ol class="article-checklist">
  <li><strong>Start in Plan Mode:</strong> Require your agent to inspect code in read-only mode and propose an architectural plan before editing.</li>
  <li><strong>Keep specs in git:</strong> Write a concise Markdown spec as your immutable contract. Never let the model guess intent.</li>
  <li><strong>Kill sub-agents early:</strong> Let each sub-agent do one job and terminate. Never let a single chat run all week.</li>
  <li><strong>Trust only terminal gates:</strong> Require clean passes on linters and unit tests. If tests fail, the task is not done.</li>
</ol>

<p class="article-sources"><strong>Primary sources:</strong> <a href="https://www.anthropic.com">Anthropic Claude Fable 5.1 Architecture (Sept 2026)</a> | <a href="https://openai.com">OpenAI GPT-5.6 Sol System Card (July 2026)</a> | <a href="https://github.com/princeton-nlp/SWE-agent">Terminal-Bench 4.0 Benchmark (Stanford, Harbor, Laude Institute)</a> | <a href="https://artificialanalysis.ai">Artificial Analysis Coding Agent Index (2026)</a> | <a href="https://arxiv.org/abs/2307.03172">Lost in the Middle: How Language Models Use Long Contexts (Liu et al.)</a></p>
HTML;

        $publishedAt = new \DateTimeImmutable('2026-09-03 14:16:06+00:00');

        $this->addSql(
            'INSERT INTO blog_articles (slug, title, description, category, read_time_minutes, published_at, updated_at, content_html, cta_label, cta_path, visual_class, visual_lines, how_to_steps, locale, alternate_slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                'bmad-vs-gsd-ai-agent-frameworks-benchmark',
                'BMAD vs GSD on Claude Fable 5.1 and GPT-5.6 Sol: The September 2026 Verdict',
                'Claude Fable 5.1 and GPT-5.6 Sol redefined AI coding. Here is why BMAD roleplay collapsed, why vibe coding drifts, and how Spec-Driven Development and Plan Mode became the 2026 industry standard.',
                'AI engineering',
                5,
                $publishedAt,
                $publishedAt,
                $contentHtml,
                'Open AI Studio Local File Sync',
                '/ai-studio-local-file-sync',
                'terminal-card',
                ['Frontier baseline: Claude Fable 5.1 & GPT-5.6 Sol', 'BMAD today: Agile roleplay overhead', 'GSD today: Spec-driven industry standard'],
                [
                    ['name' => 'Start in Plan Mode', 'text' => 'Inspect the codebase in read-only mode and establish technical choices first.'],
                    ['name' => 'Anchor with a spec', 'text' => 'Keep a short Markdown specification in git as the single source of truth.'],
                    ['name' => 'Kill sub-agents early', 'text' => 'Run tasks in fresh ephemeral sub-agents rather than open-ended chats.'],
                    ['name' => 'Trust terminal gates', 'text' => 'Verify code with automated test suites and linters directly in the CLI.'],
                ],
                'en',
                '',
            ],
            [
                Types::STRING,
                Types::STRING,
                Types::TEXT,
                Types::STRING,
                Types::SMALLINT,
                Types::DATETIMETZ_IMMUTABLE,
                Types::DATETIMETZ_IMMUTABLE,
                Types::TEXT,
                Types::STRING,
                Types::STRING,
                Types::STRING,
                Types::JSON,
                Types::JSON,
                Types::STRING,
                Types::STRING,
            ]
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'DELETE FROM blog_articles WHERE slug = ? AND locale = ?',
            ['bmad-vs-gsd-ai-agent-frameworks-benchmark', 'en'],
            [Types::STRING, Types::STRING]
        );
    }
}
