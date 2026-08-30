<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Publish a reproducible Composer dependency license screening study';
    }

    public function up(Schema $schema): void
    {
        $articleHtml = <<<'HTML'
<div class="article-lead-banner">
  <p class="lead-text"><strong>We screened the top 10,000 Composer packages and 100 popular WordPress plugins.</strong> The first pass flagged <strong>320 packages (3.2%)</strong> for manual review because their top-level license metadata differed from copyleft signals in their dependency graph. A physical archive review then separated strong signals, notice questions, incomplete metadata, benign LGPL use, and compliant packages.</p>
</div>



<h2>1. Why Top-Level License Metadata Is Not the Whole Answer</h2>
<p>A package's <code>license</code> field describes the terms declared by that package. Its dependencies retain their own licenses. A useful compliance review therefore needs the exact installed versions, the transitive dependency graph, physical license and notice files, and the way the software is combined and distributed.</p>

<p>This study is a <strong>screening exercise, not a legal verdict</strong>. A dependency edge can identify evidence worth reviewing, but it cannot by itself decide derivative-work status, license compatibility, or whether a particular source-disclosure obligation has been triggered.</p>

<div class="article-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
  <div class="stat-card" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 1.25rem; text-align: center;">
    <div style="font-size: 2rem; font-weight: 800; color: #60a5fa;">10,000</div>
    <div style="font-size: 0.85rem; color: #94a3b8;">Composer Packages Audited</div>
  </div>
  <div class="stat-card" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 10px; padding: 1.25rem; text-align: center;">
    <div style="font-size: 2rem; font-weight: 800; color: #f87171;">320 (3.2%)</div>
    <div style="font-size: 0.85rem; color: #fca5a5;">First-Pass Review Candidates</div>
  </div>
  <div class="stat-card" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); border-radius: 10px; padding: 1.25rem; text-align: center;">
    <div style="font-size: 2rem; font-weight: 800; color: #fbbf24;">184</div>
    <div style="font-size: 0.85rem; color: #fde68a;">Strong Copyleft Signals</div>
  </div>
  <div class="stat-card" style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); border-radius: 10px; padding: 1.25rem; text-align: center;">
    <div style="font-size: 2rem; font-weight: 800; color: #4ade80;">296.25s</div>
    <div style="font-size: 0.85rem; color: #86efac;">Initial Screening Duration</div>
  </div>
</div>

<h2>2. Case Study: PayPal SDK and an OSL-3.0 Dependency Signal</h2>
<p>PayPal recently deprecated their legacy PHP SDKs and instructed all e-commerce merchants to migrate to the new <code>paypal/paypal-server-sdk</code>. On Packagist, PayPal declared the package as <code>MIT</code>.</p>

<p>The selected release archive and dependency graph produced a three-level review path:</p>

<pre><code class="language-text">paypal/paypal-server-sdk (Manifest claims MIT | ZIP contains Proprietary PayPal Agreement)
 └── apimatic/core (Claims MIT | Notice Stripped)
      └── apimatic/jsonmapper (SPDX: OSL-3.0 — Open Software License 3.0)
           └── Forked from cweiske/jsonmapper (Copyright © Christian Weiske & Netresearch)
</code></pre>




<h3>What the OSL-3.0 Signal Means</h3>
<p><strong>OSL-3.0 Section 5</strong> treats External Deployment of the Original Work or a Derivative Work as distribution. The dependency path therefore deserves review in network-accessible applications. It does not, on its own, establish that an entire checkout application is a Derivative Work or determine the remedy.</p>

<p>The responsible next step is to preserve the exact package evidence, ask the relevant maintainers to confirm intended licensing, and obtain qualified advice for the concrete integration and distribution model.</p>

<h2>3. Case Study: APIMatic License Headers Inside WPForms Lite</h2>
<p>We expanded the screen to 100 popular WordPress.org plugins. WordPress.org requires submitted code, data, and images to use a GPL-compatible license.</p>

