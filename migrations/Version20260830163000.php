<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830163000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Replace legal claims in the Composer license article with evidence-based review guidance';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "UPDATE blog_articles SET visual_lines = (visual_lines #>> '{}')::json WHERE json_typeof(visual_lines) = 'string'"
        );
        $this->addSql(
            "UPDATE blog_articles SET how_to_steps = (how_to_steps #>> '{}')::json WHERE json_typeof(how_to_steps) = 'string'"
        );

        $articleHtml = <<<'HTML'
<p class="article-lead">A package can use the MIT license while one of its dependencies uses GPL, OSL, LGPL, or MPL. This is not proof of a license breach, but it is a reason to inspect the exact code and terms before release.</p>

<div class="article-callout article-callout-accent"><strong>The short version</strong><span>We screened 10,000 Composer packages and 100 WordPress plugins. The first pass found 320 review candidates and recorded their license metadata and dependency paths. It did not decide if any package or project broke a license.</span></div>

<h2>What Composer tells you</h2>
<p>Composer resolves package versions, builds a dependency graph, and shows license text supplied by package authors. It does not decide if all licenses in the graph work together.</p>
<p>The root license is only one part of the record. Each child package keeps its own license. A useful review also checks the lock file, release archive, source headers, notices, and how the code is used.</p>

<div class="article-screen">
  <div class="screen-chrome"><span class="screen-dot"></span><span class="screen-dot"></span><span class="screen-dot"></span><span>audit-snapshot-2026-08-30</span></div>
  <div class="screen-body">
    <div class="screen-title"><span>Composer metadata screen</span><span class="screen-status">REVIEW DATA</span></div>
    <div class="screen-grid">
      <div><small>PACKAGES SCREENED</small><strong>10,000</strong><span class="screen-good">Ranked input set</span></div>
      <div><small>REVIEW CANDIDATES</small><strong>320 (3.2%)</strong><span class="screen-good">Not legal findings</span></div>
      <div><small>STRONG SIGNAL EDGES</small><strong>184</strong><span class="screen-good">GPL, AGPL, OSL</span></div>
      <div><small>WEAK OR DUAL EDGES</small><strong>470</strong><span class="screen-good">LGPL, MPL, dual terms</span></div>
    </div>
  </div>
  <span class="screen-caption">The corrected table contains 654 dependency edges: 184 strong, 444 weak, and 26 dual-license review signals.</span>
</div>

<h2>How the data was built</h2>
<p>The first pass used package metadata and resolved dependency paths. It took 296.25 seconds and selected 320 packages for a closer look.</p>
<p>The first classifier made a known mistake by putting some LGPL licenses in the strong group. We kept the raw files for traceability and added a script that rebuilds the edge table with corrected groups.</p>
<p>The old archive report also used labels such as <code>CONFIRMED_VIOLATION</code>. Those heuristic labels are not verified legal findings, and the research notes now say this in plain terms.</p>

<h2>Case study: the PayPal server SDK</h2>
<p>Packagist lists release 2.4.0 as MIT and shows its direct APIMatic requirements. The research snapshot contains this exact path:</p>

<figure class="article-evidence">
  <img src="/media/composer-license/packagist-paypal-2.4.0-mit.png" alt="Packagist metadata for paypal/paypal-server-sdk 2.4.0 showing MIT and its APIMatic requirements" width="1280" height="720" loading="lazy">
  <figcaption>Packagist on August 30, 2026. The page reports the root package metadata; it does not display the full child license path.</figcaption>
</figure>

<pre class="article-code"><code>paypal/paypal-server-sdk@2.4.0 (MIT)
 \-- apimatic/core@0.3.18 (MIT)
      \-- apimatic/jsonmapper@3.1.7 (OSL-3.0)</code></pre>

<p>This path is useful evidence. It shows which release led to the OSL-3.0 package. It does not show that an entire shop or service is a derivative work.</p>
<p>OSL-3.0 has an External Deployment clause. Its effect depends on which work is used, whether it was changed, and how the parts are combined. A dependency edge cannot answer those questions alone.</p>
<p>The safe next step is to keep the exact lock file and read the three release archives. Check license and notice files, then ask a qualified reviewer about the real use case.</p>

<figure class="article-evidence">
  <img src="/media/composer-license/tool-paypal-lock-audit.png" alt="StackHal Composer lockfile audit showing the exact PayPal, APIMatic Core, and APIMatic JsonMapper versions with review signals" width="1280" height="720" loading="lazy">
  <figcaption>Real local browser result from the exact lockfile mode. The tool shows review signals and states that the result is not a legal conclusion.</figcaption>
