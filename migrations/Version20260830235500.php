<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830235500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update Composer license article with clear hierarchical audit package cards';
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
    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#243</span>
        <strong class="audit-pkg-title">barryvdh/laravel-dompdf</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#454</span>
        <strong class="audit-pkg-title">wp-coding-standards/wpcs</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsextra <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#506</span>
        <strong class="audit-pkg-title">vimeo/psalm</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#587</span>
        <strong class="audit-pkg-title">sirbrillig/phpcs-variable-analysis</strong>
        <span class="audit-badge-declared">Declared: BSD-2-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#657</span>
        <strong class="audit-pkg-title">danog/advanced-json-rpc</strong>
        <span class="audit-badge-declared">Declared: ISC</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#678</span>
        <strong class="audit-pkg-title">felixfbecker/advanced-json-rpc</strong>
        <span class="audit-badge-declared">Declared: ISC</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#747</span>
        <strong class="audit-pkg-title">mews/purifier</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#760</span>
        <strong class="audit-pkg-title">mglaman/phpstan-drupal</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">webflo/drupal-finder <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#877</span>
        <strong class="audit-pkg-title">horstoeko/zugferd</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">smalot/pdfparser <em class="lic-tag">(LGPL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1063</span>
        <strong class="audit-pkg-title">yiisoft/yii2</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1113</span>
        <strong class="audit-pkg-title">roots/wordpress</strong>
        <span class="audit-badge-declared">Declared: MIT, GPL-2.0-or-later</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">roots/wordpress-core-installer <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">roots/wordpress-no-content <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1122</span>
        <strong class="audit-pkg-title">stevebauman/purify</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1130</span>
        <strong class="audit-pkg-title">automattic/vipwpcs</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpcsstandards/phpcsextra <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1214</span>
        <strong class="audit-pkg-title">mkalkbrenner/php-htmldiff-advanced</strong>
        <span class="audit-badge-declared">Declared: GNU General Public License V2</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">caxy/php-htmldiff <em class="lic-tag">(GPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1225</span>
        <strong class="audit-pkg-title">yiisoft/yii2-debug</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1283</span>
        <strong class="audit-pkg-title">apimatic/core</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">apimatic/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1343</span>
        <strong class="audit-pkg-title">iio/libmergepdf</strong>
        <span class="audit-badge-declared">Declared: WTFPL</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">tecnickcom/tcpdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-pdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-barcode <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+11 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1402</span>
        <strong class="audit-pkg-title">vladimir-yuldashev/laravel-queue-rabbitmq</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1457</span>
        <strong class="audit-pkg-title">wikimedia/css-sanitizer</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">wikimedia/utfnormal <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">wikimedia/scoped-callback <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1487</span>
        <strong class="audit-pkg-title">yiisoft/yii2-queue</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1499</span>
        <strong class="audit-pkg-title">psalm/plugin-symfony</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1548</span>
        <strong class="audit-pkg-title">simplesamlphp/saml2</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">simplesamlphp/assert <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">simplesamlphp/xml-common <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">simplesamlphp/composer-xmlprovider-installer <em class="lic-tag">(LGPL-2.1-only)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+2 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1568</span>
        <strong class="audit-pkg-title">woocommerce/woocommerce-sniffs</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsextra <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1579</span>
        <strong class="audit-pkg-title">phan/phan</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1583</span>
        <strong class="audit-pkg-title">yiisoft/yii2-gii</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1590</span>
        <strong class="audit-pkg-title">php-amqplib/rabbitmq-bundle</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1591</span>
        <strong class="audit-pkg-title">hisorange/browser-detect</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">matomo/device-detector <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1606</span>
        <strong class="audit-pkg-title">yiisoft/yii2-redis</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1615</span>
        <strong class="audit-pkg-title">exercise/htmlpurifier-bundle</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1627</span>
        <strong class="audit-pkg-title">yiisoft/yii2-bootstrap</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1706</span>
        <strong class="audit-pkg-title">yiisoft/yii2-faker</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1715</span>
        <strong class="audit-pkg-title">yiisoft/yii2-httpclient</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1780</span>
        <strong class="audit-pkg-title">paypal/paypal-server-sdk</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">apimatic/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1819</span>
        <strong class="audit-pkg-title">shopware/core</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1822</span>
        <strong class="audit-pkg-title">yiisoft/yii2-symfonymailer</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1870</span>
        <strong class="audit-pkg-title">shopware/storefront</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1878</span>
        <strong class="audit-pkg-title">prestashop/translationtools-bundle</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">smarty/smarty <em class="lic-tag">(LGPL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1879</span>
        <strong class="audit-pkg-title">statamic/cms</strong>
        <span class="audit-badge-declared">Declared: proprietary</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">james-heinrich/getid3 <em class="lic-tag">(GPL-1.0-or-later, LGPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1949</span>
        <strong class="audit-pkg-title">palantirnet/drupal-rector</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">webflo/drupal-finder <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1979</span>
        <strong class="audit-pkg-title">lesstif/php-jira-rest-client</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#1996</span>
        <strong class="audit-pkg-title">wamania/php-stemmer</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">joomla/string <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2008</span>
        <strong class="audit-pkg-title">yiisoft/yii2-swiftmailer</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2010</span>
        <strong class="audit-pkg-title">workos/workos-php</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2050</span>
        <strong class="audit-pkg-title">square/square</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">apimatic/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2064</span>
        <strong class="audit-pkg-title">coenjacobs/mozart</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2070</span>
        <strong class="audit-pkg-title">shopware/elasticsearch</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2079</span>
        <strong class="audit-pkg-title">shopware/administration</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2084</span>
        <strong class="audit-pkg-title">shopware/conflicts</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2108</span>
        <strong class="audit-pkg-title">wp-cli/wp-cli-tests</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">wp-hooks/wordpress-core <em class="lic-tag">(GPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsextra <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2119</span>
        <strong class="audit-pkg-title">enqueue/amqp-lib</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2126</span>
        <strong class="audit-pkg-title">lucatume/wp-browser</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">druidfi/mysqldump-php <em class="lic-tag">(GPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2135</span>
        <strong class="audit-pkg-title">yiisoft/yii2-jui</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2160</span>
        <strong class="audit-pkg-title">vardot/ckeditor5-anchor-drupal</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">drupal/core <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">drupal/core-composer-scaffold <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2213</span>
        <strong class="audit-pkg-title">psalm/plugin-laravel</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2247</span>
        <strong class="audit-pkg-title">carlos-meneses/laravel-mpdf</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">mpdf/mpdf <em class="lic-tag">(GPL-2.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2305</span>
        <strong class="audit-pkg-title">setasign/fpdi-tcpdf</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">tecnickcom/tcpdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-pdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-barcode <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+11 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2326</span>
        <strong class="audit-pkg-title">weirdan/doctrine-psalm-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2387</span>
        <strong class="audit-pkg-title">yiisoft/yii2-authclient</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2400</span>
        <strong class="audit-pkg-title">saschaegerer/phpstan-typo3</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3/cms-core <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">typo3/cms-cli <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+4 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2436</span>
        <strong class="audit-pkg-title">ssch/typo3-debug-dump-pass</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3/cms-core <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">typo3/cms-cli <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+2 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2439</span>
        <strong class="audit-pkg-title">kartik-v/yii2-mpdf</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">mpdf/mpdf <em class="lic-tag">(GPL-2.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2452</span>
        <strong class="audit-pkg-title">johnbillion/wp-compat</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">wp-hooks/wordpress-core <em class="lic-tag">(GPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2457</span>
        <strong class="audit-pkg-title">sylius/sylius</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2475</span>
        <strong class="audit-pkg-title">nfephp-org/sped-da</strong>
        <span class="audit-badge-declared">Declared: LGPL-3.0-or-later+, GPL-3.0-or-later, MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">tecnickcom/tc-lib-barcode <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-color <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2492</span>
        <strong class="audit-pkg-title">consoletvs/charts</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">balping/json-raw-encoder <em class="lic-tag">(GPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2523</span>
        <strong class="audit-pkg-title">mike42/escpos-php</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">mike42/gfx-php <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2670</span>
        <strong class="audit-pkg-title">cloudconvert/cloudconvert-php</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2684</span>
        <strong class="audit-pkg-title">yiisoft/yii2-imagine</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2723</span>
        <strong class="audit-pkg-title">xemlock/htmlpurifier-html5</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2762</span>
        <strong class="audit-pkg-title">nystudio107/craft-code-editor</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2819</span>
        <strong class="audit-pkg-title">apache-solr-for-typo3/solr</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3/cms-backend <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">typo3/cms-core <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+10 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2822</span>
        <strong class="audit-pkg-title">bschmitt/laravel-amqp</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2851</span>
        <strong class="audit-pkg-title">lesstif/jira-cloud-restapi</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2965</span>
        <strong class="audit-pkg-title">yiisoft/yii2-bootstrap5</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#2970</span>
        <strong class="audit-pkg-title">yiisoft/yii2-bootstrap4</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3035</span>
        <strong class="audit-pkg-title">verbb/base</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3123</span>
        <strong class="audit-pkg-title">elibyy/tcpdf-laravel</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">tecnickcom/tcpdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-pdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-barcode <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+11 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3182</span>
        <strong class="audit-pkg-title">psalm/plugin-mockery</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3211</span>
        <strong class="audit-pkg-title">rmrevin/yii2-fontawesome</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3218</span>
        <strong class="audit-pkg-title">creocoder/yii2-nested-sets</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3254</span>
        <strong class="audit-pkg-title">stellarwp/superglobals</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">stellarwp/arrays <em class="lic-tag">(GPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3306</span>
        <strong class="audit-pkg-title">alibabacloud/client</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">clagiordano/weblibs-configmanager <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3318</span>
        <strong class="audit-pkg-title">nucleos/dompdf-bundle</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3323</span>
        <strong class="audit-pkg-title">rubix/ml</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">joomla/string <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3394</span>
        <strong class="audit-pkg-title">kartik-v/yii2-export</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">mpdf/mpdf <em class="lic-tag">(GPL-2.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3440</span>
        <strong class="audit-pkg-title">niklasravnsborg/laravel-pdf</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">mpdf/mpdf <em class="lic-tag">(GPL-2.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3492</span>
        <strong class="audit-pkg-title">statamic/eloquent-driver</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">james-heinrich/getid3 <em class="lic-tag">(GPL-1.0-or-later, LGPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3565</span>
        <strong class="audit-pkg-title">propa/tcpdi</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">tecnickcom/tcpdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-pdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-barcode <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+11 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3577</span>
        <strong class="audit-pkg-title">laravel/workos</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3581</span>
        <strong class="audit-pkg-title">samdark/yii2-psr-log-target</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3598</span>
        <strong class="audit-pkg-title">neos/fluid-adaptor</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3fluid/fluid <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3656</span>
        <strong class="audit-pkg-title">mindee/mindee</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">smalot/pdfparser <em class="lic-tag">(LGPL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3663</span>
        <strong class="audit-pkg-title">yiisoft/yii2-shell</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3681</span>
        <strong class="audit-pkg-title">plank/laravel-mediable</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3692</span>
        <strong class="audit-pkg-title">theodo-group/llphant</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpoffice/phpword <em class="lic-tag">(LGPL-3.0-only)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">smalot/pdfparser <em class="lic-tag">(LGPL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3704</span>
        <strong class="audit-pkg-title">sylius/telemetry</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3706</span>
        <strong class="audit-pkg-title">workos/workos-php-laravel</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3777</span>
        <strong class="audit-pkg-title">karriere/pdf-merge</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">tecnickcom/tcpdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-pdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-barcode <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+11 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3790</span>
        <strong class="audit-pkg-title">nystudio107/craft-twig-sandbox</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3800</span>
        <strong class="audit-pkg-title">codex-team/editor.js</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3806</span>
        <strong class="audit-pkg-title">contao-components/colorbox</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3856</span>
        <strong class="audit-pkg-title">craftcms/cms</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3866</span>
        <strong class="audit-pkg-title">stevebauman/hypertext</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3868</span>
        <strong class="audit-pkg-title">sylius/paypal-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3910</span>
        <strong class="audit-pkg-title">frosh/tools</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#3972</span>
        <strong class="audit-pkg-title">sylius/refund-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4106</span>
        <strong class="audit-pkg-title">retailcrm/api-client-php</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">pnz/json-exception <em class="lic-tag">(GPL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4126</span>
        <strong class="audit-pkg-title">spryker/rabbit-mq</strong>
        <span class="audit-badge-declared">Declared: proprietary</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4210</span>
        <strong class="audit-pkg-title">advoor/nova-editor-js</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4236</span>
        <strong class="audit-pkg-title">log1x/acf-composer</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">stoutlogic/acf-builder <em class="lic-tag">(GPL-2.0+)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4316</span>
        <strong class="audit-pkg-title">php-collective/code-sniffer</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpcsstandards/phpcsextra <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4328</span>
        <strong class="audit-pkg-title">doctrineencryptbundle/doctrine-encrypt-bundle</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4371</span>
        <strong class="audit-pkg-title">statamic/seo-pro</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">james-heinrich/getid3 <em class="lic-tag">(GPL-1.0-or-later, LGPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4408</span>
        <strong class="audit-pkg-title">atgp/factur-x</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">smalot/pdfparser <em class="lic-tag">(LGPL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4469</span>
        <strong class="audit-pkg-title">contao-components/simplemodal</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4475</span>
        <strong class="audit-pkg-title">oneduo/nova-file-manager</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">james-heinrich/getid3 <em class="lic-tag">(GPL-1.0-or-later, LGPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4480</span>
        <strong class="audit-pkg-title">contao-components/ace</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4533</span>
        <strong class="audit-pkg-title">contao-components/swiper</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4538</span>
        <strong class="audit-pkg-title">sylius/mollie-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4540</span>
        <strong class="audit-pkg-title">contao-components/datepicker</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4546</span>
        <strong class="audit-pkg-title">himiklab/yii2-recaptcha-widget</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4569</span>
        <strong class="audit-pkg-title">rokka/imagine-vips</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phenx/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4600</span>
        <strong class="audit-pkg-title">php-standard-library/psalm-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4649</span>
        <strong class="audit-pkg-title">verbb/auth</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4652</span>
        <strong class="audit-pkg-title">contao-components/jquery</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4656</span>
        <strong class="audit-pkg-title">fig-r/psr2r-sniffer</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpcsstandards/phpcsextra <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4663</span>
        <strong class="audit-pkg-title">contao-components/dropzone</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4664</span>
        <strong class="audit-pkg-title">contao-components/tablesorter</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4668</span>
        <strong class="audit-pkg-title">contao-components/mootools</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4669</span>
        <strong class="audit-pkg-title">contao-components/swipe</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4675</span>
        <strong class="audit-pkg-title">contao-components/mediabox</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4678</span>
        <strong class="audit-pkg-title">contao-components/jquery-ui</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4684</span>
        <strong class="audit-pkg-title">10up/phpcs-composer</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpcsstandards/phpcsextra <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4773</span>
        <strong class="audit-pkg-title">seatsio/seatsio-php</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4852</span>
        <strong class="audit-pkg-title">justbetter/magento2-sentry</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">justbetter/magento2-core <em class="lic-tag">(GPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4861</span>
        <strong class="audit-pkg-title">reload/jira-security-issue</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4863</span>
        <strong class="audit-pkg-title">infocyph/phpforge</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4868</span>
        <strong class="audit-pkg-title">contao-components/tristen-tablesort</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4891</span>
        <strong class="audit-pkg-title">alibabacloud/sdk</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">clagiordano/weblibs-configmanager <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4917</span>
        <strong class="audit-pkg-title">awcodes/filament-curator</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4918</span>
        <strong class="audit-pkg-title">magefan/module-blog</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">magefan/module-wysiwyg-advanced <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4937</span>
        <strong class="audit-pkg-title">notamedia/yii2-sentry</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#4957</span>
        <strong class="audit-pkg-title">jwage/phpamqplib-messenger</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5005</span>
        <strong class="audit-pkg-title">cebe/yii2-gravatar</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5010</span>
        <strong class="audit-pkg-title">dmstr/yii2-adminlte-asset</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5077</span>
        <strong class="audit-pkg-title">bk2k/bootstrap-package</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3/cms-backend <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">typo3/cms-core <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+10 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5081</span>
        <strong class="audit-pkg-title">contao-components/handorgel</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5085</span>
        <strong class="audit-pkg-title">sylius/invoicing-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5086</span>
        <strong class="audit-pkg-title">unclead/yii2-multiple-input</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5110</span>
        <strong class="audit-pkg-title">contao-components/choices</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5138</span>
        <strong class="audit-pkg-title">hyperf/amqp</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5200</span>
        <strong class="audit-pkg-title">amazeeio/drupal_integrations</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">drupal/core-composer-scaffold <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5207</span>
        <strong class="audit-pkg-title">yiisoft/yii2-elasticsearch</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5251</span>
        <strong class="audit-pkg-title">sulu/sulu</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao/imagine-svg <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">matomo/device-detector <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5252</span>
        <strong class="audit-pkg-title">yii2-extensions/phpstan</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5352</span>
        <strong class="audit-pkg-title">spryker/opentelemetry</strong>
        <span class="audit-badge-declared">Declared: proprietary</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5361</span>
        <strong class="audit-pkg-title">miloschuman/yii2-highcharts-widget</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5402</span>
        <strong class="audit-pkg-title">loupe/loupe</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">joomla/string <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5428</span>
        <strong class="audit-pkg-title">roave/infection-static-analysis-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5490</span>
        <strong class="audit-pkg-title">2amigos/yii2-ckeditor-widget</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ckeditor/ckeditor <em class="lic-tag">(GPL-2.0+)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5537</span>
        <strong class="audit-pkg-title">cloudconvert/cloudconvert-laravel</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5549</span>
        <strong class="audit-pkg-title">mdmsoft/yii2-admin</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5556</span>
        <strong class="audit-pkg-title">getkirby/cms</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpmailer/phpmailer <em class="lic-tag">(LGPL-2.1-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5575</span>
        <strong class="audit-pkg-title">creocoder/yii2-flysystem</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5576</span>
        <strong class="audit-pkg-title">pear/pear</strong>
        <span class="audit-badge-declared">Declared: BSD-2-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">pear/structures_graph <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5637</span>
        <strong class="audit-pkg-title">yiisoft/yii2-mongodb</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5642</span>
        <strong class="audit-pkg-title">wyrihaximus/css-compress</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">websharks/css-minifier <em class="lic-tag">(GPL-3.0+)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5655</span>
        <strong class="audit-pkg-title">yiisoft/yii2-twig</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5721</span>
        <strong class="audit-pkg-title">codemix/yii2-localeurls</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5773</span>
        <strong class="audit-pkg-title">tornevall/tornelib-php-database</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5830</span>
        <strong class="audit-pkg-title">craftcms/html-field</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5862</span>
        <strong class="audit-pkg-title">qipsius/tcpdf-bundle</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">tecnickcom/tcpdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-pdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-barcode <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+11 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5995</span>
        <strong class="audit-pkg-title">spryker-feature/spryker-core</strong>
        <span class="audit-badge-declared">Declared: proprietary</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#5997</span>
        <strong class="audit-pkg-title">codewithkyrian/transformers</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phenx/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6129</span>
        <strong class="audit-pkg-title">neos/kickstarter</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3fluid/fluid <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6190</span>
        <strong class="audit-pkg-title">drupal/config_sync_without_site_uuid</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">drupal/core <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">drupal/core-composer-scaffold <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6214</span>
        <strong class="audit-pkg-title">yii2tech/csv-grid</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6220</span>
        <strong class="audit-pkg-title">contao-components/altcha</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6246</span>
        <strong class="audit-pkg-title">yii2tech/ar-softdelete</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6255</span>
        <strong class="audit-pkg-title">netlogix/webapi</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3fluid/fluid <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6261</span>
        <strong class="audit-pkg-title">oxid-esales/oxideshop-ce</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpmailer/phpmailer <em class="lic-tag">(LGPL-2.1-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6270</span>
        <strong class="audit-pkg-title">t3n/jobqueue-rabbitmq</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6289</span>
        <strong class="audit-pkg-title">netlogix/jobqueue-fast-rabbit</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6343</span>
        <strong class="audit-pkg-title">magefan/module-blog-comments-recaptcha</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">magefan/module-wysiwyg-advanced <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6431</span>
        <strong class="audit-pkg-title">anilcancakir/laravel-agent-mcp</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpmyadmin/sql-parser <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6524</span>
        <strong class="audit-pkg-title">2amigos/yii2-gallery-widget</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6574</span>
        <strong class="audit-pkg-title">omnilight/yii2-scheduling</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6600</span>
        <strong class="audit-pkg-title">nystudio107/craft-plugin-vite</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6752</span>
        <strong class="audit-pkg-title">october/rain</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6758</span>
        <strong class="audit-pkg-title">neos/media</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3fluid/fluid <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6759</span>
        <strong class="audit-pkg-title">lunarphp/lunar</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6770</span>
        <strong class="audit-pkg-title">2amigos/yii2-chartjs-widget</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6859</span>
        <strong class="audit-pkg-title">flux-se/sylius-stripe-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6865</span>
        <strong class="audit-pkg-title">cmsig/seal-loupe-adapter</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">joomla/string <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6875</span>
        <strong class="audit-pkg-title">rackspace/php-opencloud</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">mikemccabe/json-patch-php <em class="lic-tag">(LGPL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6885</span>
        <strong class="audit-pkg-title">asana/asana</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">adoy/oauth2 <em class="lic-tag">(LGPL-2.1)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6907</span>
        <strong class="audit-pkg-title">php-soap/psr18-wsse-middleware</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#6924</span>
        <strong class="audit-pkg-title">dq5studios/psalm-junit</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7028</span>
        <strong class="audit-pkg-title">wyrihaximus/html-compress</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">websharks/css-minifier <em class="lic-tag">(GPL-3.0+)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7064</span>
        <strong class="audit-pkg-title">mollie/laravel-cashier-mollie</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7077</span>
        <strong class="audit-pkg-title">ibexa/headless</strong>
        <span class="audit-badge-declared">Declared: proprietary</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ibexa/oss <em class="lic-tag">((GPL-2.0-only or proprietary))</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ibexa/doctrine-schema <em class="lic-tag">((GPL-2.0-only or proprietary))</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ibexa/system-info <em class="lic-tag">((GPL-2.0-only or proprietary))</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+21 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7080</span>
        <strong class="audit-pkg-title">simple-bus/rabbitmq-bundle-bridge</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7087</span>
        <strong class="audit-pkg-title">light/yii2-swagger</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7101</span>
        <strong class="audit-pkg-title">bandwidth/sdk</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">apimatic/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7104</span>
        <strong class="audit-pkg-title">lullabot/twig-cs-fixer-drupal</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">drupal/core <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">drupal/core-composer-scaffold <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">webflo/drupal-finder <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7118</span>
        <strong class="audit-pkg-title">bizley/jwt</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7164</span>
        <strong class="audit-pkg-title">yoast/yoastcs</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpcsstandards/phpcsextra <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7223</span>
        <strong class="audit-pkg-title">codeception/module-amqp</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7224</span>
        <strong class="audit-pkg-title">ibexa/messenger</strong>
        <span class="audit-badge-declared">Declared: proprietary</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ibexa/core <em class="lic-tag">((GPL-2.0-only or proprietary))</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ibexa/doctrine-schema <em class="lic-tag">((GPL-2.0-only or proprietary))</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ibexa/core-persistence <em class="lic-tag">((GPL-2.0-only or proprietary))</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7248</span>
        <strong class="audit-pkg-title">kartik-v/yii2-icons</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7259</span>
        <strong class="audit-pkg-title">stefandoorn/sitemap-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7265</span>
        <strong class="audit-pkg-title">2amigos/yii2-file-upload-widget</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7281</span>
        <strong class="audit-pkg-title">2amigos/yii2-tinymce-widget</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7289</span>
        <strong class="audit-pkg-title">dektrium/yii2-user</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7316</span>
        <strong class="audit-pkg-title">yii2mod/yii2-enum</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7337</span>
        <strong class="audit-pkg-title">netresearch/typo3-ci-workflows</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3/cms-core <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">typo3/cms-cli <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+8 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7350</span>
        <strong class="audit-pkg-title">borales/yii2-phone-input</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7375</span>
        <strong class="audit-pkg-title">vova07/yii2-imperavi-widget</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7388</span>
        <strong class="audit-pkg-title">neos/fusion</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3fluid/fluid <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7410</span>
        <strong class="audit-pkg-title">chromatic/usher</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">drush/drush <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">chi-teck/drupal-code-generator <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7412</span>
        <strong class="audit-pkg-title">bitbag/elasticsearch-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7445</span>
        <strong class="audit-pkg-title">neos/fusion-afx</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3fluid/fluid <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7450</span>
        <strong class="audit-pkg-title">akaunting/laravel-apexcharts</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">balping/json-raw-encoder <em class="lic-tag">(GPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7452</span>
        <strong class="audit-pkg-title">liip/serializer</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">pnz/json-exception <em class="lic-tag">(GPL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7511</span>
        <strong class="audit-pkg-title">linslin/yii2-curl</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7537</span>
        <strong class="audit-pkg-title">acquia/blt</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">acquia/drupal-environment-detector <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">drupal/core <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">drupal/core-composer-scaffold <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+2 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7637</span>
        <strong class="audit-pkg-title">ehaerer/paste-reference</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3/cms-backend <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">typo3/cms-core <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7691</span>
        <strong class="audit-pkg-title">neos/neos</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3fluid/fluid <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">neos/fusion-form <em class="lic-tag">(GPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7725</span>
        <strong class="audit-pkg-title">neos/media-browser</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3fluid/fluid <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">neos/fusion-form <em class="lic-tag">(GPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7732</span>
        <strong class="audit-pkg-title">owen-oj/laravel-getid3</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">james-heinrich/getid3 <em class="lic-tag">(GPL-1.0-or-later, LGPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7789</span>
        <strong class="audit-pkg-title">frosh/sentry-bundle</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7807</span>
        <strong class="audit-pkg-title">frosh/mail-platform-archive</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7821</span>
        <strong class="audit-pkg-title">dektrium/yii2-rbac</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7824</span>
        <strong class="audit-pkg-title">friendsofsylius/sylius-import-export-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7834</span>
        <strong class="audit-pkg-title">sizeg/yii2-jwt</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7936</span>
        <strong class="audit-pkg-title">moonlandsoft/yii2-phpexcel</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7970</span>
        <strong class="audit-pkg-title">mihaildev/yii2-elfinder</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#7984</span>
        <strong class="audit-pkg-title">wikimedia/remex-html</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">wikimedia/utfnormal <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8081</span>
        <strong class="audit-pkg-title">previousnext/coding-standard</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">drupal/coder <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8129</span>
        <strong class="audit-pkg-title">frosh/development-helper</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8137</span>
        <strong class="audit-pkg-title">mihaildev/yii2-ckeditor</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8152</span>
        <strong class="audit-pkg-title">marcorieser/statamic-livewire</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">james-heinrich/getid3 <em class="lic-tag">(GPL-1.0-or-later, LGPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8160</span>
        <strong class="audit-pkg-title">sylius/cms-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8202</span>
        <strong class="audit-pkg-title">wbraganca/yii2-dynamicform</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8249</span>
        <strong class="audit-pkg-title">statamic-rad-pack/runway</strong>
        <span class="audit-badge-declared">Declared: mit</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">james-heinrich/getid3 <em class="lic-tag">(GPL-1.0-or-later, LGPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8254</span>
        <strong class="audit-pkg-title">calebdw/larastan</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpmyadmin/sql-parser <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8345</span>
        <strong class="audit-pkg-title">yiithings/yii2-dotenv</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8357</span>
        <strong class="audit-pkg-title">rias/statamic-redirect</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">james-heinrich/getid3 <em class="lic-tag">(GPL-1.0-or-later, LGPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8380</span>
        <strong class="audit-pkg-title">liip/serializer-jms-adapter</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">pnz/json-exception <em class="lic-tag">(GPL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8388</span>
        <strong class="audit-pkg-title">rawilk/laravel-printing</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">mike42/gfx-php <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8399</span>
        <strong class="audit-pkg-title">alexandernst/yii2-device-detect</strong>
        <span class="audit-badge-declared">Declared: GNU General Public License v3</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8404</span>
        <strong class="audit-pkg-title">firstred/postnl-api-php</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8410</span>
        <strong class="audit-pkg-title">contao-components/chosen</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8427</span>
        <strong class="audit-pkg-title">phalcon/quill</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">pds/composer-script-names <em class="lic-tag">(CC-BY-SA-4.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">pds/skeleton <em class="lic-tag">(CC-BY-SA-4.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8439</span>
        <strong class="audit-pkg-title">wptrt/wpthemereview</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsextra <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8467</span>
        <strong class="audit-pkg-title">kak/clickhouse</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8468</span>
        <strong class="audit-pkg-title">contributte/pdf</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">mpdf/mpdf <em class="lic-tag">(GPL-2.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8515</span>
        <strong class="audit-pkg-title">wikimedia/shellbox</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">wikimedia/base-convert <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8544</span>
        <strong class="audit-pkg-title">rebuy/amqp-php-consumer</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8547</span>
        <strong class="audit-pkg-title">horstoeko/zugferdvisualizer</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+2 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8577</span>
        <strong class="audit-pkg-title">contao-components/colorpicker</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">contao-components/installer <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8587</span>
        <strong class="audit-pkg-title">creagia/laravel-sign-pad</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">tecnickcom/tcpdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-pdf <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-barcode <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+11 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8605</span>
        <strong class="audit-pkg-title">ctidigital/magento2-configurator</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">firegento/fastsimpleimport <em class="lic-tag">(GPL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8609</span>
        <strong class="audit-pkg-title">humanmade/psalm-plugin-wordpress</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">wp-hooks/wordpress-core <em class="lic-tag">(GPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8622</span>
        <strong class="audit-pkg-title">sylius/adyen-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8629</span>
        <strong class="audit-pkg-title">friendsoftypo3/headless</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">typo3/cms-core <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">typo3/cms-cli <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+5 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8634</span>
        <strong class="audit-pkg-title">sylius/wishlist-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+2 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8681</span>
        <strong class="audit-pkg-title">pagarme/pagarme-php-sdk</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">apimatic/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8695</span>
        <strong class="audit-pkg-title">monsieurbiz/sylius-rich-editor-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8718</span>
        <strong class="audit-pkg-title">2amigos/yii2-selectize-widget</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8724</span>
        <strong class="audit-pkg-title">yiisoft/yii2-sphinx</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8729</span>
        <strong class="audit-pkg-title">phpdocumentor/phpdocumentor</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">jawira/plantuml <em class="lic-tag">(GPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8740</span>
        <strong class="audit-pkg-title">craftcms/flysystem</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8806</span>
        <strong class="audit-pkg-title">bashkarev/clickhouse</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8811</span>
        <strong class="audit-pkg-title">horstoeko/zugferd-laravel</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">smalot/pdfparser <em class="lic-tag">(LGPL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8827</span>
        <strong class="audit-pkg-title">jbzoo/codestyle</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8828</span>
        <strong class="audit-pkg-title">rmrevin/yii2-minify-view</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8911</span>
        <strong class="audit-pkg-title">codedredd/laravel-soap</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8936</span>
        <strong class="audit-pkg-title">craftcms/generator</strong>
        <span class="audit-badge-declared">Declared: mit</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8982</span>
        <strong class="audit-pkg-title">orklah/psalm-insane-comparison</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#8989</span>
        <strong class="audit-pkg-title">studio1902/statamic-peak-tools</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">james-heinrich/getid3 <em class="lic-tag">(GPL-1.0-or-later, LGPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9041</span>
        <strong class="audit-pkg-title">yii2tech/spreadsheet</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9061</span>
        <strong class="audit-pkg-title">jbzoo/toolbox-dev</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9116</span>
        <strong class="audit-pkg-title">asofter/yii2-imperavi-redactor</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9121</span>
        <strong class="audit-pkg-title">alperenersoy/filament-export</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9136</span>
        <strong class="audit-pkg-title">yiisoft/yii2-codeception</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9164</span>
        <strong class="audit-pkg-title">oxid-esales/oxideshop-metapackage-ce</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">makaira/oxid-connect-essential <em class="lic-tag">(GPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpmailer/phpmailer <em class="lic-tag">(LGPL-2.1-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9234</span>
        <strong class="audit-pkg-title">shopware/docker</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9263</span>
        <strong class="audit-pkg-title">xj/yii2-jplayer-widget</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9270</span>
        <strong class="audit-pkg-title">swag/paypal</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9294</span>
        <strong class="audit-pkg-title">craftcms/redactor</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9324</span>
        <strong class="audit-pkg-title">nystudio107/craft-seomatic</strong>
        <span class="audit-badge-declared">Declared: proprietary</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9437</span>
        <strong class="audit-pkg-title">liuggio/excelbundle</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpoffice/phpexcel <em class="lic-tag">(LGPL-2.1)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9476</span>
        <strong class="audit-pkg-title">keepa/php_api</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9500</span>
        <strong class="audit-pkg-title">kdyby/rabbitmq</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">php-amqplib/php-amqplib <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9508</span>
        <strong class="audit-pkg-title">log1x/poet</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">johnbillion/extended-cpts <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">johnbillion/args <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9532</span>
        <strong class="audit-pkg-title">raoul2000/yii2-jcrop-widget</strong>
        <span class="audit-badge-declared">Declared: BSD 3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9538</span>
        <strong class="audit-pkg-title">dhl/sdk-api-parcel-de</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9549</span>
        <strong class="audit-pkg-title">oro/oauth2-server</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">matomo/device-detector <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">oro/platform-serialised-fields <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9559</span>
        <strong class="audit-pkg-title">lctrs/psalm-psr-container-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">netresearch/jsonmapper <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9575</span>
        <strong class="audit-pkg-title">thamtech/yii2-ratelimiter-advanced</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9577</span>
        <strong class="audit-pkg-title">oro/calendar-bundle</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">matomo/device-detector <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">oro/platform-serialised-fields <em class="lic-tag">(OSL-3.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9616</span>
        <strong class="audit-pkg-title">winter/storm</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9619</span>
        <strong class="audit-pkg-title">faryshta/yii2-disable-submit-buttons</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9667</span>
        <strong class="audit-pkg-title">heptacom/shopware-platform-admin-open-auth</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-font-lib <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">dompdf/php-svg-lib <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9684</span>
        <strong class="audit-pkg-title">studio1902/statamic-peak-seo</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">james-heinrich/getid3 <em class="lic-tag">(GPL-1.0-or-later, LGPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9692</span>
        <strong class="audit-pkg-title">kop/yii2-scroll-pager</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9714</span>
        <strong class="audit-pkg-title">2amigos/yii2-date-picker-widget</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9729</span>
        <strong class="audit-pkg-title">shopware/production</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">dompdf/dompdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">setasign/tfpdf <em class="lic-tag">(LGPL-2.1)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-more">+3 more</span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9744</span>
        <strong class="audit-pkg-title">sylius/admin-api-bundle</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9765</span>
        <strong class="audit-pkg-title">yii2tech/html2pdf</strong>
        <span class="audit-badge-declared">Declared: BSD-3-Clause</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9775</span>
        <strong class="audit-pkg-title">tig/postnl-magento2</strong>
        <span class="audit-badge-declared">Declared: CC-BY-NC-ND-3.0</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">tecnickcom/tc-lib-barcode <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">tecnickcom/tc-lib-color <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9778</span>
        <strong class="audit-pkg-title">craftcms/feed-me</strong>
        <span class="audit-badge-declared">Declared: proprietary</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9816</span>
        <strong class="audit-pkg-title">craftcms/aws-s3</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">enshrined/svg-sanitize <em class="lic-tag">(GPL-2.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9833</span>
        <strong class="audit-pkg-title">stefandoorn/google-tag-manager-plugin</strong>
        <span class="audit-badge-declared">Declared: Unspecified</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">xynnn/google-tag-manager-bundle <em class="lic-tag">(LGPL-3.0-only)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9839</span>
        <strong class="audit-pkg-title">yooper/php-text-analysis</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-strong">Strong Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">joomla/string <em class="lic-tag">(GPL-2.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9861</span>
        <strong class="audit-pkg-title">thamtech/yii2-uuid</strong>
        <span class="audit-badge-declared">Declared: Apache-2.0</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">ezyang/htmlpurifier <em class="lic-tag">(LGPL-2.1-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9862</span>
        <strong class="audit-pkg-title">sylius/test-application</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9896</span>
        <strong class="audit-pkg-title">pantheon-systems/pantheon-wp-coding-standards</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">phpcsstandards/phpcsextra <em class="lic-tag">(LGPL-3.0-or-later)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">phpcsstandards/phpcsutils <em class="lic-tag">(LGPL-3.0-or-later)</em></span>
      </div>
    </div>    <div class="audit-item">
      <div class="audit-item-head">
        <span class="audit-rank">#9985</span>
        <strong class="audit-pkg-title">bitbag/cms-plugin</strong>
        <span class="audit-badge-declared">Declared: MIT</span>
        <span class="audit-badge-risk risk-weak">Weak Copyleft</span>
      </div>
      <div class="audit-item-chain">
        <span class="chain-label">Hidden copyleft:</span>
        <span class="chain-item">paragonie/halite <em class="lic-tag">(MPL-2.0)</em></span> <span class="chain-sep">&rarr;</span> <span class="chain-item">paragonie/hidden-string <em class="lic-tag">(MPL-2.0)</em></span>
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
                new \DateTimeImmutable('2026-08-30 23:55:00+00:00'),
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
