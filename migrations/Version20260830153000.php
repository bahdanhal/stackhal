<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830153000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update Azure SDK blog post to plain ASCII and high readability';
    }

    public function up(Schema $schema): void
    {
        $contentHtml = <<<'HTML'
<p class="article-lead">PHP on Azure works well. The risk is an app that still uses Microsoft's retired PHP SDK: code that can return successful responses while quietly lowering transport security.</p>
<div class="article-callout article-callout-accent"><strong>The short version</strong><span>The archived SDK turns off TLS certificate checks whenever <code>HTTPS_PROXY</code> is set. Its later Storage client also trusts uppercase <code>HTTP_PROXY</code> in web requests, which bypasses Guzzle's fix for httpoxy attacks. Both issues live in app code, not in a server warning.</span></div>
<h2>Check the package, not the hosting platform</h2>
<p>Running PHP in Azure App Service or containers is a valid setup. This note covers Composer files that include <code>microsoft/windowsazure</code>, <code>microsoft/azure-storage</code>, or retired <code>microsoft/azure-storage-*</code> clients.</p>
<pre class="article-code"><code>composer why microsoft/windowsazure
composer why microsoft/azure-storage-blob
composer show --locked | grep -E 'microsoft/(windowsazure|azure-storage)'</code></pre>
<p>Microsoft retired the original Azure SDK for PHP in 2021 and archived it in 2023. They retired the separate Storage clients in 2024. These packages stay on Packagist so old builds do not break, but they get no security updates.</p>
<h2>Risk 1: a proxy disables TLS checks</h2>
<p>The old SDK's HTTP client contains this code before sending requests:</p>
<pre class="article-code article-code-danger"><code>if (getenv('HTTPS_PROXY')) {
    $options[RequestOptions::VERIFY] = false;
}</code></pre>
<p>This is the real runtime path in the archived <code>HttpClient</code>. When <code>HTTPS_PROXY</code> is set, the client stops checking if the TLS certificate belongs to Azure. A proxy or network actor can show any certificate, read the request, and pass it along.</p>
<p>The dangerous part is quiet:</p>
<ul class="article-checklist"><li>The app starts normally.</li><li>The proxy returns real Azure data.</li><li>Health checks stay green.</li><li>No certificate error shows up.</li><li>Keys, tokens, and data cross the proxy in plain text.</li></ul>
<h2>Risk 2: Storage client reintroduces httpoxy</h2>
<p>The later Storage SDK leaves TLS checks on, but reads proxy settings unsafely:</p>
<pre class="article-code article-code-danger"><code>$proxy = getenv('HTTP_PROXY');
if (!empty($proxy)) {
    $options['proxy'] = $proxy;
}</code></pre>
<p>In web servers using CGI or FastCGI, an attacker can send a <code>Proxy: http://bad-host</code> header. PHP turns that into the <code>HTTP_PROXY</code> variable. The SDK reads it and sends storage calls to the attacker's server.</p>
<p>Guzzle only reads <code>HTTP_PROXY</code> on the command line for this reason. The Azure SDK bypassed Guzzle's safety rule by passing the variable directly.</p>
<h2>Why this matters more than version numbers</h2>
<p>These proxy paths are active code flaws. They run silently, keep returning green checks, and have no upstream fixes because the repositories are archived. Updating Guzzle alone does not fix them, because the bad logic lives in the Azure wrapper code.</p>
<h2>A maintained migration path</h2>
<p>The independent <code>azure-oss/storage-blob</code> project provides current packages for Blob Storage, Queue Storage, and Entra ID on PHP 8.2+ with modern Guzzle:</p>
<pre class="article-code"><code>{
  "php": "^8.2",
  "guzzlehttp/guzzle": "^7.8 || ^8.0",
  "azure-oss/storage-common": "^2.0"
}</code></pre>
<h2>How to migrate safely</h2>
<ol class="article-checklist"><li>Search code for <code>WindowsAzure\</code> and <code>MicrosoftAzure\Storage\</code>.</li><li>Check server environment for <code>HTTP_PROXY</code>, <code>HTTPS_PROXY</code>, and <code>verify=false</code>.</li><li>Add an interface around storage calls so your app does not depend on SDK classes directly.</li><li>Switch one service at a time to <code>azure-oss/storage-blob</code>.</li><li>Test uploads, downloads, SAS tokens, and error handling.</li><li>Confirm retired Microsoft packages are gone from <code>composer.lock</code>.</li></ol>
<p class="article-sources"><strong>Primary sources:</strong> <a href="https://github.com/Azure/azure-sdk-for-php">Archived Azure SDK for PHP</a> | <a href="https://httpoxy.org/">httpoxy Vulnerability Class</a> | <a href="https://github.com/php-oss-for-azure/azure-php">PHP OSS for Azure</a></p>
HTML;

        $this->addSql(
            'UPDATE blog_articles SET title = ?, description = ?, content_html = ?, read_time_minutes = ?, updated_at = ? WHERE slug = ?',
            [
                'The Retired Azure SDK for PHP Can Fail Quietly',
                'Two transport-security paths in Microsoft\'s archived PHP SDK can keep Azure requests working while weakening TLS verification or proxy routing.',
                $contentHtml,
                6,
                new \DateTimeImmutable('2026-08-30 15:30:00+00:00'),
                'retired-azure-sdk-for-php-migration',
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
