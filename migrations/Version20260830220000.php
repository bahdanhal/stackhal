<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add verified open source enforcement cases and business impact to the Composer license article';
    }

    public function up(Schema $schema): void
    {
        $oldLead = '<p class="article-lead">A package can use the MIT license while one of its dependencies uses GPL, OSL, LGPL, or MPL. This is not proof of a license breach, but it is a reason to inspect the exact code and terms before release.</p>';
        $newLead = '<p class="article-lead">Open source license terms have been enforced in court. Some cases led to six-figure payments and limits on product use. This report starts with those cases, then shows how an exact PHP dependency path can expose the same risk before release.</p>';
        $oldSummary = '<div class="article-callout article-callout-accent"><strong>The short version</strong><span>We screened 10,000 Composer packages and 100 WordPress plugins. The first pass found 320 review candidates and recorded their license metadata and dependency paths. It did not decide if any package or project broke a license.</span></div>';
        $newSummary = '<div class="article-callout article-callout-accent"><strong>The business case</strong><span>Orange was ordered to pay EUR 800,000 in a GPLv2 case. Other disputes ended with payments, injunctions, and formal compliance programs. Our package screen shows where to look early, but it does not declare a violation.</span></div>';
        $marker = '<h2>What Composer tells you</h2>';
        $businessHtml = <<<'HTML'
<section id="business-risk">
<h2>What license risk can cost</h2>
<p>Yes, companies have paid after open source license failures. There is no fixed GPL fine. Courts look at the facts, terms, harm, and local law.</p>

<div class="article-callout article-callout-accent"><strong>Orange: EUR 800,000 in damages, plus EUR 60,000 in legal costs</strong><span>In February 2024, the Paris Court of Appeal ruled that Orange and Orange Business Services broke GPLv2 terms and infringed Lasso's copyright. The court awarded EUR 500,000 for economic harm, EUR 150,000 for profits, and EUR 150,000 for moral harm. It also ordered EUR 60,000 toward Entr'Ouvert's legal costs. This was civil compensation, not a standard GPL fine.</span></div>

<ul class="article-checklist">
  <li><strong>Jacobsen v. Katzer: $100,000 settlement and an injunction.</strong> A US appeals court held that open source license conditions can be enforced under copyright law. The wider dispute ended in 2010 with a $100,000 payment and a permanent injunction. The case also involved patent and domain claims, so the payment was not a stand-alone license damages award.</li>
  <li><strong>Cisco and Linksys: public compliance terms, private amount.</strong> The Free Software Foundation sued over GPL and LGPL compliance. The 2009 settlement required a compliance director, source code notices, and continued compliance work. Cisco also made a payment to the FSF, but the amount was not disclosed.</li>
  <li><strong>Neo4j v. PureThink: $597,000 and a permanent injunction.</strong> A US district court made the award in 2024. Yet this was not a clean AGPL award. The court tied the loss to trademark misuse, false ads, and removed license data. The judgment was appealed.</li>
</ul>

<h2>Why Composer changes the scale</h2>
<p>Composer is not the legal problem. The scale comes from child packages. One direct package can pull in many more packages with different terms.</p>
<p>Our screen covered 10,000 Composer packages and 100 WordPress plugins. It found 320 review candidates, or 3.2% of the package set. The corrected data contains 654 flagged dependency edges. This was a ranked sample, not a random market survey, so the rate is not an industry estimate.</p>

<ul class="article-checklist">
  <li><strong>Money:</strong> a court may award lost income, profits, legal costs, or other relief allowed by local law.</li>
  <li><strong>Product limits:</strong> a court order can stop use, sale, or distribution while the dispute is fixed.</li>
  <li><strong>Source duties:</strong> the facts may trigger duties to share source, keep notices, or make a source offer. The scope depends on the license and use.</li>
  <li><strong>Remediation:</strong> the company may need a commercial license, a new component, rebuilt releases, and customer notices.</li>
  <li><strong>Deal delay:</strong> an unresolved dependency can slow procurement, due diligence, funding, or an acquisition.</li>
</ul>

<p>For management, the larger risk is often the remedy around the payment. A team may need to replace code, rebuild releases, contact customers, and pause a launch or transaction.</p>
<p>A scan cannot price that exposure. It can show the exact package and path early, while the company still has cheap options.</p>

<p class="article-sources"><strong>Enforcement sources:</strong> <a href="https://www.courdecassation.fr/decision/65cdbcdf2425a70008258563">Paris Court of Appeal, Entr'Ouvert v. Orange</a> | <a href="https://www.cafc.uscourts.gov/opinions-orders/08-1001.pdf">US Federal Circuit, Jacobsen v. Katzer</a> | <a href="https://www.jmri.org/k/Recent.shtml">JMRI settlement summary</a> | <a href="https://www.fsf.org/news/2009-05-cisco-settlement.html">FSF and Cisco settlement</a> | <a href="https://app.midpage.ai/document/neo4j-inc-v-purethink-llc-1000406331297">Neo4j v. PureThink findings and order</a></p>
</section>

HTML;

        $this->addSql(
            'UPDATE blog_articles SET title = ?, content_html = replace(replace(replace(content_html, ?, ?), ?, ?), ?, ?), description = ?, read_time_minutes = ?, updated_at = ? WHERE slug = ? AND locale = ? AND strpos(content_html, ?) = 0',
            [
                'Open Source License Violations: Real Cases, Costs, and Dependency Evidence',
                $oldLead,
                $newLead,
                $oldSummary,
                $newSummary,
                $marker,
                $businessHtml . $marker,
                'What Orange, Cisco, Jacobsen, and Neo4j show about open source license risk, plus a reproducible Composer screen for finding exact dependency paths.',
                10,
                new \DateTimeImmutable('2026-08-30 22:00:00+00:00'),
                'composer-license-metadata-dependency-audit',
                'en',
                'id="business-risk"',
            ],
            [
                Types::STRING,
                Types::TEXT,
                Types::TEXT,
                Types::TEXT,
                Types::TEXT,
                Types::TEXT,
                Types::TEXT,
                Types::STRING,
                Types::INTEGER,
                Types::DATETIMETZ_IMMUTABLE,
                Types::STRING,
                Types::STRING,
                Types::STRING,
            ]
        );
    }

    public function down(Schema $schema): void
    {
        $oldLead = '<p class="article-lead">A package can use the MIT license while one of its dependencies uses GPL, OSL, LGPL, or MPL. This is not proof of a license breach, but it is a reason to inspect the exact code and terms before release.</p>';
        $newLead = '<p class="article-lead">Open source license terms have been enforced in court. Some cases led to six-figure payments and limits on product use. This report starts with those cases, then shows how an exact PHP dependency path can expose the same risk before release.</p>';
        $oldSummary = '<div class="article-callout article-callout-accent"><strong>The short version</strong><span>We screened 10,000 Composer packages and 100 WordPress plugins. The first pass found 320 review candidates and recorded their license metadata and dependency paths. It did not decide if any package or project broke a license.</span></div>';
        $newSummary = '<div class="article-callout article-callout-accent"><strong>The business case</strong><span>Orange was ordered to pay EUR 800,000 in a GPLv2 case. Other disputes ended with payments, injunctions, and formal compliance programs. Our package screen shows where to look early, but it does not declare a violation.</span></div>';
        $marker = '<h2>What Composer tells you</h2>';
        $businessHtml = <<<'HTML'
<section id="business-risk">
<h2>What license risk can cost</h2>
<p>Yes, companies have paid after open source license failures. There is no fixed GPL fine. Courts look at the facts, terms, harm, and local law.</p>

<div class="article-callout article-callout-accent"><strong>Orange: EUR 800,000 in damages, plus EUR 60,000 in legal costs</strong><span>In February 2024, the Paris Court of Appeal ruled that Orange and Orange Business Services broke GPLv2 terms and infringed Lasso's copyright. The court awarded EUR 500,000 for economic harm, EUR 150,000 for profits, and EUR 150,000 for moral harm. It also ordered EUR 60,000 toward Entr'Ouvert's legal costs. This was civil compensation, not a standard GPL fine.</span></div>

<ul class="article-checklist">
  <li><strong>Jacobsen v. Katzer: $100,000 settlement and an injunction.</strong> A US appeals court held that open source license conditions can be enforced under copyright law. The wider dispute ended in 2010 with a $100,000 payment and a permanent injunction. The case also involved patent and domain claims, so the payment was not a stand-alone license damages award.</li>
  <li><strong>Cisco and Linksys: public compliance terms, private amount.</strong> The Free Software Foundation sued over GPL and LGPL compliance. The 2009 settlement required a compliance director, source code notices, and continued compliance work. Cisco also made a payment to the FSF, but the amount was not disclosed.</li>
  <li><strong>Neo4j v. PureThink: $597,000 and a permanent injunction.</strong> A US district court made the award in 2024. Yet this was not a clean AGPL award. The court tied the loss to trademark misuse, false ads, and removed license data. The judgment was appealed.</li>
</ul>

<h2>Why Composer changes the scale</h2>
<p>Composer is not the legal problem. The scale comes from child packages. One direct package can pull in many more packages with different terms.</p>
<p>Our screen covered 10,000 Composer packages and 100 WordPress plugins. It found 320 review candidates, or 3.2% of the package set. The corrected data contains 654 flagged dependency edges. This was a ranked sample, not a random market survey, so the rate is not an industry estimate.</p>

<ul class="article-checklist">
  <li><strong>Money:</strong> a court may award lost income, profits, legal costs, or other relief allowed by local law.</li>
  <li><strong>Product limits:</strong> a court order can stop use, sale, or distribution while the dispute is fixed.</li>
  <li><strong>Source duties:</strong> the facts may trigger duties to share source, keep notices, or make a source offer. The scope depends on the license and use.</li>
  <li><strong>Remediation:</strong> the company may need a commercial license, a new component, rebuilt releases, and customer notices.</li>
  <li><strong>Deal delay:</strong> an unresolved dependency can slow procurement, due diligence, funding, or an acquisition.</li>
</ul>

<p>For management, the larger risk is often the remedy around the payment. A team may need to replace code, rebuild releases, contact customers, and pause a launch or transaction.</p>
<p>A scan cannot price that exposure. It can show the exact package and path early, while the company still has cheap options.</p>

<p class="article-sources"><strong>Enforcement sources:</strong> <a href="https://www.courdecassation.fr/decision/65cdbcdf2425a70008258563">Paris Court of Appeal, Entr'Ouvert v. Orange</a> | <a href="https://www.cafc.uscourts.gov/opinions-orders/08-1001.pdf">US Federal Circuit, Jacobsen v. Katzer</a> | <a href="https://www.jmri.org/k/Recent.shtml">JMRI settlement summary</a> | <a href="https://www.fsf.org/news/2009-05-cisco-settlement.html">FSF and Cisco settlement</a> | <a href="https://app.midpage.ai/document/neo4j-inc-v-purethink-llc-1000406331297">Neo4j v. PureThink findings and order</a></p>
</section>

HTML;

        $this->addSql(
            'UPDATE blog_articles SET title = ?, content_html = replace(replace(replace(content_html, ?, ?), ?, ?), ?, ?), description = ?, read_time_minutes = ?, updated_at = ? WHERE slug = ? AND locale = ?',
            [
                'Composer Licenses: What a Dependency Graph Can and Cannot Prove',
                $businessHtml . $marker,
                $marker,
                $newSummary,
                $oldSummary,
                $newLead,
                $oldLead,
                'A reproducible screen of 10,000 Composer packages, with corrected license groups, exact dependency paths, and clear limits on automated findings.',
                8,
                new \DateTimeImmutable('2026-08-30 16:30:00+00:00'),
                'composer-license-metadata-dependency-audit',
                'en',
            ],
            [
                Types::STRING,
                Types::TEXT,
                Types::TEXT,
                Types::TEXT,
                Types::TEXT,
                Types::TEXT,
                Types::TEXT,
                Types::STRING,
                Types::INTEGER,
                Types::DATETIMETZ_IMMUTABLE,
                Types::STRING,
                Types::STRING,
            ]
        );
    }
}