</figure>

<h2>Case study: WPForms Lite</h2>
<p>The WordPress snapshot found five PHP files with OSL-3.0 headers under this path:</p>

<pre class="article-code"><code>wp-content/plugins/wpforms-lite/vendor_prefixed/apimatic/jsonmapper/</code></pre>

<p>The files were copied into a prefixed vendor tree. A prefix changes PHP names. It does not remove the source headers. This is a clear provenance signal for the maintainer to review.</p>
<p>The scan does not prove that each site owner has a legal problem. It also does not decide if the plugin terms are compatible. Those claims need source review and legal context.</p>

<h2>What the counts mean</h2>
<ul class="article-checklist">
  <li><strong>184 strong-copyleft edges:</strong> metadata matched GPL, AGPL, or OSL families.</li>
  <li><strong>444 weak-copyleft edges:</strong> metadata matched LGPL or MPL families.</li>
  <li><strong>26 dual-license edges:</strong> metadata offered GPL and proprietary choices.</li>
  <li><strong>320 review candidates:</strong> one package may have more than one edge.</li>
</ul>
<p>These groups help sort work. They do not replace the license text. They also do not decide linking, distribution, source duties, or compatibility.</p>

<h2>Check your project</h2>
<p>Start with <code>composer.lock</code>. It records exact installed versions. A check based only on <code>composer.json</code> is an estimate because its ranges can resolve to many releases.</p>

<pre class="article-code"><code># Show declared licenses for locked packages
composer licenses --format=json

# Explain why a package is installed
composer why apimatic/jsonmapper

# Check the final locked dependency set
composer audit --locked</code></pre>

<p>Then inspect <code>LICENSE</code>, <code>COPYING</code>, <code>NOTICE</code>, and source headers in the exact archives. Record any missing node or failed download as incomplete. Never turn missing data into a clean result.</p>

<h2>Rule for coding agents</h2>
<p>The project includes a small Agent Skill for this task. It keeps the result useful without making legal claims.</p>

<details class="code-spoiler-widget">
  <summary>View the "composer-license-audit" Agent Skill</summary>
  <div class="spoiler-body">
    <button type="button" data-copy-target="composer-license-skill">Copy skill</button>
    <pre><code id="composer-license-skill" class="language-yaml">---
name: composer-license-audit
description: Screen Composer dependencies for license metadata and copyleft signals.
---

# Composer License Screening

1. Prefer composer.lock and report exact versions.
2. Label composer.json and Packagist range checks as estimates.
3. Keep dependency paths, license files, notices, and missing nodes.
4. Treat license matches as review signals, not legal findings.
5. Send compatibility and distribution questions to a qualified reviewer.

Never call a package illegal, infected, contaminated, or license laundering.
</code></pre>
  </div>
</details>

<h2>Data you can reproduce</h2>
<p>The repository contains the input list, raw reports, corrected edge table, checksums, and the normalization script. The README also lists known limits. This makes each published count open to review.</p>

<p class="article-sources"><strong>Sources and data:</strong> <a href="https://getcomposer.org/doc/01-basic-usage.md#commit-your-composer-lock-file-to-version-control">Composer lock file guide</a> | <a href="https://opensource.org/license/osl-3.0">Open Software License 3.0</a> | <a href="https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/">WordPress plugin guidelines</a> | <a href="https://github.com/bahdanhal/stackhal/tree/main/docs/research/composer-license-audit">Research snapshot and scripts</a></p>
HTML;

        $this->addSql(
            'UPDATE blog_articles SET title = ?, description = ?, content_html = ?, read_time_minutes = ?, updated_at = ? WHERE slug = ? AND locale = ?',
            [
                'Composer Licenses: What a Dependency Graph Can and Cannot Prove',
                'A reproducible screen of 10,000 Composer packages, with corrected license groups, exact dependency paths, and clear limits on automated findings.',
                $articleHtml,
                8,
                new \DateTimeImmutable('2026-08-30 16:30:00+00:00'),
                'composer-license-metadata-dependency-audit',
                'en',
            ],
            [
                Types::STRING,
                Types::STRING,
                Types::TEXT,
                Types::INTEGER,
                Types::DATETIMETZ_IMMUTABLE,
                Types::STRING,
                Types::STRING,
            ]
        );
    }

    public function down(Schema $schema): void
    {
    }
}
