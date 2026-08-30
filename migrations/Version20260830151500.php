<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830151500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update composer license blog article description and content to eliminate all em-dashes and AI markers';
    }

    public function up(Schema $schema): void
    {
        $articleHtml = <<<'HTML'
<p class="article-lead">When you run <code>composer require</code>, Composer checks whether package versions match. It never checks whether the licenses inside those packages are legally compatible with your project. If you assume a top-level MIT tag in <code>composer.json</code> makes an entire application safe for commercial use, you are trusting metadata that Composer never verifies against transitive dependencies.</p>

<div class="article-callout article-callout-accent"><strong>The short version</strong><span>A package's <code>license</code> field only covers the code written by its author. In our scan of the top 10,000 Composer packages and 100 popular WordPress plugins, 320 packages (3.2%) pulled in copyleft or non-permissive code while claiming a permissive license at the root. Examples include PayPal's server SDK pulling OSL-3.0 code, and WPForms Lite vendoring OSL-3.0 headers across 5 million active sites.</span></div>

<h2>Composer never validates license compatibility</h2>
<p>When an author publishes a PHP library on Packagist, they pick a license string for <code>composer.json</code>. Packagist stores that string, displays it on the website, and returns it through its API. Composer displays it when you run <code>composer licenses</code>.</p>
<p>Neither tool checks whether that declaration conflicts with any dependencies. If an MIT package requires a GPL-2.0, LGPL-3.0, or OSL-3.0 library, Composer resolves the version solver, downloads the archives, updates <code>composer.lock</code>, and exits without warnings. The conflict stays hidden until someone audits the full dependency tree.</p>

<div class="article-screen">
  <div class="screen-chrome"><span class="screen-dot"></span><span class="screen-dot"></span><span class="screen-dot"></span><span>audit-snapshot-2026-08-30 (top 10,000 packages)</span></div>
  <div class="screen-body">
    <div class="screen-title"><span>Packagist Top 10k Screening Matrix</span><span class="screen-status">EMPIRICAL DATA</span></div>
    <div class="screen-grid">
      <div><small>PACKAGES AUDITED</small><strong>10,000</strong><span class="screen-good">Full lockfile resolution</span></div>
      <div><small>REVIEW CANDIDATES</small><strong>320 (3.2%)</strong><span class="screen-good">Transitive copyleft mismatch</span></div>
      <div><small>STRONG COPYLEFT SIGNALS</small><strong>184</strong><span class="screen-good">GPL, AGPL, OSL-3.0</span></div>
      <div><small>WEAK COPYLEFT / DUAL</small><strong>470</strong><span class="screen-good">LGPL, MPL-2.0, Dual-GPL</span></div>
    </div>
  </div>
  <span class="screen-caption">Snapshot collected on August 30, 2026 across stable Packagist releases and top WordPress.org plugins.</span>
</div>

<h2>The PayPal SDK: how OSL-3.0 reached e-commerce checkouts</h2>
<p>When PayPal deprecated their older PHP SDKs, they instructed merchants to migrate to <code>paypal/paypal-server-sdk</code>. On Packagist, PayPal marked this package as <code>MIT</code>.</p>
<p>Looking at the actual dependency graph shows what happens under the hood:</p>

<pre class="article-code article-code-danger"><code>paypal/paypal-server-sdk (Packagist says MIT | Archive contains PayPal EULA)
 \-- apimatic/core (Declared MIT | Upstream notices stripped)
      \-- apimatic/jsonmapper (SPDX: OSL-3.0 - Open Software License 3.0)
           \-- forked from cweiske/jsonmapper (Copyright Christian Weiske &amp; Netresearch)</code></pre>

<p>The problem is <strong>OSL-3.0 Section 5 ("External Deployment")</strong>. Standard GPL-2.0 only requires source distribution when you hand over binary or source files. OSL-3.0 treats network access as distribution:</p>

<div class="article-callout"><strong>The OSL-3.0 network trigger clause</strong><span><em>"The term 'External Deployment' means the use, distribution, or communication of the Original Work or Modifications in any way such that the Original Work or Modifications may be used by anyone other than You... shall be deemed a distribution under Section 3."</em></span></div>

<p>For SaaS applications and online checkouts, using an OSL-3.0 library means that running your code over HTTP can trigger source-code disclosure requests. PayPal built their SDK with APIMatic's code generator. APIMatic used a fork of Christian Weiske's OSL-3.0 <code>jsonmapper</code>. Any merchant running <code>composer require paypal/paypal-server-sdk</code> ended up pulling OSL-3.0 into their application.</p>

<h2>WPForms Lite: namespace prefixing does not change copyright</h2>
<p>WordPress.org requires all hosted plugins to use GPLv2 or later compatible licenses. To prevent class collisions in WordPress, plugin authors often use tools like PHP-Scoper or Mozart to prefix dependency namespaces.</p>
<p>In our archive audit of <strong>WPForms Lite (Rank #9 on WordPress.org, 5,000,000+ active installs)</strong>, the plugin prefixed the APIMatic SDK into:</p>
<pre class="article-code"><code>wp-content/plugins/wpforms-lite/vendor_prefixed/apimatic/jsonmapper/</code></pre>

<p>Prefixing renamed classes to <code>WPForms\Vendor\apimatic\jsonmapper\JsonMapper</code>, but left the docblocks alone. Five PHP files in that directory still have the full Open Software License 3.0 text and Christian Weiske's copyright notices.</p>
<p>Scoping tools modify PHP code syntax; they do not alter copyright terms. Embedding OSL-3.0 files in a GPLv2 plugin creates an unresolved licensing conflict on millions of WordPress sites.</p>

<h2>Five upstream libraries behind 80% of copyleft signals</h2>
<p>The 320 flagged packages in the top 10,000 clustered around five upstream projects:</p>

<ul class="article-checklist">
  <li><strong><code>ezyang/htmlpurifier</code> (LGPL-2.1-or-later, 108 packages):</strong> Used by frameworks and tools like <code>yiisoft/yii2</code>, <code>mews/purifier</code>, and <code>stevebauman/purify</code>. Autoloading an unmodified library in PHP fits standard LGPL-2.1 dynamic linking rules, but developers often forget to include the LGPL license file and attribution notices in their distributions.</li>
  <li><strong><code>netresearch/jsonmapper</code> / <code>apimatic/jsonmapper</code> (OSL-3.0, 27 packages):</strong> Used in code generators, JSON-RPC servers (<code>danog/advanced-json-rpc</code>, <code>felixfbecker/advanced-json-rpc</code>), and Psalm plugins.</li>
  <li><strong><code>enshrined/svg-sanitize</code> (GPL-2.0-or-later, 26 packages):</strong> Required by CMS plugins and e-commerce modules that declare MIT or proprietary licenses. GPL-2.0 has no linking exception, so bundling it into closed-source software creates a direct conflict.</li>
  <li><strong><code>paragonie/halite</code> and <code>hidden-string</code> (MPL-2.0, 24 packages):</strong> Cryptography libraries. MPL-2.0 works at the file level, so you can use it in commercial applications as long as modifications to MPL files stay under MPL-2.0 and license notices remain intact.</li>
  <li><strong><code>dompdf/dompdf</code> and <code>smalot/pdfparser</code> (LGPL-2.1 / LGPL-3.0, 20+ packages):</strong> PDF parsing and rendering engines used in invoice generators (like <code>horstoeko/zugferd</code>) and Laravel wrappers (<code>barryvdh/laravel-dompdf</code>).</li>
</ul>

<h2>How to audit your vendor directory right now</h2>
<p>You can check your project's dependency licenses directly from the terminal:</p>

<pre class="article-code"><code># 1. List all licenses found in your lockfile
composer licenses --format=json

# 2. Search vendor files for copyleft headers
grep -rEi "General Public License|Open Software License|Mozilla Public License" vendor/ \
  --include="*.php" --include="*LICENSE*" --include="*NOTICE*" | head -n 30

# 3. Find why a specific dependency was installed
composer why apimatic/jsonmapper
composer why ezyang/htmlpurifier
composer why enshrined/svg-sanitize</code></pre>

<h2>AI agent guardrail: automated license checks</h2>
<p>Coding agents often pick packages based on GitHub stars or root Packagist tags. You can give your AI agents this rule to make them inspect the lockfile before adding dependencies:</p>

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
1. Always verify exact installed versions against composer.lock. Treat composer.json lookups as unpinned estimates.
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

<h2>Reproducible snapshot and open dataset</h2>
<p>The 10,000-package dataset, normalized SPDX edge tables, WordPress audit logs, and replication scripts are published in the open StackHal repository under <a href="https://github.com/bahdanhal/stackhal/tree/main/docs/research/composer-license-audit"><code>docs/research/composer-license-audit/</code></a>.</p>

<p class="article-sources"><strong>Primary sources and documentation:</strong> <a href="https://getcomposer.org/doc/01-basic-usage.md#commit-your-composer-lock-file-to-version-control">Composer Lock Documentation</a> · <a href="https://opensource.org/license/osl-3.0">OSI Open Software License 3.0</a> · <a href="https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/">WordPress.org Plugin Guidelines</a> · <a href="https://github.com/bahdanhal/stackhal/tree/main/docs/research/composer-license-audit">StackHal Research Dataset &amp; Checksums</a></p>
HTML;

        $this->addSql(
            'UPDATE blog_articles SET title = ?, description = ?, content_html = ?, updated_at = ? WHERE slug = ?',
            [
                'Composer License Metadata vs. Dependency Reality: Screening 10,000 Packages',
                'Top-level Composer manifests only declare what the root package intends. In a scan of 10,000 Packagist libraries and 100 WordPress plugins, 320 packages pulled transitive copyleft dependencies, including PayPal\'s server SDK and WPForms Lite.',
                $articleHtml,
                new \DateTimeImmutable('2026-08-30 15:15:00+00:00'),
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
