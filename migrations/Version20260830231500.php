<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830231500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update Composer license article text to use first-person singular voice';
    }

    public function up(Schema $schema): void
    {
        $articleHtml = <<<'HTML'
<p class="article-lead">I scanned 10,000 top PHP packages and 100 popular WordPress plugins. What I found is an alarming compliance blind spot: 320 packages pull viral copyleft code into projects that claim simple MIT terms. Real SDKs from PayPal and plugins on 5 million live sites distribute OSL-3.0 and GPL files under innocent labels. If you build SaaS, online shops, or private code, here is what hides in your vendor folder. Real court cases show you could face heavy lawsuits.</p>

<div class="article-callout article-callout-accent"><strong>The short version</strong><span>A package license only covers code written by its direct author. In my scan of 10,000 Composer packages, 320 libraries (3.2%) pulled in copyleft code while claiming MIT at the root. Real courts have ordered 800,000 EUR in damages for open source license breaches. Here are the exact dependency paths, the real risks, and how to protect your code today.</span></div>

<h2>What open source lawsuits can cost</h2>
<p>Open source licenses are legally binding contracts. Courts in Europe and the United States enforce these terms with heavy costs for companies.</p>

<div class="article-callout article-callout-accent"><strong>Orange: EUR 800,000 in damages, plus EUR 60,000 in legal costs</strong><span>In February 2024, the Paris Court of Appeal ruled that Orange broke GPLv2 terms and infringed copyright in the Lasso library. The court ordered Orange to pay EUR 500,000 for business harm, EUR 150,000 for lost profits, and EUR 150,000 for moral harm. It also added EUR 60,000 for legal costs.</span></div>

<ul class="article-checklist">
  <li><strong>Jacobsen v. Katzer: $100,000 settlement and a court order.</strong> A US federal appeals court ruled that open source terms are binding under copyright law. The dispute ended with a $100,000 payment and a permanent ban on distribution.</li>
  <li><strong>Cisco and Linksys: forced source release and settlement.</strong> The Free Software Foundation sued Cisco over GPL code in Linksys routers. The settlement forced Cisco to appoint a compliance director, share complete source code, and pay an undisclosed sum.</li>
  <li><strong>Neo4j v. PureThink: $597,000 judgment and an injunction.</strong> A US federal court awarded hundreds of thousands of dollars in damages over stripped license notices and AGPL terms.</li>
  <li><strong>The hidden business damage:</strong> Beyond direct fines, courts can order you to stop shipping your product, share your private source code, rebuild your software, or cancel a company sale during due diligence.</li>
</ul>

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

<p>My method was simple: I recursively checked every single dependency across the entire tree to find any child package carrying a more restrictive license than the root project.</p>

<h2>Case study: the PayPal server SDK and OSL-3.0</h2>
<p>When PayPal retired older PHP SDKs, they told merchants to use <code>paypal/paypal-server-sdk</code>. On Packagist, PayPal listed this package as <code>MIT</code>.</p>
<p>Looking at the real dependency tree shows a very different picture:</p>

<figure class="article-evidence">
  <img src="/media/composer-license/packagist-paypal-2.4.0-mit.png" alt="Packagist metadata for paypal/paypal-server-sdk 2.4.0 showing MIT and its APIMatic requirements" width="1280" height="720" loading="lazy">
  <figcaption>Packagist on August 30, 2026. The web page shows the root MIT tag, but hides the child license chain.</figcaption>
</figure>

<pre class="article-code article-code-danger"><code>paypal/paypal-server-sdk@2.4.0 (Declared MIT)
 \-- apimatic/core@0.3.18 (Declared MIT)
      \-- apimatic/jsonmapper@3.1.7 (SPDX: OSL-3.0 - Open Software License 3.0)
           \-- forked from cweiske/jsonmapper (Copyright Christian Weiske &amp; Netresearch)</code></pre>

<p>The trap is <strong>OSL-3.0 Section 5 ("External Deployment")</strong>. Standard GPL only triggers when you ship files to users. OSL-3.0 treats web access as distribution:</p>

<div class="article-callout"><strong>The OSL-3.0 network trigger clause</strong><span><em>"The term 'External Deployment' means the use, distribution, or communication of the Original Work or Modifications in any way such that the Original Work or Modifications may be used by anyone other than You... shall be deemed a distribution under Section 3."</em></span></div>

<p>If you run an online shop or SaaS app with OSL-3.0 code, users can ask for your entire source code. PayPal built their SDK with APIMatic tools. APIMatic used an OSL-3.0 library. Any store installing PayPal server SDK pulled OSL-3.0 straight into their checkout stack.</p>

<figure class="article-evidence">
  <img src="/media/composer-license/tool-paypal-lock-audit.png" alt="StackHal Composer lockfile audit showing the exact PayPal, APIMatic Core, and APIMatic JsonMapper versions with review signals" width="1280" height="720" loading="lazy">
  <figcaption>Real browser audit of the PayPal SDK lockfile, highlighting the exact OSL-3.0 dependency chain.</figcaption>
</figure>

<h2>Case study: WPForms Lite and 5,000,000 websites</h2>
<p>WPForms Lite is the 9th most popular plugin on WordPress.org, running on over 5,000,000 active websites. WordPress.org rules state that all plugins must use GPLv2 or compatible terms.</p>
<p>To avoid name clashes, the authors used tools to add namespace prefixes. The plugin placed the APIMatic SDK in:</p>

<pre class="article-code"><code>wp-content/plugins/wpforms-lite/vendor_prefixed/apimatic/jsonmapper/</code></pre>

<p>This changed class names to <code>WPForms\Vendor\apimatic\jsonmapper\JsonMapper</code>, but kept the file comments. Five PHP files in that folder still hold the full Open Software License 3.0 text and copyright notices.</p>
<p>Prefix tools change PHP code names. They do not alter copyright terms. Bundling OSL-3.0 code inside a GPLv2 plugin creates an open conflict across millions of live sites.</p>

<h2>Five libraries behind most copyleft findings</h2>
<p>Most of the 320 flagged packages came from five upstream projects:</p>

<ul class="article-checklist">
  <li><strong><code>ezyang/htmlpurifier</code> (LGPL-2.1-or-later, 108 packages):</strong> Used by frameworks like <code>yiisoft/yii2</code> and tools like <code>mews/purifier</code>. Loading an untouched library fits LGPL rules, but authors often forget to include LGPL license texts and credit notices.</li>
  <li><strong><code>netresearch/jsonmapper</code> and <code>apimatic/jsonmapper</code> (OSL-3.0, 27 packages):</strong> Used in code tools, JSON-RPC servers (<code>danog/advanced-json-rpc</code>), and Psalm plugins.</li>
  <li><strong><code>enshrined/svg-sanitize</code> (GPL-2.0-or-later, 26 packages):</strong> Used by CMS plugins and store add-ons that claim MIT terms. GPL-2.0 has no linking exception, so putting it in closed software creates a direct conflict.</li>
  <li><strong><code>paragonie/halite</code> and <code>hidden-string</code> (MPL-2.0, 24 packages):</strong> Cryptography libraries. MPL-2.0 works at the file level, so you can use it in commercial apps if edits to MPL files stay open and notices remain.</li>
  <li><strong><code>dompdf/dompdf</code> and <code>smalot/pdfparser</code> (LGPL-2.1 / LGPL-3.0, 20+ packages):</strong> PDF tools used in invoice makers and Laravel wrappers like <code>barryvdh/laravel-dompdf</code>.</li>
</ul>

<h2>Why Composer never warns you</h2>
<p>When an author puts a package on Packagist, they write a license name in <code>composer.json</code>. Packagist stores that text and shows it on the web page. Composer reads it when you run <code>composer licenses</code>.</p>
<p>Neither tool checks whether that license works with child packages in your tree. If an MIT package needs a GPL or OSL library, Composer still installs it without warnings. The trap stays hidden until someone audits the full tree.</p>

<h2>AI agent rule: check licenses before adding code</h2>
<p>AI coding tools often pick packages based on popularity or root tags. Give your AI agents this rule so they check lockfiles before adding new packages:</p>

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

<h2>Full data and tools</h2>
<p>The full dataset of 10,000 packages, edge tables, WordPress logs, and scripts are open in the StackHal repository under <a href="https://github.com/bahdanhal/stackhal/tree/main/docs/research/composer-license-audit"><code>docs/research/composer-license-audit/</code></a>.</p>

<p class="article-sources"><strong>Enforcement sources and data:</strong> <a href="https://www.courdecassation.fr/decision/65cdbcdf2425a70008258563">Paris Court of Appeal, Entr'Ouvert v. Orange</a> | <a href="https://www.cafc.uscourts.gov/opinions-orders/08-1001.pdf">US Federal Circuit, Jacobsen v. Katzer</a> | <a href="https://www.jmri.org/k/Recent.shtml">JMRI settlement summary</a> | <a href="https://www.fsf.org/news/2009-05-cisco-settlement.html">FSF and Cisco settlement</a> | <a href="https://app.midpage.ai/document/neo4j-inc-v-purethink-llc-1000406331297">Neo4j v. PureThink findings and order</a> | <a href="https://github.com/bahdanhal/stackhal/tree/main/docs/research/composer-license-audit">StackHal Research Dataset</a></p>
HTML;

        $this->addSql(
            'UPDATE blog_articles SET title = ?, description = ?, content_html = ?, read_time_minutes = ?, updated_at = ? WHERE slug = ?',
            [
                'I Scanned 10,000 PHP Packages and Found Hundreds of Hidden License Traps: Here Is Why You Could Get Sued',
                'In an audit of 10,000 Composer packages and top WordPress plugins, 320 libraries pulled copyleft code under permissive root tags. See real court cases and exact dependency risks.',
                $articleHtml,
                9,
                new \DateTimeImmutable('2026-08-30 23:15:00+00:00'),
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