<p>In our physical archive audit of <strong>WPForms Lite (Rank #9, 5,000,000+ active installs)</strong>, we found that WPForms vendored and prefixed the APIMatic SDK engine into <code>wp-content/plugins/wpforms-lite/vendor_prefixed/apimatic/jsonmapper/</code>.</p>
<p>The scan found five prefixed PHP files carrying OSL-3.0 headers. This is a concrete provenance signal worth maintainer review; install count alone does not prove that every site operator has a legal violation.</p>

<h2>4. Frequently Observed Upstream License Signals</h2>
<p>The first-pass candidates cluster around a small number of upstream libraries. Counts below describe dependency-graph observations, not confirmed infringements:</p>

<ul>
  <li><strong><code>ezyang/htmlpurifier</code> (LGPL-2.1-or-later — 108 packages):</strong> Frequently used through Composer autoloading; the physical review classified many wrappers as benign, subject to version-specific LGPL obligations.</li>
  <li><strong><code>netresearch/jsonmapper</code> / <code>apimatic/jsonmapper</code> (OSL-3.0 — 27 packages):</strong> APIMatic SDKs, Psalm plugins, JSON-RPC servers.</li>
  <li><strong><code>enshrined/svg-sanitize</code> (GPL-2.0-or-later — 26 packages):</strong> Used across Shopware 6, CraftCMS modules, and Sylius plugins that claim to be MIT or proprietary.</li>
  <li><strong><code>paragonie/halite</code> & <code>hidden-string</code> (MPL-2.0 — 24 packages):</strong> Cryptography and security wrappers.</li>
  <li><strong><code>dompdf/dompdf</code> suite (LGPL-2.1 / LGPL-3.0 — 20 packages):</strong> PDF-generation wrappers that require notice and linking review.</li>
</ul>

<h2>5. Dataset Layers and Known Limitations</h2>
<ul>
  <li><strong>320</strong> packages were selected as first-pass review candidates.</li>
  <li><strong>654</strong> dependency edges were recorded: 184 strong-copyleft signals, 444 weak-copyleft signals, and 26 dual GPL/proprietary expressions.</li>
  <li>Physical archive metadata was collected for all 320 candidates, including conventional license files, notices, and source headers.</li>
  <li>The original physical-review labels are retained for traceability but are explicitly marked as legacy heuristic output, not verified legal findings.</li>
</ul>
<p>The complete CSV/JSON snapshot, methodology, checksums, and limitations are versioned with the StackHal source under <code>docs/research/composer-license-audit/</code>.</p>

<h2>6. AI Agent Guardrail</h2>
<p>This compact skill tells coding agents to distinguish exact lockfile evidence from estimates and to escalate legal conclusions to a human reviewer:</p>

<details class="code-spoiler-widget" style="background: rgba(13,17,23,0.8); border: 1px solid #30363d; border-radius: 8px; padding: 1rem; margin: 1.5rem 0;">
  <summary style="cursor: pointer; font-weight: 700; color: #58a6ff; user-select: none;">
    📦 View the "composer-license-audit" Agent Skill
  </summary>
  <div style="margin-top: 1rem;">
    <button type="button" data-copy-target="composer-license-skill">Copy skill</button>
    <pre><code id="composer-license-skill" class="language-yaml">---
name: composer-license-audit
description: Screen Composer dependencies for license metadata and copyleft signals before recommending packages.
---

# Composer & Open Source License Compliance Skill

## Mandatory Verification Rules
1. Prefer composer.lock because it records exact installed versions; label composer.json results as estimates.
2. Treat dependency-license matches as review signals, not automatic legal violations.
3. Preserve exact package versions, dependency paths, LICENSE/NOTICE evidence, and unresolved nodes.
4. Use the StackHal MCP tool or API, then request human review for compatibility, linking, distribution, and External Deployment questions.

## Output Contract
- State whether the evidence came from exact lock data or Packagist constraint resolution.
- Report incomplete graph traversal as incomplete, never clean.
- Do not call a package illegal, infected, contaminated, or laundering a license.
</code></pre>
  </div>
</details>

<h2>7. Screen Your Project</h2>
<p>The StackHal tool reads exact versions and dependency links from <code>composer.lock</code>. For <code>composer.json</code> or single-package lookups it resolves compatible stable Packagist releases and labels the result as an estimate.</p>

<h2>Primary References and Data</h2>
<ul>
  <li><a href="https://getcomposer.org/doc/01-basic-usage.md#commit-your-composer-lock-file-to-version-control" rel="noopener noreferrer">Composer documentation: exact versions in composer.lock</a></li>
  <li><a href="https://opensource.org/license/osl-3.0" rel="noopener noreferrer">Open Source Initiative: Open Software License 3.0</a></li>
  <li><a href="https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/" rel="noopener noreferrer">WordPress.org detailed plugin guidelines</a></li>
  <li><a href="https://github.com/bahdanhal/stackhal/tree/main/docs/research/composer-license-audit" rel="noopener noreferrer">Versioned dataset, normalization script, and limitations</a></li>
</ul>



<p style="margin-top: 1.5rem;"><a href="/composer-license-checker" class="btn btn-primary" style="display: inline-block; padding: 0.85rem 1.5rem; background: #3b82f6; color: #fff; border-radius: 8px; font-weight: 700; text-decoration: none;">Launch Composer License & Copyleft Checker &rarr;</a></p>
HTML;

        $this->addSql(
            'INSERT INTO blog_articles (slug, title, description, category, read_time_minutes, published_at, updated_at, content_html, cta_label, cta_path, visual_class, visual_lines, how_to_steps) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                'composer-license-metadata-dependency-audit',
                'Composer License Metadata vs. Dependency Reality: Screening 10,000 Packages',
                'A reproducible Composer and WordPress license-metadata screen: methodology, exact candidate breakdown, PayPal and WPForms evidence paths, and explicit legal limitations.',
                'Security & Compliance',
                11,
                new \DateTimeImmutable('2026-08-30 13:00:00+00:00'),
                new \DateTimeImmutable('2026-08-30 13:00:00+00:00'),
                $articleHtml,
                'Audit Your composer.lock',
                '/composer-license-checker',
                'terminal-card',
                json_encode([
                    'screening 10,000 Composer packages...',
                    'paypal/paypal-server-sdk -> apimatic/jsonmapper [OSL-3.0]',
                    'wpforms-lite -> vendor_prefixed/apimatic/jsonmapper [OSL-3.0]',
                    '320 first-pass review candidates (3.2%)',
                ], JSON_THROW_ON_ERROR),
                json_encode([
                    [
                        'name' => 'Paste composer.json or composer.lock',
                        'text' => 'Open Stackhal Composer License Checker and paste your project dependency manifest.',
                    ],
                    [
                        'name' => 'Inspect Transitive Copyleft Conflicts',
                        'text' => 'Review exact versions and evidence paths for GPL, AGPL, OSL, LGPL, or MPL signals.',
                    ],
                    [
                        'name' => 'Review Before Acting',
                        'text' => 'Inspect physical license files, confirm integration details, and involve a qualified reviewer before replacing a dependency.',
                    ],
                ], JSON_THROW_ON_ERROR),
            ],
            [
                Types::STRING,
                Types::STRING,
                Types::STRING,
                Types::STRING,
                Types::INTEGER,
                Types::DATETIMETZ_IMMUTABLE,
                Types::DATETIMETZ_IMMUTABLE,
                Types::TEXT,
                Types::STRING,
                Types::STRING,
                Types::STRING,
                Types::JSON,
                Types::JSON,
            ]
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM blog_articles WHERE slug = ?', ['composer-license-metadata-dependency-audit']);
    }
}
