<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830152500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update composer license blog article to high readability grade level and plain punctuation';
    }

    public function up(Schema $schema): void
    {
        $articleHtml = <<<'HTML'
<p class="article-lead">When you install a PHP library, Composer checks version numbers. It does not check licenses. If you think an MIT tag in <code>composer.json</code> makes your whole app safe for business use, you are trusting a label that Composer never checks against real dependencies.</p>

<div class="article-callout article-callout-accent"><strong>The short version</strong><span>A package license only covers code written by its author. We scanned 10,000 top Composer packages and 100 top WordPress plugins. 320 packages (3.2%) pulled in copyleft code while claiming a permissive license at the root. Notable examples include PayPal's server SDK pulling OSL-3.0 code, and WPForms Lite bundling OSL-3.0 headers across 5 million active sites.</span></div>

<h2>Composer does not check license compatibility</h2>
<p>When an author puts a library on Packagist, they write a license name in <code>composer.json</code>. Packagist stores that text and shows it on the web page. Composer reads it when you run <code>composer licenses</code>.</p>
<p>Neither tool checks whether that license works with other packages in your tree. If an MIT package needs a GPL or OSL library, Composer still installs it without warnings. The problem stays hidden until someone audits the full tree.</p>

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

<h2>The PayPal SDK: how OSL-3.0 reached online stores</h2>
<p>When PayPal retired older PHP SDKs, they told merchants to use <code>paypal/paypal-server-sdk</code>. On Packagist, PayPal listed this package as <code>MIT</code>.</p>
<p>Looking at the real dependency tree shows a different setup:</p>

<pre class="article-code article-code-danger"><code>paypal/paypal-server-sdk (Packagist says MIT | Archive contains PayPal EULA)
 \-- apimatic/core (Declared MIT | Upstream notices stripped)
      \-- apimatic/jsonmapper (SPDX: OSL-3.0 - Open Software License 3.0)
           \-- forked from cweiske/jsonmapper (Copyright Christian Weiske &amp; Netresearch)</code></pre>

<p>The issue is <strong>OSL-3.0 Section 5 ("External Deployment")</strong>. Standard GPL only triggers when you share files. OSL-3.0 treats web access as distribution:</p>

<div class="article-callout"><strong>The OSL-3.0 network trigger clause</strong><span><em>"The term 'External Deployment' means the use, distribution, or communication of the Original Work or Modifications in any way such that the Original Work or Modifications may be used by anyone other than You... shall be deemed a distribution under Section 3."</em></span></div>

<p>If you run an online store or SaaS app with OSL-3.0 code, users can ask for your source code. PayPal built their SDK with APIMatic tools. APIMatic used an OSL-3.0 tool from Christian Weiske. Any store using PayPal's new SDK pulled OSL-3.0 into their stack.</p>

<h2>WPForms Lite: renaming classes does not change copyright</h2>
<p>WordPress.org requires all hosted plugins to use GPLv2 or later compatible terms. To stop class name clashes, plugin authors often use tools like PHP-Scoper to add prefixes to third-party code.</p>
<p>In our scan of <strong>WPForms Lite (Rank #9 on WordPress.org, 5,000,000+ active installs)</strong>, the plugin placed the APIMatic SDK in:</p>
<pre class="article-code"><code>wp-content/plugins/wpforms-lite/vendor_prefixed/apimatic/jsonmapper/</code></pre>

<p>This changed class names to <code>WPForms\Vendor\apimatic\jsonmapper\JsonMapper</code>, but kept the file comments. Five PHP files in that folder still hold the full Open Software License 3.0 text and copyright notices.</p>
<p>Prefix tools change PHP code names. They do not change license rights. Putting OSL-3.0 files inside a GPLv2 plugin creates an unresolved conflict across millions of WordPress sites.</p>

<h2>Five libraries behind most copyleft findings</h2>
<p>Most of the 320 flagged packages came from five upstream projects:</p>

<ul class="article-checklist">
  <li><strong><code>ezyang/htmlpurifier</code> (LGPL-2.1-or-later, 108 packages):</strong> Used by frameworks like <code>yiisoft/yii2</code> and tools like <code>mews/purifier</code>. Loading an untouched library via Composer matches LGPL dynamic linking rules, but authors often forget to include the LGPL license file and notices.</li>
  <li><strong><code>netresearch/jsonmapper</code> / <code>apimatic/jsonmapper</code> (OSL-3.0, 27 packages):</strong> Used in code tools, JSON-RPC servers (<code>danog/advanced-json-rpc</code>), and Psalm plugins.</li>
  <li><strong><code>enshrined/svg-sanitize</code> (GPL-2.0-or-later, 26 packages):</strong> Used by CMS plugins and shop add-ons that claim MIT terms. GPL-2.0 has no linking exception, so putting it in closed-source code creates a direct conflict.</li>
  <li><strong><code>paragonie/halite</code> and <code>hidden-string</code> (MPL-2.0, 24 packages):</strong> Cryptography libraries. MPL-2.0 works at the file level, so you can use it in commercial apps if edits to MPL files stay open and notices stay in place.</li>
  <li><strong><code>dompdf/dompdf</code> and <code>smalot/pdfparser</code> (LGPL-2.1 / LGPL-3.0, 20+ packages):</strong> PDF tools used in invoice makers (like <code>horstoeko/zugferd</code>) and Laravel wrappers (<code>barryvdh/laravel-dompdf</code>).</li>
</ul>

<h2>Check your vendor folder right now</h2>
<p>You can check your dependencies from the command line:</p>

<pre class="article-code"><code># 1. List all licenses found in your lockfile
composer licenses --format=json

# 2. Search vendor files for copyleft headers
grep -rEi "General Public License|Open Software License|Mozilla Public License" vendor/ \
  --include="*.php" --include="*LICENSE*" --include="*NOTICE*" | head -n 30

# 3. Find why a package was installed
composer why apimatic/jsonmapper
composer why ezyang/htmlpurifier
composer why enshrined/svg-sanitize</code></pre>

<h2>AI agent rule: check licenses before adding code</h2>
<p>AI coding tools often pick packages based on popularity or root tags. Give your AI agents this rule so they check lockfiles before adding new packages:</p>

<details class="code-spoiler-widget">
  <summary>
    View the "composer-license-audit" Agent Skill
  </summary>
  <div class="spoiler-body">
    <button type="button" data-copy-target="composer-license-skill">Copy skill</button>
    <pre><code id="composer-license-skill" class="language-yaml">---
name: composer-license-audit
description: Screen Composer dependencies for license metadata and copyleft signals before recommending packages.
---

# Composer License Compliance Skill

## Rules
1. Always check exact versions in composer.lock. Treat composer.json lookups as unpinned estimates.
2. Trace all child dependencies to find copyleft licenses (GPL, AGPL, OSL, LGPL, MPL) before picking packages.
3. Look for LICENSE files, NOTICE files, and code header comments in vendored or prefixed code.
4. Separate file-level weak copyleft (MPL, LGPL) from network-triggered strong copyleft (OSL-3.0, AGPL).
5. Never assume a root MIT or BSD tag covers the full dependency tree.

## Output
- State if data came from composer.lock or unpinned Packagist lookups.
- Report full dependency paths for any flagged licenses (e.g. root -> depA -> depB [OSL-3.0]).
- Note if libraries are autoloaded or bundled directly.
</code></pre>
  </div>
</details>

<h2>Full data and tools</h2>
<p>The full dataset of 10,000 packages, edge tables, WordPress logs, and scripts are open in the StackHal repository under <a href="https://github.com/bahdanhal/stackhal/tree/main/docs/research/composer-license-audit"><code>docs/research/composer-license-audit/</code></a>.</p>

<p class="article-sources"><strong>Sources and links:</strong> <a href="https://getcomposer.org/doc/01-basic-usage.md#commit-your-composer-lock-file-to-version-control">Composer Lock Documentation</a> | <a href="https://opensource.org/license/osl-3.0">OSI Open Software License 3.0</a> | <a href="https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/">WordPress.org Plugin Guidelines</a> | <a href="https://github.com/bahdanhal/stackhal/tree/main/docs/research/composer-license-audit">StackHal Research Dataset</a></p>
HTML;

        $this->addSql(
            'UPDATE blog_articles SET title = ?, description = ?, content_html = ?, read_time_minutes = ?, updated_at = ? WHERE slug = ?',
            [
                'Composer License Metadata vs. Dependency Reality: Screening 10,000 Packages',
                'A package license only covers its author\'s code. We scanned 10,000 Composer packages and 100 WordPress plugins. 320 packages pulled in copyleft code while claiming a permissive license at the root, including PayPal\'s server SDK and WPForms Lite.',
                $articleHtml,
                9,
                new \DateTimeImmutable('2026-08-30 15:25:00+00:00'),
                'composer-license-metadata-dependency-audit',
            ],
            [
                Types::STRING,
                Types::STRING,
                Types::TEXT,
                Types::INTEGER,
                Types::DATETIMETZ_IMMUTABLE,
                Types::STRING,
            ]
        );
    }

    public function down(Schema $schema): void
    {
    }
}
