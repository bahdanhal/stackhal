<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update composer-license-metadata-dependency-audit article with deslopified human analysis';
    }

    public function up(Schema $schema): void
    {
        $articleHtml = <<<'HTML'
<p class="article-lead">Your <code>composer.json</code> only declares what you wrote. It says nothing about what happens when Composer recursively unpacks hundreds of third-party libraries into your <code>vendor/</code> directory. If you assume a top-level MIT declaration makes an entire application commercially safe, you are trusting metadata that Composer never checks against its own dependency graph.</p>

<div class="article-callout article-callout-accent"><strong>The short version</strong><span>A package’s <code>license</code> field describes only its root author’s terms. In an empirical audit of the top 10,000 Composer packages and 100 popular WordPress plugins, 320 packages (3.2%) pulled in copyleft or non-permissive dependencies despite declaring permissive licenses at the top level. Notable examples include PayPal’s modern server SDK pulling OSL-3.0 code, and WPForms Lite vendoring OSL-3.0 headers across 5,000,000+ active installations.</span></div>

<h2>Composer never validates license compatibility</h2>
<p>When you publish a PHP library on Packagist, you pick a license string in <code>composer.json</code>. Packagist stores that string, displays a neat green badge on the web interface, and exposes it through the Packagist API. Composer uses it when you run <code>composer licenses</code>.</p>
<p>What neither tool does is verify whether that declaration is compatible with the package’s dependencies. If an MIT-declared library requires a GPL-2.0, LGPL-3.0, or OSL-3.0 package, Composer resolves the solver constraints, downloads the zip archives, writes <code>composer.lock</code>, and exits with status 0. The incompatibility remains completely silent until a human reviewer, security auditor, or legal compliance scanner inspects the transitive tree.</p>

<div class="article-screen">
  <div class="screen-chrome"><span class="screen-dot"></span><span class="screen-dot"></span><span class="screen-dot"></span><span>audit-snapshot-2026-08-30 — top 10,000 packages</span></div>
  <div class="screen-body">
    <div class="screen-title"><span>Packagist Top 10k Screening Matrix</span><span class="screen-status">EMPIRICAL DATA</span></div>
    <div class="screen-grid">
      <div><small>PACKAGES AUDITED</small><strong>10,000</strong><span class="screen-good">Full lockfile resolution</span></div>
      <div><small>REVIEW CANDIDATES</small><strong>320 (3.2%)</strong><span class="screen-good">Transitive copyleft mismatch</span></div>
      <div><small>STRONG COPYLEFT SIGNALS</small><strong>184</strong><span class="screen-good">GPL, AGPL, OSL-3.0</span></div>
      <div><small>WEAK COPYLEFT / DUAL</small><strong>470</strong><span class="screen-good">LGPL, MPL-2.0, Dual-GPL</span></div>
    </div>
  </div>
  <span class="screen-caption">Snapshot dataset collected on August 30, 2026 across stable Packagist releases and top WordPress.org plugins.</span>
</div>

<h2>The PayPal SDK rabbit hole: how OSL-3.0 reached e-commerce checkouts</h2>
<p>When PayPal deprecated their legacy PHP SDKs, they instructed merchants and platform integrations to migrate to the modern <code>paypal/paypal-server-sdk</code>. On Packagist, PayPal declared the package as <code>MIT</code>.</p>
<p>Tracing the actual dependency tree down through its code-generation pipeline reveals a different story:</p>

<pre class="article-code article-code-danger"><code>paypal/paypal-server-sdk (Packagist says MIT | Archive contains PayPal EULA)
 └── apimatic/core (Declared MIT | Upstream notices stripped)
      └── apimatic/jsonmapper (SPDX: OSL-3.0 — Open Software License 3.0)
           └── forked from cweiske/jsonmapper (Copyright © Christian Weiske &amp; Netresearch)</code></pre>

<p>The issue here is <strong>OSL-3.0 Section 5 ("External Deployment")</strong>. Unlike standard GPL-2.0, which only triggers source disclosure obligations upon physical binary or source distribution, OSL-3.0 explicitly treats execution over a network interface as distribution:</p>

<div class="article-callout"><strong>The OSL-3.0 network trigger clause</strong><span><em>"The term 'External Deployment' means the use, distribution, or communication of the Original Work or Modifications in any way such that the Original Work or Modifications may be used by anyone other than You... shall be deemed a distribution under Section 3."</em></span></div>

<p>For SaaS platforms and hosted e-commerce checkouts, using an OSL-3.0 library means any derivative work communicating over a network can trigger source-code disclosure requests. PayPal generated their SDK using APIMatic’s engine, APIMatic vendored a fork of Christian Weiske’s OSL-3.0 <code>jsonmapper</code>, and every downstream merchant running <code>composer require paypal/paypal-server-sdk</code> inherited that OSL-3.0 dependency edge.</p>

<h2>WPForms Lite: namespace prefixing does not erase copyright</h2>
<p>WordPress.org plugin directory guidelines require all hosted code, libraries, and images to be GPLv2-or-later compatible. To avoid class collisions in WordPress’s global namespace, many popular plugin authors use tools like PHP-Scoper or Mozart to prefix dependency namespaces.</p>
<p>In our physical archive audit of <strong>WPForms Lite (Rank #9 on WordPress.org, 5,000,000+ active installs)</strong>, the plugin vendor-prefixed the APIMatic SDK into:</p>
<pre class="article-code"><code>wp-content/plugins/wpforms-lite/vendor_prefixed/apimatic/jsonmapper/</code></pre>

<p>Automated namespace prefixing successfully rewrote class declarations like <code>WPForms\Vendor\apimatic\jsonmapper\JsonMapper</code>, but it left the original file-level docblocks intact. Five core classes still carried the full Open Software License 3.0 header and Christian Weiske's copyright block.</p>
<p>Scoping tools modify PHP AST tokens; they do not alter intellectual property grants or resolve upstream copyleft terms. When a plugin distributed under GPLv2 embeds OSL-3.0 files, the resulting combination creates an unresolved licensing conflict in a plugin powering millions of live WordPress sites.</p>

<h2>The five upstream libraries behind 80% of copyleft signals</h2>
<p>Across the 320 flagged packages in the top 10,000, copyleft dependency edges were not evenly distributed. They clustered around five specific upstream projects:</p>

<ul class="article-checklist">
  <li><strong><code>ezyang/htmlpurifier</code> (LGPL-2.1-or-later — 108 packages):</strong> Embedded by frameworks and sanitizers including <code>yiisoft/yii2</code>, <code>mews/purifier</code>, and <code>stevebauman/purify</code>. In standard PHP web apps, runtime Composer autoloading of an unmodified library behaves similarly to dynamic linking under LGPL-2.1, but distributors frequently forget to bundle the required LGPL license text and attribution notices.</li>
  <li><strong><code>netresearch/jsonmapper</code> / <code>apimatic/jsonmapper</code> (OSL-3.0 — 27 packages):</strong> Forked across API client generators, JSON-RPC servers (<code>danog/advanced-json-rpc</code>, <code>felixfbecker/advanced-json-rpc</code>), and static analysis tools (<code>vimeo/psalm</code>, <code>phan/phan</code>).</li>
  <li><strong><code>enshrined/svg-sanitize</code> (GPL-2.0-or-later — 26 packages):</strong> Widely required by CMS plugins, Shopware 6 modules, and CraftCMS addons that declare themselves MIT or commercial. GPL-2.0 has no linking exception, creating a direct conflict when combined into closed-source or MIT distributions.</li>
  <li><strong><code>paragonie/halite</code> &amp; <code>hidden-string</code> (MPL-2.0 — 24 packages):</strong> Cryptographic libraries used across enterprise security tools. MPL-2.0 is file-level copyleft: clean to combine with proprietary code, provided modifications to MPL files remain under MPL-2.0 and notices are preserved.</li>
  <li><strong><code>dompdf/dompdf</code> &amp; <code>smalot/pdfparser</code> (LGPL-2.1 / LGPL-3.0 — 20+ packages):</strong> PDF generation and parsing engines embedded in invoice generators (e.g. <code>horstoeko/zugferd</code>) and Laravel wrappers (<code>barryvdh/laravel-dompdf</code>).</li>
</ul>

<h2>How to audit your vendor directory right now</h2>
<p>Do not wait for a procurement blocker or enterprise security audit. You can inspect your project’s real dependency licenses directly from the terminal:</p>

<pre class="article-code"><code># 1. Output all licenses declared across installed packages
composer licenses --format=json

# 2. Search installed vendor headers for copyleft identifiers
grep -rEi "General Public License|Open Software License|Mozilla Public License" vendor/ \
  --include="*.php" --include="*LICENSE*" --include="*NOTICE*" | head -n 30

# 3. Find why a specific copyleft dependency exists in your lockfile
composer why apimatic/jsonmapper
composer why ezyang/htmlpurifier
composer why enshrined/svg-sanitize</code></pre>

<h2>AI agent guardrail: automated license verification</h2>
<p>When autonomous coding agents generate or refactor Composer dependencies, they frequently recommend packages based solely on popular GitHub stars or top-level Packagist tags. You can equip your AI agents with this compact rule to ensure they inspect exact lockfile trees before introducing dependencies:</p>

<details class="code-spoiler-widget">
  <summary>
    📦 View the "composer-license-audit" Agent Skill
  </summary>
  <div class="spoiler-body">
    <button type="button" data-copy-target="composer-license-skill">Copy skill</button>
    <pre><code id="composer-license-skill" class="language-yaml">---
name: composer-license-audit
description: Screen Composer dependencies for license metadata and copyleft signals before recommending packages.
---

# Composer & Open Source License Compliance Skill

## Mandatory Verification Rules
1. Always verify exact installed versions against composer.lock; treat composer.json lookups as unpinned estimates.
2. Trace transitive dependency chains to identify copyleft licenses (GPL, AGPL, OSL, LGPL, MPL) before recommending packages.
3. Check for physical LICENSE, NOTICE, and source docblock headers in vendored or prefixed code.
4. Distinguish file-level weak copyleft (MPL, LGPL dynamic autoloading) from network-triggered strong copyleft (OSL-3.0, AGPL).
5. Never assume a top-level MIT or BSD declaration guarantees permissive terms for the full dependency tree.

## Output Contract
- State whether license evidence originates from composer.lock or unpinned Packagist constraints.
- Report full dependency paths for any flagged licenses (e.g. root -> depA -> depB [OSL-3.0]).
- Distinguish between runtime autoloaded libraries and vendored/bundled code.
</code></pre>
  </div>
</details>

<h2>Reproducible snapshot & open dataset</h2>
<p>The complete 10,000-package dataset, normalized SPDX edge tables, WordPress audit logs, and Python replication scripts are published in the open StackHal repository under <a href="https://github.com/bahdanhal/stackhal/tree/main/docs/research/composer-license-audit"><code>docs/research/composer-license-audit/</code></a>.</p>

<p class="article-sources"><strong>Primary sources &amp; documentation:</strong> <a href="https://getcomposer.org/doc/01-basic-usage.md#commit-your-composer-lock-file-to-version-control">Composer Lock Documentation</a> · <a href="https://opensource.org/license/osl-3.0">OSI Open Software License 3.0</a> · <a href="https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/">WordPress.org Plugin Guidelines</a> · <a href="https://github.com/bahdanhal/stackhal/tree/main/docs/research/composer-license-audit">StackHal Research Dataset &amp; Checksums</a></p>
HTML;

        $this->addSql(
            'UPDATE blog_articles SET title = ?, description = ?, content_html = ?, updated_at = ? WHERE slug = ?',
            [
                'Composer License Metadata vs. Dependency Reality: Screening 10,000 Packages',
                'Top-level Composer manifests only declare what the root package intends. In a scan of 10,000 Packagist libraries and 100 WordPress plugins, 320 packages pulled transitive copyleft dependencies—including PayPal\'s server SDK and WPForms Lite.',
                $articleHtml,
                new \DateTimeImmutable('2026-08-30 15:00:00+00:00'),
                'composer-license-metadata-dependency-audit',
            ],
            [
                Types::STRING,
                Types::STRING,
                Types::TEXT,
                Types::DATETIMETZ_IMMUTABLE,
                Types::STRING,
            ]
        );
    }

    public function down(Schema $schema): void
    {
    }
}
