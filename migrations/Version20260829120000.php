<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260829120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Publish the retired Azure SDK for PHP security and migration field note';
    }

    public function up(Schema $schema): void
    {
        $contentHtml = <<<'HTML'
<p class="article-lead">PHP on Azure is not the problem. The risk is a PHP application that still talks to Azure through Microsoft’s retired SDK: code that can keep returning successful responses while quietly weakening the transport security around them.</p>
<div class="article-callout article-callout-accent"><strong>The short version</strong><span>The archived SDK disables TLS certificate verification whenever <code>HTTPS_PROXY</code> exists. Its later Storage client independently trusts uppercase <code>HTTP_PROXY</code> in web requests, bypassing the protection Guzzle added for the httpoxy class of attacks. Both behaviours live in application code, not in a deployment warning.</span></div>
<h2>Check the dependency, not the hosting platform</h2>
<p>Running a PHP application in Azure App Service, a container, or a virtual machine remains a valid architecture. This article concerns Composer graphs that contain <code>microsoft/windowsazure</code>, <code>microsoft/azure-storage</code>, or one of the retired <code>microsoft/azure-storage-*</code> clients.</p>
<pre class="article-code"><code>composer why microsoft/windowsazure
composer why microsoft/azure-storage-blob
composer show --locked | grep -E 'microsoft/(windowsazure|azure-storage)'</code></pre>
<p>The original <a href="https://github.com/Azure/azure-sdk-for-php">Azure SDK for PHP</a> entered retirement in February 2021 and was archived on November 27, 2023. Microsoft retired the separate <a href="https://github.com/Azure/azure-storage-php">Azure Storage PHP clients</a> on March 17, 2024 and archived that repository in May 2024.</p>
<p>The packages remain downloadable because removing them would break existing builds. Availability on Packagist is not a maintenance or security commitment.</p>
<h2>Quiet danger #1: a proxy disables TLS verification</h2>
<p>The old SDK’s HTTP client contains this branch immediately before sending a request:</p>
<pre class="article-code article-code-danger"><code>// Since PHP 5.6, a default value for certificate validation is 'true'.
// We set it back to false if an enviroment variable 'HTTPS_PROXY' is
// defined.
if (getenv('HTTPS_PROXY')) {
    $options[RequestOptions::VERIFY] = false;
}</code></pre>
<p>This is not an example or optional workaround. It is the runtime path in <a href="https://github.com/Azure/azure-sdk-for-php/blob/master/src/Common/Internal/Http/HttpClient.php">the archived <code>HttpClient</code></a>.</p>
<p><code>HTTPS_PROXY</code> is normal infrastructure configuration. Enterprises use it for controlled egress, network inspection, and private build or runtime environments. The presence of a proxy should change where the TCP connection goes; it should not disable verification of the Azure endpoint’s certificate.</p>
<p>With <code>verify=false</code>, the client stops checking whether the TLS certificate belongs to the Azure service it intended to reach. A proxy or another actor controlling the network path can present an arbitrary certificate, terminate TLS, inspect or modify the request, and open a separate connection to Azure.</p>
<p>The dangerous part is operationally quiet:</p>
<ul class="article-checklist"><li>The application starts normally.</li><li>The proxy returns genuine Azure responses after forwarding requests.</li><li>Health checks and functional tests remain green.</li><li>No certificate error appears because certificate verification was deliberately disabled.</li><li>Authorization headers, service-management requests, Service Bus messages, Media Services data, and response bodies can cross the interception point in plaintext.</li></ul>
<p>A correctly configured TLS-inspection environment should trust a narrowly managed corporate CA. It should never make the client accept every certificate.</p>
<h2>Why the impact is broader than “someone could read traffic”</h2>
<p>Azure API traffic is privileged. Depending on which part of the old SDK an application uses, intercepted requests can contain bearer tokens, Shared Access Signature material, account-level operations, message payloads, resource identifiers, or application data.</p>
<p>An active interceptor can also modify responses. That changes the risk from passive confidentiality loss to integrity loss: an application may make decisions based on an Azure response that never came from Azure.</p>
<p>The attack does require control of the configured proxy or another useful point on the network path. It is not a public unauthenticated exploit against every application. But a proxy is explicitly a security boundary, and the SDK silently removes the cryptographic verification that is meant to protect that boundary.</p>
<h2>Quiet danger #2: the Storage client reintroduces httpoxy routing</h2>
<p>The later Storage SDK uses a different HTTP layer and leaves TLS verification enabled by default. It nevertheless contains another unsafe proxy decision:</p>
<pre class="article-code article-code-danger"><code>$proxy = getenv('HTTP_PROXY');

if (!empty($proxy)) {
    $options['proxy'] = $proxy;
}</code></pre>
<p>This code appears in <a href="https://github.com/Azure/azure-storage-php/blob/master/azure-storage-common/src/Common/Internal/ServiceRestProxy.php">the retired <code>ServiceRestProxy</code></a> used by the Blob, Queue, Table, and File clients.</p>
<p>The uppercase variable is important. Under CGI-style environments, incoming HTTP headers are mapped into environment variables. An attacker-supplied request header named <code>Proxy</code> can become <code>HTTP_PROXY</code>. This collision is the vulnerability class known as <a href="https://httpoxy.org/">httpoxy</a>.</p>
<p>Guzzle’s own documentation explicitly says it only consumes uppercase <code>HTTP_PROXY</code> in the CLI SAPI because the value may be attacker-controlled in CGI environments. The Azure Storage SDK bypasses that safeguard by reading the variable itself and passing the result back to Guzzle as an explicit proxy option.</p>
<p>In an affected CGI or FastCGI deployment, the flow becomes:</p>
<pre class="article-code"><code>attacker request
  Proxy: http://attacker-controlled.example:8080
        ↓
web server / SAPI
  HTTP_PROXY=http://attacker-controlled.example:8080
        ↓
Azure Storage SDK
  $options['proxy'] = getenv('HTTP_PROXY')
        ↓
outbound Blob / Queue / File request uses attacker proxy</code></pre>
<p>For a properly verified HTTPS Azure endpoint, a hostile proxy can observe and disrupt the tunnel but cannot silently decrypt it without a trusted certificate. The risk becomes full interception when certificate verification is disabled, a hostile CA is trusted, or the application uses an HTTP custom/emulator endpoint. Even without decryption, allowing an inbound request header to select the egress route violates a fundamental trust boundary.</p>
<div class="article-callout"><strong>This one still installs cleanly</strong><span>In a clean reproduction on August 29, 2026, Composer 2.10.3 installed <code>microsoft/azure-storage-blob:1.5.4</code> with current Guzzle 7.15.5. Composer printed the abandonment warning and reported “No security vulnerability advisories found.” Runtime inspection then confirmed that setting uppercase <code>HTTP_PROXY</code> became the Guzzle client’s proxy configuration. A dependency-only scanner does not see this SDK-level behaviour.</span></div>
<h2>What makes these better migration evidence than an old version number</h2>
<p>An abandoned dependency is a maintenance concern. A hardcoded 2017 Storage API is a compatibility concern. These proxy paths are concrete security semantics:</p>
<ul class="article-checklist"><li>They execute at runtime.</li><li>They do not require an exception or crash.</li><li>Normal Azure operations can continue to succeed.</li><li>One weakens endpoint authentication; the other lets request-derived state influence egress routing.</li><li>Neither has an upstream release path because the repositories are archived.</li></ul>
<p>They also demonstrate why updating only Guzzle is insufficient. The unsafe decisions are made by the Azure wrappers around Guzzle.</p>
<h2>The dependency graph adds more pressure</h2>
<p>The latest <code>microsoft/windowsazure</code> release is 0.5.7 from November 2017. It constrains Guzzle to <code>^6.2</code> and PHP-JWT to <code>^4.0</code>. Guzzle 6 is end-of-life, while current security fixes exist on maintained Guzzle 7 and 8 branches.</p>
<p>A fresh Composer 2.10.3 reproduction now refuses to resolve <code>microsoft/windowsazure:0.5.7</code> because PHP-JWT 4.0.0 is affected by two advisories, including critical <a href="https://packagist.org/security-advisories/PKSA-2kqm-ps5x-s4f5">CVE-2021-46743</a>. Existing lock files can keep old deployments running, which is precisely why the silent runtime findings matter more than the failed clean install.</p>
<h2>The Azure protocol has also moved years ahead</h2>
<p>The retired Blob client identifies itself as version 1.5.4 and hardcodes Azure Storage REST API version <code>2017-11-09</code>. Azure still accepts older versions, so common uploads and downloads can work. The application is nevertheless capped at the behaviour of that protocol generation.</p>
<p>Storage API version <code>2019-12-12</code> already introduced blob index tags, blob versioning, querying blob contents, restoring soft-deleted containers, improved OAuth bearer challenges, and larger upload limits. Microsoft now documents 2026 service versions and recommends current versions for present-day behaviour and optimizations.</p>
<h2>Why “just call REST” was not a cheap answer</h2>
<p>A production Azure client must handle request signing, SAS semantics, token acquisition and refresh, XML parsing, continuation tokens, conditional requests, retries, clock skew, streaming block uploads, leases, snapshots, versions, and service-specific errors.</p>
<p>Once an application owns authentication, signing, serialization, retries, and pagination, it has created a private SDK. Replacing an abandoned public SDK with an undocumented internal one is not automatically safer.</p>
<h2>A maintained community migration path now exists</h2>
<p>The independent <a href="https://github.com/php-oss-for-azure/azure-php">PHP OSS for Azure</a> project provides current packages for Blob Storage, Queue Storage, File Share, common Storage primitives, and Microsoft Entra ID authentication. It also provides Blob integrations for Flysystem, Symfony, and Laravel.</p>
<p>At the time of writing, <code>azure-oss/storage-blob</code> 2.3.0 targets PHP 8.2 or later and permits maintained Guzzle 7 and 8 releases:</p>
<pre class="article-code"><code>{
  "php": "^8.2",
  "guzzlehttp/guzzle": "^7.8 || ^8.0",
  "azure-oss/storage-common": "^2.0"
}</code></pre>
<p>The project is community-maintained and is not affiliated with, endorsed by, or supported by Microsoft. It still deserves normal due diligence around maintainers, tests, releases, and service coverage. What it restores is the essential property the archived SDK lacks: a path to review a security report, merge a fix, and publish a release.</p>
<h2>It is not a replacement for every historical Azure API</h2>
<ul class="article-checklist"><li><strong>Blob Storage:</strong> <code>azure-oss/storage-blob</code></li><li><strong>Queue Storage:</strong> <code>azure-oss/storage-queue</code></li><li><strong>Azure File Share:</strong> <code>azure-oss/storage-file-share</code></li><li><strong>Entra ID:</strong> <code>azure-oss/identity</code></li><li><strong>Table Storage:</strong> evaluate direct REST or another maintained abstraction</li><li><strong>Service Bus:</strong> evaluate a current REST or AMQP client behind an application boundary</li><li><strong>Legacy management APIs:</strong> migrate to current Azure Resource Manager APIs</li></ul>
<h2>A low-risk migration sequence</h2>
<ol class="article-checklist"><li><strong>Inventory the packages and namespaces.</strong> Search for <code>WindowsAzure\</code>, <code>MicrosoftAzure\Storage\</code>, <code>ServicesBuilder</code>, and the REST proxy classes.</li><li><strong>Audit proxy state immediately.</strong> Inspect <code>HTTP_PROXY</code>, <code>HTTPS_PROXY</code>, SAPI behaviour, web-server httpoxy mitigations, custom endpoints, and any use of <code>verify=false</code>.</li><li><strong>Record current behaviour.</strong> Cover uploads, downloads, pagination, deletes, metadata, SAS URLs, messages, retries, and Azure errors.</li><li><strong>Introduce an application-owned boundary.</strong> Keep SDK types out of domain code so one service can migrate independently.</li><li><strong>Move one service at a time.</strong> Keep authentication stable during the client migration.</li><li><strong>Re-test every signed or privileged path.</strong> Include SAS scope, expiry, clock skew, leases, and conditional operations.</li><li><strong>Audit the new lock file.</strong> Confirm retired Microsoft packages and EOL HTTP/JWT majors are gone.</li><li><strong>Modernize identity separately.</strong> Roll out managed or workload identity with its own permission review and rollback.</li></ol>
<pre class="article-code"><code>composer why microsoft/windowsazure
composer why microsoft/azure-storage-blob
composer show guzzlehttp/guzzle
composer show firebase/php-jwt
composer audit --locked</code></pre>
<h2>The old SDK does not need to crash to be dangerous</h2>
<p>The most concerning failure mode is not a 500 response. It is a successful request whose endpoint identity was not verified, or whose network route came from attacker-influenced process state.</p>
<p>That is why a green health check cannot settle this decision. The SDK is retired, its protocol is frozen, its dependency constraints block normal upgrades, and its own transport wrapper contains security decisions that modern HTTP clients explicitly avoid.</p>
<p>If one of these packages appears in a PHP application, review the proxy and TLS paths now, then plan a deliberate migration. Credit is due to the PHP OSS for Azure maintainers for making that migration achievable without forcing every PHP team to build an Azure client from raw REST calls.</p>
<p class="article-sources"><strong>Primary sources:</strong> <a href="https://github.com/Azure/azure-sdk-for-php">retired Microsoft SDK</a> · <a href="https://github.com/Azure/azure-sdk-for-php/blob/master/src/Common/Internal/Http/HttpClient.php">TLS verification branch</a> · <a href="https://github.com/Azure/azure-storage-php/blob/master/azure-storage-common/src/Common/Internal/ServiceRestProxy.php">Storage proxy branch</a> · <a href="https://docs.guzzlephp.org/en/stable/quickstart.html#environment-variables">Guzzle’s HTTP_PROXY safeguard</a> · <a href="https://httpoxy.org/">httpoxy explanation</a> · <a href="https://learn.microsoft.com/en-us/rest/api/storageservices/versioning-for-the-azure-storage-services">Azure Storage versioning</a> · <a href="https://github.com/php-oss-for-azure/azure-php">PHP OSS for Azure</a> · <a href="https://php-oss-for-azure.github.io/storage-blob/migrate-from-microsoft-azure-storage-blob">Blob migration guide</a></p>
HTML;

        $publishedAt = new \DateTimeImmutable('2026-08-29 12:00:00+00:00');

        $this->addSql(
            'INSERT INTO blog_articles (slug, title, description, category, read_time_minutes, published_at, updated_at, content_html, cta_label, cta_path, visual_class, visual_lines, how_to_steps) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                'retired-azure-sdk-for-php-migration',
                'The Retired Azure SDK for PHP Can Fail Quietly',
                'Two transport-security paths in Microsoft’s archived PHP SDK can keep Azure requests working while weakening TLS verification or proxy routing.',
                'PHP and cloud security',
                13,
                $publishedAt,
                $publishedAt,
                $contentHtml,
                'Open the migration guide',
                'https://php-oss-for-azure.github.io/storage-blob/migrate-from-microsoft-azure-storage-blob',
                'azure',
                ['HTTPS_PROXY → verify=false', 'HTTP_PROXY → request route', 'azure-oss/storage-blob'],
                [
                    ['name' => 'Audit proxy state', 'text' => 'Inspect HTTP_PROXY, HTTPS_PROXY, TLS verification, SAPI behaviour, and web-server mitigations.'],
                    ['name' => 'Inventory SDK usage', 'text' => 'Find retired Azure packages, namespaces, services, and signed or privileged request paths.'],
                    ['name' => 'Migrate one service', 'text' => 'Replace Blob, Queue, File Share, or identity code behind an application-owned boundary.'],
                    ['name' => 'Verify the new path', 'text' => 'Test TLS, proxy routing, signed URLs, operations, and the final Composer dependency graph.'],
                ],
            ],
            [
                Types::STRING,
                Types::STRING,
                Types::TEXT,
                Types::STRING,
                Types::SMALLINT,
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
        $this->addSql(
            'DELETE FROM blog_articles WHERE slug = ?',
            ['retired-azure-sdk-for-php-migration'],
            [Types::STRING]
        );
    }
}

// phpcs:enable Generic.Files.LineLength.TooLong
