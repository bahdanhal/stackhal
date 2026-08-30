<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830235000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update Composer license article with responsive no-scroll 320 packages list';
    }

    public function up(Schema $schema): void
    {
        $articleHtml = <<<'HTML'
<p class="article-lead">I scanned 10,000 top PHP packages and 100 popular WordPress plugins. What I found is an alarming compliance blind spot: 320 packages pull viral copyleft code into projects that claim simple MIT terms. Real SDKs from PayPal and plugins on 5 million live sites distribute OSL-3.0 and GPL files under innocent labels. If you build SaaS, online shops, or private code, here is what hides in your vendor folder. Real court cases show you could face heavy lawsuits.</p>

<div class="article-callout article-callout-accent"><strong>The short version</strong><span>A package license only covers code written by its direct author. In my scan of 10,000 Composer packages, 320 libraries (3.2%) pulled in copyleft code while claiming MIT at the root. Real courts have ordered 800,000 EUR in damages for open source license breaches. Here are the exact dependency paths, the real risks, and how to protect your code today.</span></div>

<details class="code-spoiler-widget audit-dataset-dropdown">
  <summary>Inspect all 320 flagged packages from the 10,000-package audit</summary>
  <div class="spoiler-body">
    <p class="dropdown-intro">Below is the complete list of 320 packages from the top 10,000 Packagist scan that pulled in copyleft or restrictive dependencies under different root declarations.</p>
    <div class="audit-pkg-list">
    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#243</span>
        <code class="pkg-name">barryvdh/laravel-dompdf</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#454</span>
        <code class="pkg-name">wp-coding-standards/wpcs</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsextra</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#506</span>
        <code class="pkg-name">vimeo/psalm</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#587</span>
        <code class="pkg-name">sirbrillig/phpcs-variable-analysis</code>
        <span class="badge-root">BSD-2-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#657</span>
        <code class="pkg-name">danog/advanced-json-rpc</code>
        <span class="badge-root">ISC</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#678</span>
        <code class="pkg-name">felixfbecker/advanced-json-rpc</code>
        <span class="badge-root">ISC</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#747</span>
        <code class="pkg-name">mews/purifier</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#760</span>
        <code class="pkg-name">mglaman/phpstan-drupal</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">webflo/drupal-finder</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#877</span>
        <code class="pkg-name">horstoeko/zugferd</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">smalot/pdfparser</code> <span class="badge-weak">LGPL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1063</span>
        <code class="pkg-name">yiisoft/yii2</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1113</span>
        <code class="pkg-name">roots/wordpress</code>
        <span class="badge-root">MIT, GPL-2.0-or-later</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">roots/wordpress-core-installer</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">roots/wordpress-no-content</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1122</span>
        <code class="pkg-name">stevebauman/purify</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1130</span>
        <code class="pkg-name">automattic/vipwpcs</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpcsstandards/phpcsextra</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1214</span>
        <code class="pkg-name">mkalkbrenner/php-htmldiff-advanced</code>
        <span class="badge-root">GNU General Public License V2</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">caxy/php-htmldiff</code> <span class="badge-strong">GPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1225</span>
        <code class="pkg-name">yiisoft/yii2-debug</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1283</span>
        <code class="pkg-name">apimatic/core</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">apimatic/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1343</span>
        <code class="pkg-name">iio/libmergepdf</code>
        <span class="badge-root">WTFPL</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">tecnickcom/tcpdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-pdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-barcode</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+11 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1402</span>
        <code class="pkg-name">vladimir-yuldashev/laravel-queue-rabbitmq</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1457</span>
        <code class="pkg-name">wikimedia/css-sanitizer</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">wikimedia/utfnormal</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">wikimedia/scoped-callback</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1487</span>
        <code class="pkg-name">yiisoft/yii2-queue</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1499</span>
        <code class="pkg-name">psalm/plugin-symfony</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1548</span>
        <code class="pkg-name">simplesamlphp/saml2</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">simplesamlphp/assert</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">simplesamlphp/xml-common</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">simplesamlphp/composer-xmlprovider-installer</code> <span class="badge-weak">LGPL-2.1-only</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+2 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1568</span>
        <code class="pkg-name">woocommerce/woocommerce-sniffs</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsextra</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1579</span>
        <code class="pkg-name">phan/phan</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1583</span>
        <code class="pkg-name">yiisoft/yii2-gii</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1590</span>
        <code class="pkg-name">php-amqplib/rabbitmq-bundle</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1591</span>
        <code class="pkg-name">hisorange/browser-detect</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">matomo/device-detector</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1606</span>
        <code class="pkg-name">yiisoft/yii2-redis</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1615</span>
        <code class="pkg-name">exercise/htmlpurifier-bundle</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1627</span>
        <code class="pkg-name">yiisoft/yii2-bootstrap</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1706</span>
        <code class="pkg-name">yiisoft/yii2-faker</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1715</span>
        <code class="pkg-name">yiisoft/yii2-httpclient</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1780</span>
        <code class="pkg-name">paypal/paypal-server-sdk</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">apimatic/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1819</span>
        <code class="pkg-name">shopware/core</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1822</span>
        <code class="pkg-name">yiisoft/yii2-symfonymailer</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1870</span>
        <code class="pkg-name">shopware/storefront</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1878</span>
        <code class="pkg-name">prestashop/translationtools-bundle</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">smarty/smarty</code> <span class="badge-weak">LGPL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1879</span>
        <code class="pkg-name">statamic/cms</code>
        <span class="badge-root">proprietary</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">james-heinrich/getid3</code> <span class="badge-strong">GPL-1.0-or-later, LGPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1949</span>
        <code class="pkg-name">palantirnet/drupal-rector</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">webflo/drupal-finder</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1979</span>
        <code class="pkg-name">lesstif/php-jira-rest-client</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#1996</span>
        <code class="pkg-name">wamania/php-stemmer</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">joomla/string</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2008</span>
        <code class="pkg-name">yiisoft/yii2-swiftmailer</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2010</span>
        <code class="pkg-name">workos/workos-php</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2050</span>
        <code class="pkg-name">square/square</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">apimatic/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2064</span>
        <code class="pkg-name">coenjacobs/mozart</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2070</span>
        <code class="pkg-name">shopware/elasticsearch</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2079</span>
        <code class="pkg-name">shopware/administration</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2084</span>
        <code class="pkg-name">shopware/conflicts</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2108</span>
        <code class="pkg-name">wp-cli/wp-cli-tests</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">wp-hooks/wordpress-core</code> <span class="badge-strong">GPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsextra</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2119</span>
        <code class="pkg-name">enqueue/amqp-lib</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2126</span>
        <code class="pkg-name">lucatume/wp-browser</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">druidfi/mysqldump-php</code> <span class="badge-strong">GPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2135</span>
        <code class="pkg-name">yiisoft/yii2-jui</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2160</span>
        <code class="pkg-name">vardot/ckeditor5-anchor-drupal</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">drupal/core</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">drupal/core-composer-scaffold</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2213</span>
        <code class="pkg-name">psalm/plugin-laravel</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2247</span>
        <code class="pkg-name">carlos-meneses/laravel-mpdf</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">mpdf/mpdf</code> <span class="badge-strong">GPL-2.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2305</span>
        <code class="pkg-name">setasign/fpdi-tcpdf</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">tecnickcom/tcpdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-pdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-barcode</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+11 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2326</span>
        <code class="pkg-name">weirdan/doctrine-psalm-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2387</span>
        <code class="pkg-name">yiisoft/yii2-authclient</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2400</span>
        <code class="pkg-name">saschaegerer/phpstan-typo3</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3/cms-core</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">typo3/cms-cli</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+4 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2436</span>
        <code class="pkg-name">ssch/typo3-debug-dump-pass</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3/cms-core</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">typo3/cms-cli</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+2 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2439</span>
        <code class="pkg-name">kartik-v/yii2-mpdf</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">mpdf/mpdf</code> <span class="badge-strong">GPL-2.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2452</span>
        <code class="pkg-name">johnbillion/wp-compat</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">wp-hooks/wordpress-core</code> <span class="badge-strong">GPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2457</span>
        <code class="pkg-name">sylius/sylius</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2475</span>
        <code class="pkg-name">nfephp-org/sped-da</code>
        <span class="badge-root">LGPL-3.0-or-later+, GPL-3.0-or-later, MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">tecnickcom/tc-lib-barcode</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-color</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2492</span>
        <code class="pkg-name">consoletvs/charts</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">balping/json-raw-encoder</code> <span class="badge-strong">GPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2523</span>
        <code class="pkg-name">mike42/escpos-php</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">mike42/gfx-php</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2670</span>
        <code class="pkg-name">cloudconvert/cloudconvert-php</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2684</span>
        <code class="pkg-name">yiisoft/yii2-imagine</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2723</span>
        <code class="pkg-name">xemlock/htmlpurifier-html5</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2762</span>
        <code class="pkg-name">nystudio107/craft-code-editor</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2819</span>
        <code class="pkg-name">apache-solr-for-typo3/solr</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3/cms-backend</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">typo3/cms-core</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+10 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2822</span>
        <code class="pkg-name">bschmitt/laravel-amqp</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2851</span>
        <code class="pkg-name">lesstif/jira-cloud-restapi</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2965</span>
        <code class="pkg-name">yiisoft/yii2-bootstrap5</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#2970</span>
        <code class="pkg-name">yiisoft/yii2-bootstrap4</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3035</span>
        <code class="pkg-name">verbb/base</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3123</span>
        <code class="pkg-name">elibyy/tcpdf-laravel</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">tecnickcom/tcpdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-pdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-barcode</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+11 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3182</span>
        <code class="pkg-name">psalm/plugin-mockery</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3211</span>
        <code class="pkg-name">rmrevin/yii2-fontawesome</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3218</span>
        <code class="pkg-name">creocoder/yii2-nested-sets</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3254</span>
        <code class="pkg-name">stellarwp/superglobals</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">stellarwp/arrays</code> <span class="badge-strong">GPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3306</span>
        <code class="pkg-name">alibabacloud/client</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">clagiordano/weblibs-configmanager</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3318</span>
        <code class="pkg-name">nucleos/dompdf-bundle</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3323</span>
        <code class="pkg-name">rubix/ml</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">joomla/string</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3394</span>
        <code class="pkg-name">kartik-v/yii2-export</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">mpdf/mpdf</code> <span class="badge-strong">GPL-2.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3440</span>
        <code class="pkg-name">niklasravnsborg/laravel-pdf</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">mpdf/mpdf</code> <span class="badge-strong">GPL-2.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3492</span>
        <code class="pkg-name">statamic/eloquent-driver</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">james-heinrich/getid3</code> <span class="badge-strong">GPL-1.0-or-later, LGPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3565</span>
        <code class="pkg-name">propa/tcpdi</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">tecnickcom/tcpdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-pdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-barcode</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+11 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3577</span>
        <code class="pkg-name">laravel/workos</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3581</span>
        <code class="pkg-name">samdark/yii2-psr-log-target</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3598</span>
        <code class="pkg-name">neos/fluid-adaptor</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3fluid/fluid</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3656</span>
        <code class="pkg-name">mindee/mindee</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">smalot/pdfparser</code> <span class="badge-weak">LGPL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3663</span>
        <code class="pkg-name">yiisoft/yii2-shell</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3681</span>
        <code class="pkg-name">plank/laravel-mediable</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3692</span>
        <code class="pkg-name">theodo-group/llphant</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpoffice/phpword</code> <span class="badge-weak">LGPL-3.0-only</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">smalot/pdfparser</code> <span class="badge-weak">LGPL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3704</span>
        <code class="pkg-name">sylius/telemetry</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3706</span>
        <code class="pkg-name">workos/workos-php-laravel</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3777</span>
        <code class="pkg-name">karriere/pdf-merge</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">tecnickcom/tcpdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-pdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-barcode</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+11 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3790</span>
        <code class="pkg-name">nystudio107/craft-twig-sandbox</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3800</span>
        <code class="pkg-name">codex-team/editor.js</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3806</span>
        <code class="pkg-name">contao-components/colorbox</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3856</span>
        <code class="pkg-name">craftcms/cms</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3866</span>
        <code class="pkg-name">stevebauman/hypertext</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3868</span>
        <code class="pkg-name">sylius/paypal-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3910</span>
        <code class="pkg-name">frosh/tools</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#3972</span>
        <code class="pkg-name">sylius/refund-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4106</span>
        <code class="pkg-name">retailcrm/api-client-php</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">pnz/json-exception</code> <span class="badge-strong">GPL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4126</span>
        <code class="pkg-name">spryker/rabbit-mq</code>
        <span class="badge-root">proprietary</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4210</span>
        <code class="pkg-name">advoor/nova-editor-js</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4236</span>
        <code class="pkg-name">log1x/acf-composer</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">stoutlogic/acf-builder</code> <span class="badge-strong">GPL-2.0+</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4316</span>
        <code class="pkg-name">php-collective/code-sniffer</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpcsstandards/phpcsextra</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4328</span>
        <code class="pkg-name">doctrineencryptbundle/doctrine-encrypt-bundle</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4371</span>
        <code class="pkg-name">statamic/seo-pro</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">james-heinrich/getid3</code> <span class="badge-strong">GPL-1.0-or-later, LGPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4408</span>
        <code class="pkg-name">atgp/factur-x</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">smalot/pdfparser</code> <span class="badge-weak">LGPL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4469</span>
        <code class="pkg-name">contao-components/simplemodal</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4475</span>
        <code class="pkg-name">oneduo/nova-file-manager</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">james-heinrich/getid3</code> <span class="badge-strong">GPL-1.0-or-later, LGPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4480</span>
        <code class="pkg-name">contao-components/ace</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4533</span>
        <code class="pkg-name">contao-components/swiper</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4538</span>
        <code class="pkg-name">sylius/mollie-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4540</span>
        <code class="pkg-name">contao-components/datepicker</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4546</span>
        <code class="pkg-name">himiklab/yii2-recaptcha-widget</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4569</span>
        <code class="pkg-name">rokka/imagine-vips</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phenx/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4600</span>
        <code class="pkg-name">php-standard-library/psalm-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4649</span>
        <code class="pkg-name">verbb/auth</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4652</span>
        <code class="pkg-name">contao-components/jquery</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4656</span>
        <code class="pkg-name">fig-r/psr2r-sniffer</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpcsstandards/phpcsextra</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4663</span>
        <code class="pkg-name">contao-components/dropzone</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4664</span>
        <code class="pkg-name">contao-components/tablesorter</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4668</span>
        <code class="pkg-name">contao-components/mootools</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4669</span>
        <code class="pkg-name">contao-components/swipe</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4675</span>
        <code class="pkg-name">contao-components/mediabox</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4678</span>
        <code class="pkg-name">contao-components/jquery-ui</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4684</span>
        <code class="pkg-name">10up/phpcs-composer</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpcsstandards/phpcsextra</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4773</span>
        <code class="pkg-name">seatsio/seatsio-php</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4852</span>
        <code class="pkg-name">justbetter/magento2-sentry</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">justbetter/magento2-core</code> <span class="badge-strong">GPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4861</span>
        <code class="pkg-name">reload/jira-security-issue</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4863</span>
        <code class="pkg-name">infocyph/phpforge</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4868</span>
        <code class="pkg-name">contao-components/tristen-tablesort</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4891</span>
        <code class="pkg-name">alibabacloud/sdk</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">clagiordano/weblibs-configmanager</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4917</span>
        <code class="pkg-name">awcodes/filament-curator</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4918</span>
        <code class="pkg-name">magefan/module-blog</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">magefan/module-wysiwyg-advanced</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4937</span>
        <code class="pkg-name">notamedia/yii2-sentry</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#4957</span>
        <code class="pkg-name">jwage/phpamqplib-messenger</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5005</span>
        <code class="pkg-name">cebe/yii2-gravatar</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5010</span>
        <code class="pkg-name">dmstr/yii2-adminlte-asset</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5077</span>
        <code class="pkg-name">bk2k/bootstrap-package</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3/cms-backend</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">typo3/cms-core</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+10 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5081</span>
        <code class="pkg-name">contao-components/handorgel</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5085</span>
        <code class="pkg-name">sylius/invoicing-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5086</span>
        <code class="pkg-name">unclead/yii2-multiple-input</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5110</span>
        <code class="pkg-name">contao-components/choices</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5138</span>
        <code class="pkg-name">hyperf/amqp</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5200</span>
        <code class="pkg-name">amazeeio/drupal_integrations</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">drupal/core-composer-scaffold</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5207</span>
        <code class="pkg-name">yiisoft/yii2-elasticsearch</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5251</span>
        <code class="pkg-name">sulu/sulu</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao/imagine-svg</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">matomo/device-detector</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5252</span>
        <code class="pkg-name">yii2-extensions/phpstan</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5352</span>
        <code class="pkg-name">spryker/opentelemetry</code>
        <span class="badge-root">proprietary</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5361</span>
        <code class="pkg-name">miloschuman/yii2-highcharts-widget</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5402</span>
        <code class="pkg-name">loupe/loupe</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">joomla/string</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5428</span>
        <code class="pkg-name">roave/infection-static-analysis-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5490</span>
        <code class="pkg-name">2amigos/yii2-ckeditor-widget</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ckeditor/ckeditor</code> <span class="badge-strong">GPL-2.0+</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5537</span>
        <code class="pkg-name">cloudconvert/cloudconvert-laravel</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5549</span>
        <code class="pkg-name">mdmsoft/yii2-admin</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5556</span>
        <code class="pkg-name">getkirby/cms</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpmailer/phpmailer</code> <span class="badge-weak">LGPL-2.1-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5575</span>
        <code class="pkg-name">creocoder/yii2-flysystem</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5576</span>
        <code class="pkg-name">pear/pear</code>
        <span class="badge-root">BSD-2-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">pear/structures_graph</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5637</span>
        <code class="pkg-name">yiisoft/yii2-mongodb</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5642</span>
        <code class="pkg-name">wyrihaximus/css-compress</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">websharks/css-minifier</code> <span class="badge-strong">GPL-3.0+</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5655</span>
        <code class="pkg-name">yiisoft/yii2-twig</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5721</span>
        <code class="pkg-name">codemix/yii2-localeurls</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5773</span>
        <code class="pkg-name">tornevall/tornelib-php-database</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5830</span>
        <code class="pkg-name">craftcms/html-field</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5862</span>
        <code class="pkg-name">qipsius/tcpdf-bundle</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">tecnickcom/tcpdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-pdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-barcode</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+11 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5995</span>
        <code class="pkg-name">spryker-feature/spryker-core</code>
        <span class="badge-root">proprietary</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#5997</span>
        <code class="pkg-name">codewithkyrian/transformers</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phenx/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6129</span>
        <code class="pkg-name">neos/kickstarter</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3fluid/fluid</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6190</span>
        <code class="pkg-name">drupal/config_sync_without_site_uuid</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">drupal/core</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">drupal/core-composer-scaffold</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6214</span>
        <code class="pkg-name">yii2tech/csv-grid</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6220</span>
        <code class="pkg-name">contao-components/altcha</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6246</span>
        <code class="pkg-name">yii2tech/ar-softdelete</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6255</span>
        <code class="pkg-name">netlogix/webapi</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3fluid/fluid</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6261</span>
        <code class="pkg-name">oxid-esales/oxideshop-ce</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpmailer/phpmailer</code> <span class="badge-weak">LGPL-2.1-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6270</span>
        <code class="pkg-name">t3n/jobqueue-rabbitmq</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6289</span>
        <code class="pkg-name">netlogix/jobqueue-fast-rabbit</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6343</span>
        <code class="pkg-name">magefan/module-blog-comments-recaptcha</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">magefan/module-wysiwyg-advanced</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6431</span>
        <code class="pkg-name">anilcancakir/laravel-agent-mcp</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpmyadmin/sql-parser</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6524</span>
        <code class="pkg-name">2amigos/yii2-gallery-widget</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6574</span>
        <code class="pkg-name">omnilight/yii2-scheduling</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6600</span>
        <code class="pkg-name">nystudio107/craft-plugin-vite</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6752</span>
        <code class="pkg-name">october/rain</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6758</span>
        <code class="pkg-name">neos/media</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3fluid/fluid</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6759</span>
        <code class="pkg-name">lunarphp/lunar</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6770</span>
        <code class="pkg-name">2amigos/yii2-chartjs-widget</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6859</span>
        <code class="pkg-name">flux-se/sylius-stripe-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6865</span>
        <code class="pkg-name">cmsig/seal-loupe-adapter</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">joomla/string</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6875</span>
        <code class="pkg-name">rackspace/php-opencloud</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">mikemccabe/json-patch-php</code> <span class="badge-weak">LGPL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6885</span>
        <code class="pkg-name">asana/asana</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">adoy/oauth2</code> <span class="badge-weak">LGPL-2.1</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6907</span>
        <code class="pkg-name">php-soap/psr18-wsse-middleware</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#6924</span>
        <code class="pkg-name">dq5studios/psalm-junit</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7028</span>
        <code class="pkg-name">wyrihaximus/html-compress</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">websharks/css-minifier</code> <span class="badge-strong">GPL-3.0+</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7064</span>
        <code class="pkg-name">mollie/laravel-cashier-mollie</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7077</span>
        <code class="pkg-name">ibexa/headless</code>
        <span class="badge-root">proprietary</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ibexa/oss</code> <span class="badge-weak">(GPL-2.0-only or proprietary)</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ibexa/doctrine-schema</code> <span class="badge-weak">(GPL-2.0-only or proprietary)</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ibexa/system-info</code> <span class="badge-weak">(GPL-2.0-only or proprietary)</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+21 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7080</span>
        <code class="pkg-name">simple-bus/rabbitmq-bundle-bridge</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7087</span>
        <code class="pkg-name">light/yii2-swagger</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7101</span>
        <code class="pkg-name">bandwidth/sdk</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">apimatic/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7104</span>
        <code class="pkg-name">lullabot/twig-cs-fixer-drupal</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">drupal/core</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">drupal/core-composer-scaffold</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">webflo/drupal-finder</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7118</span>
        <code class="pkg-name">bizley/jwt</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7164</span>
        <code class="pkg-name">yoast/yoastcs</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpcsstandards/phpcsextra</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7223</span>
        <code class="pkg-name">codeception/module-amqp</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7224</span>
        <code class="pkg-name">ibexa/messenger</code>
        <span class="badge-root">proprietary</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ibexa/core</code> <span class="badge-weak">(GPL-2.0-only or proprietary)</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ibexa/doctrine-schema</code> <span class="badge-weak">(GPL-2.0-only or proprietary)</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ibexa/core-persistence</code> <span class="badge-weak">(GPL-2.0-only or proprietary)</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7248</span>
        <code class="pkg-name">kartik-v/yii2-icons</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7259</span>
        <code class="pkg-name">stefandoorn/sitemap-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7265</span>
        <code class="pkg-name">2amigos/yii2-file-upload-widget</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7281</span>
        <code class="pkg-name">2amigos/yii2-tinymce-widget</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7289</span>
        <code class="pkg-name">dektrium/yii2-user</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7316</span>
        <code class="pkg-name">yii2mod/yii2-enum</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7337</span>
        <code class="pkg-name">netresearch/typo3-ci-workflows</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3/cms-core</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">typo3/cms-cli</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+8 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7350</span>
        <code class="pkg-name">borales/yii2-phone-input</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7375</span>
        <code class="pkg-name">vova07/yii2-imperavi-widget</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7388</span>
        <code class="pkg-name">neos/fusion</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3fluid/fluid</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7410</span>
        <code class="pkg-name">chromatic/usher</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">drush/drush</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">chi-teck/drupal-code-generator</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7412</span>
        <code class="pkg-name">bitbag/elasticsearch-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7445</span>
        <code class="pkg-name">neos/fusion-afx</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3fluid/fluid</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7450</span>
        <code class="pkg-name">akaunting/laravel-apexcharts</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">balping/json-raw-encoder</code> <span class="badge-strong">GPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7452</span>
        <code class="pkg-name">liip/serializer</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">pnz/json-exception</code> <span class="badge-strong">GPL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7511</span>
        <code class="pkg-name">linslin/yii2-curl</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7537</span>
        <code class="pkg-name">acquia/blt</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">acquia/drupal-environment-detector</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">drupal/core</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">drupal/core-composer-scaffold</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+2 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7637</span>
        <code class="pkg-name">ehaerer/paste-reference</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3/cms-backend</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">typo3/cms-core</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7691</span>
        <code class="pkg-name">neos/neos</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3fluid/fluid</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">neos/fusion-form</code> <span class="badge-strong">GPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7725</span>
        <code class="pkg-name">neos/media-browser</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3fluid/fluid</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">neos/fusion-form</code> <span class="badge-strong">GPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7732</span>
        <code class="pkg-name">owen-oj/laravel-getid3</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">james-heinrich/getid3</code> <span class="badge-strong">GPL-1.0-or-later, LGPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7789</span>
        <code class="pkg-name">frosh/sentry-bundle</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7807</span>
        <code class="pkg-name">frosh/mail-platform-archive</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7821</span>
        <code class="pkg-name">dektrium/yii2-rbac</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7824</span>
        <code class="pkg-name">friendsofsylius/sylius-import-export-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7834</span>
        <code class="pkg-name">sizeg/yii2-jwt</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7936</span>
        <code class="pkg-name">moonlandsoft/yii2-phpexcel</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7970</span>
        <code class="pkg-name">mihaildev/yii2-elfinder</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#7984</span>
        <code class="pkg-name">wikimedia/remex-html</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">wikimedia/utfnormal</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8081</span>
        <code class="pkg-name">previousnext/coding-standard</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">drupal/coder</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8129</span>
        <code class="pkg-name">frosh/development-helper</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8137</span>
        <code class="pkg-name">mihaildev/yii2-ckeditor</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8152</span>
        <code class="pkg-name">marcorieser/statamic-livewire</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">james-heinrich/getid3</code> <span class="badge-strong">GPL-1.0-or-later, LGPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8160</span>
        <code class="pkg-name">sylius/cms-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8202</span>
        <code class="pkg-name">wbraganca/yii2-dynamicform</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8249</span>
        <code class="pkg-name">statamic-rad-pack/runway</code>
        <span class="badge-root">mit</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">james-heinrich/getid3</code> <span class="badge-strong">GPL-1.0-or-later, LGPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8254</span>
        <code class="pkg-name">calebdw/larastan</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpmyadmin/sql-parser</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8345</span>
        <code class="pkg-name">yiithings/yii2-dotenv</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8357</span>
        <code class="pkg-name">rias/statamic-redirect</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">james-heinrich/getid3</code> <span class="badge-strong">GPL-1.0-or-later, LGPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8380</span>
        <code class="pkg-name">liip/serializer-jms-adapter</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">pnz/json-exception</code> <span class="badge-strong">GPL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8388</span>
        <code class="pkg-name">rawilk/laravel-printing</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">mike42/gfx-php</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8399</span>
        <code class="pkg-name">alexandernst/yii2-device-detect</code>
        <span class="badge-root">GNU General Public License v3</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8404</span>
        <code class="pkg-name">firstred/postnl-api-php</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8410</span>
        <code class="pkg-name">contao-components/chosen</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8427</span>
        <code class="pkg-name">phalcon/quill</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">pds/composer-script-names</code> <span class="badge-strong">CC-BY-SA-4.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">pds/skeleton</code> <span class="badge-strong">CC-BY-SA-4.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8439</span>
        <code class="pkg-name">wptrt/wpthemereview</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsextra</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8467</span>
        <code class="pkg-name">kak/clickhouse</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8468</span>
        <code class="pkg-name">contributte/pdf</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">mpdf/mpdf</code> <span class="badge-strong">GPL-2.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8515</span>
        <code class="pkg-name">wikimedia/shellbox</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">wikimedia/base-convert</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8544</span>
        <code class="pkg-name">rebuy/amqp-php-consumer</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8547</span>
        <code class="pkg-name">horstoeko/zugferdvisualizer</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+2 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8577</span>
        <code class="pkg-name">contao-components/colorpicker</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">contao-components/installer</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8587</span>
        <code class="pkg-name">creagia/laravel-sign-pad</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">tecnickcom/tcpdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-pdf</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-barcode</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+11 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8605</span>
        <code class="pkg-name">ctidigital/magento2-configurator</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">firegento/fastsimpleimport</code> <span class="badge-strong">GPL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8609</span>
        <code class="pkg-name">humanmade/psalm-plugin-wordpress</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">wp-hooks/wordpress-core</code> <span class="badge-strong">GPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8622</span>
        <code class="pkg-name">sylius/adyen-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8629</span>
        <code class="pkg-name">friendsoftypo3/headless</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">typo3/cms-core</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">typo3/cms-cli</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+5 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8634</span>
        <code class="pkg-name">sylius/wishlist-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+2 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8681</span>
        <code class="pkg-name">pagarme/pagarme-php-sdk</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">apimatic/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8695</span>
        <code class="pkg-name">monsieurbiz/sylius-rich-editor-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8718</span>
        <code class="pkg-name">2amigos/yii2-selectize-widget</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8724</span>
        <code class="pkg-name">yiisoft/yii2-sphinx</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8729</span>
        <code class="pkg-name">phpdocumentor/phpdocumentor</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">jawira/plantuml</code> <span class="badge-strong">GPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8740</span>
        <code class="pkg-name">craftcms/flysystem</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8806</span>
        <code class="pkg-name">bashkarev/clickhouse</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8811</span>
        <code class="pkg-name">horstoeko/zugferd-laravel</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">smalot/pdfparser</code> <span class="badge-weak">LGPL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8827</span>
        <code class="pkg-name">jbzoo/codestyle</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8828</span>
        <code class="pkg-name">rmrevin/yii2-minify-view</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8911</span>
        <code class="pkg-name">codedredd/laravel-soap</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8936</span>
        <code class="pkg-name">craftcms/generator</code>
        <span class="badge-root">mit</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8982</span>
        <code class="pkg-name">orklah/psalm-insane-comparison</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#8989</span>
        <code class="pkg-name">studio1902/statamic-peak-tools</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">james-heinrich/getid3</code> <span class="badge-strong">GPL-1.0-or-later, LGPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9041</span>
        <code class="pkg-name">yii2tech/spreadsheet</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9061</span>
        <code class="pkg-name">jbzoo/toolbox-dev</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9116</span>
        <code class="pkg-name">asofter/yii2-imperavi-redactor</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9121</span>
        <code class="pkg-name">alperenersoy/filament-export</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9136</span>
        <code class="pkg-name">yiisoft/yii2-codeception</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9164</span>
        <code class="pkg-name">oxid-esales/oxideshop-metapackage-ce</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">makaira/oxid-connect-essential</code> <span class="badge-strong">GPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpmailer/phpmailer</code> <span class="badge-weak">LGPL-2.1-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9234</span>
        <code class="pkg-name">shopware/docker</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9263</span>
        <code class="pkg-name">xj/yii2-jplayer-widget</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9270</span>
        <code class="pkg-name">swag/paypal</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9294</span>
        <code class="pkg-name">craftcms/redactor</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9324</span>
        <code class="pkg-name">nystudio107/craft-seomatic</code>
        <span class="badge-root">proprietary</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9437</span>
        <code class="pkg-name">liuggio/excelbundle</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpoffice/phpexcel</code> <span class="badge-weak">LGPL-2.1</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9476</span>
        <code class="pkg-name">keepa/php_api</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9500</span>
        <code class="pkg-name">kdyby/rabbitmq</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">php-amqplib/php-amqplib</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9508</span>
        <code class="pkg-name">log1x/poet</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">johnbillion/extended-cpts</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">johnbillion/args</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9532</span>
        <code class="pkg-name">raoul2000/yii2-jcrop-widget</code>
        <span class="badge-root">BSD 3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9538</span>
        <code class="pkg-name">dhl/sdk-api-parcel-de</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9549</span>
        <code class="pkg-name">oro/oauth2-server</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">matomo/device-detector</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">oro/platform-serialised-fields</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9559</span>
        <code class="pkg-name">lctrs/psalm-psr-container-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">netresearch/jsonmapper</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9575</span>
        <code class="pkg-name">thamtech/yii2-ratelimiter-advanced</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9577</span>
        <code class="pkg-name">oro/calendar-bundle</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">matomo/device-detector</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">oro/platform-serialised-fields</code> <span class="badge-strong">OSL-3.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9616</span>
        <code class="pkg-name">winter/storm</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9619</span>
        <code class="pkg-name">faryshta/yii2-disable-submit-buttons</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9667</span>
        <code class="pkg-name">heptacom/shopware-platform-admin-open-auth</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-font-lib</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">dompdf/php-svg-lib</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9684</span>
        <code class="pkg-name">studio1902/statamic-peak-seo</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">james-heinrich/getid3</code> <span class="badge-strong">GPL-1.0-or-later, LGPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9692</span>
        <code class="pkg-name">kop/yii2-scroll-pager</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9714</span>
        <code class="pkg-name">2amigos/yii2-date-picker-widget</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9729</span>
        <code class="pkg-name">shopware/production</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">dompdf/dompdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">setasign/tfpdf</code> <span class="badge-weak">LGPL-2.1</span> <span class="pkg-dot">&bull;</span> <small class="pkg-more">+3 more</small>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9744</span>
        <code class="pkg-name">sylius/admin-api-bundle</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9765</span>
        <code class="pkg-name">yii2tech/html2pdf</code>
        <span class="badge-root">BSD-3-Clause</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9775</span>
        <code class="pkg-name">tig/postnl-magento2</code>
        <span class="badge-root">CC-BY-NC-ND-3.0</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">tecnickcom/tc-lib-barcode</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">tecnickcom/tc-lib-color</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9778</span>
        <code class="pkg-name">craftcms/feed-me</code>
        <span class="badge-root">proprietary</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9816</span>
        <code class="pkg-name">craftcms/aws-s3</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">enshrined/svg-sanitize</code> <span class="badge-strong">GPL-2.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9833</span>
        <code class="pkg-name">stefandoorn/google-tag-manager-plugin</code>
        <span class="badge-root">Unspecified</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">xynnn/google-tag-manager-bundle</code> <span class="badge-weak">LGPL-3.0-only</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9839</span>
        <code class="pkg-name">yooper/php-text-analysis</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-strong">Strong Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">joomla/string</code> <span class="badge-strong">GPL-2.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9861</span>
        <code class="pkg-name">thamtech/yii2-uuid</code>
        <span class="badge-root">Apache-2.0</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">ezyang/htmlpurifier</code> <span class="badge-weak">LGPL-2.1-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9862</span>
        <code class="pkg-name">sylius/test-application</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9896</span>
        <code class="pkg-name">pantheon-systems/pantheon-wp-coding-standards</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">phpcsstandards/phpcsextra</code> <span class="badge-weak">LGPL-3.0-or-later</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">phpcsstandards/phpcsutils</code> <span class="badge-weak">LGPL-3.0-or-later</span>
      </div>
    </div>    <div class="audit-pkg-item">
      <div class="pkg-top-line">
        <span class="pkg-rank">#9985</span>
        <code class="pkg-name">bitbag/cms-plugin</code>
        <span class="badge-root">MIT</span>
        <span class="badge-signal badge-signal-weak">Weak Copyleft</span>
      </div>
      <div class="pkg-bottom-line">
        <span class="pkg-arrow">&rdsh;</span>
        <code class="pkg-dep">paragonie/halite</code> <span class="badge-weak">MPL-2.0</span> <span class="pkg-dot">&bull;</span> <code class="pkg-dep">paragonie/hidden-string</code> <span class="badge-weak">MPL-2.0</span>
      </div>
    </div>
    </div>
  </div>
</details>

<h2>10 to 15 million websites affected worldwide</h2>
<p>This is not a theoretical edge case. A deep inspection of physical archives across all 320 flagged packages reveals severe real-world exposure:</p>

<ul class="article-checklist">
  <li><strong>191 packages have confirmed copyleft violations:</strong> 60 packages pull strong viral copyleft terms (OSL-3.0, GPL, AGPL) directly into permissive projects with no copyleft licensing. Another 131 packages bundle unresolved copyleft files.</li>
  <li><strong>81 packages are dynamic wrappers missing notices:</strong> These load untouched LGPL libraries. While dynamic linking is valid, authors failed to include required LGPL credit texts and license files.</li>
  <li><strong>Over 5 million WordPress sites run OSL-3.0 code:</strong> WPForms Lite alone runs on more than 5,000,000 active websites while bundling OSL-3.0 files inside its prefixed vendor directory.</li>
  <li><strong>Millions of production apps run copyleft PDF and payment code:</strong> Popular tools like <code>laravel-dompdf</code> (over 50 million downloads) and <code>paypal/paypal-server-sdk</code> place copyleft requirements across enterprise checkout backends and invoice systems.</li>
</ul>

<p>In total, between 10 million and 15 million active websites and web services worldwide run code with hidden copyleft obligations.</p>

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
                'I scanned 10,000 PHP packages and found hundreds of hidden license traps: here is why you could get sued',
                'In an audit of 10,000 Composer packages and top WordPress plugins, 320 libraries pulled copyleft code under permissive root tags. See real court cases, exact dependency risks, and worldwide impact.',
                $articleHtml,
                11,
                new \DateTimeImmutable('2026-08-30 23:50:00+00:00'),
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
