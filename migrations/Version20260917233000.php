<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917233000 extends AbstractMigration
{
    private const BACKUP_TABLE = 'blog_census_rewrite_backup_20260917';
    private const FIELDS = ['title', 'description', 'category', 'read_time_minutes', 'updated_at', 'content_html', 'cta_label', 'cta_path', 'visual_lines', 'how_to_steps'];

    /** @return array<string, array{title: string, description: string, category: string, read_time_minutes: int, content_html: string}> */
    public static function corrections(): array
    {
        return [
            'ai-coding-agents-deprecated-dependencies-census' => [
                'title' => 'New Repositories, Old Dependencies: A 34,187-Repository Study',
                'description' => 'A study of registry maintenance signals in newly created GitHub repositories, with concrete Next.js, ESLint and Recharts findings and downloadable data.',
                'category' => 'Dependency research',
                'read_time_minutes' => 5,
                'content_html' => <<<'HTML'
<p class="article-lead">A new repository is not a fresh dependency stack. I scanned 34,187 GitHub repositories selected by creation date and found registry maintenance flags in the sampled lookups for 1,981 of them. The notices ranged from unsupported release lines to security warnings. New project, existing maintenance work.</p>
<h2>The study in numbers</h2>
<p>The scan targeted repositories created on September 4, 2026, across eight languages. Collection ran the next day. The database contains 34,187 distinct repositories and 25,322 detected build manifests. Successful package lookups cover 21,653 repositories across npm, PyPI, Packagist, and crates.io.</p>
<div class="article-callout article-callout-accent"><strong>2,187 flagged lookups across 1,981 repositories</strong><span>These are registry signals attached to the scanner's version matches. They are leads for review, not confirmed installed versions or a count of vulnerable applications.</span></div>
<p>The scanner considered at most the first ten parsed dependency entries per project. It recorded 171,796 successful lookup rows. This is a sample of declared dependencies, not every package in every repository.</p>
<h2>Where the maintenance signals appeared</h2>
<div class="census-table-scroll" role="region" aria-label="Registry maintenance signals" tabindex="0"><table class="census-table"><caption>Successful lookups and maintenance flags by registry</caption><thead><tr><th scope="col">Registry</th><th scope="col">Projects checked</th><th scope="col">Lookup rows</th><th scope="col">Flagged projects</th><th scope="col">Flagged rows</th></tr></thead><tbody>
<tr><th scope="row">npm</th><td>15,134</td><td>129,964</td><td>1,868</td><td>2,070</td></tr>
<tr><th scope="row">PyPI</th><td>5,662</td><td>35,812</td><td>102</td><td>106</td></tr>
<tr><th scope="row">Packagist</th><td>338</td><td>2,564</td><td>5</td><td>5</td></tr>
<tr><th scope="row">crates.io</th><td>519</td><td>3,456</td><td>6</td><td>6</td></tr>
</tbody><tfoot><tr><th scope="row">Total</th><td>21,653</td><td>171,796</td><td>1,981</td><td>2,187</td></tr></tfoot></table></div>
<p>The flag has a different meaning in each registry. npm records deprecation notices. PyPI and crates.io record yanked releases. Packagist records package abandonment. A security warning, a retired release line, and a withdrawn release need different responses.</p>
<p>These totals are not a language safety ranking. The parsers and version matching differ, and the scan did not check every dependency. A lower count does not make an ecosystem safer.</p>
<h2>Three concrete npm findings</h2>
<p>Three familiar packages account for many of the npm hits: <code>next</code> appears in flagged lookups for 857 repositories, <code>eslint</code> for 517, and <code>recharts</code> for 236. These counts can overlap within a project. They describe flagged version matches, not deprecated packages as a whole.</p>
<ul class="article-checklist">
<li><strong>Next.js: check the security notice.</strong> The stored matches include Next.js 14.2.15 in 124 flagged rows and 14.2.5 in 114. Their registry notices point to a security update. The <a href="https://nextjs.org/blog/security-update-2025-12-11">maintainer's advisory</a> describes affected App Router applications and distinguishes them from Pages Router applications. Confirm the resolved version and application setup before assessing exposure.</li>
<li><strong>ESLint: check release-line support.</strong> The matches include ESLint 9.39.5 in 58 flagged rows. Its stored notice concerns lack of support. The <a href="https://eslint.org/version-support/">ESLint support policy</a> gives release-line status. A linter maintenance task is not the same finding as an exposed application vulnerability.</li>
<li><strong>Recharts: plan the migration.</strong> The matches include Recharts 2.12.7 in 77 flagged rows. The stored notices direct users from older branches to version 3. The <a href="https://github.com/recharts/recharts/wiki/3.0-migration-guide">migration guide</a> is the useful next step: check API changes and chart behavior rather than treating a major-version bump as a routine patch.</li>
</ul>
<p>The scanner used approximate version matching, not lockfiles. For npm and PyPI, it tried a cleaned exact version, then fell back to latest if no match existed. These examples identify things to inspect; they do not prove that each project installed the matched version.</p>
<h2>Review the dependency stack before the first release</h2>
<p>Do not wait for a project to age before checking its dependencies. A copied template or a familiar version pin can carry old choices into new work. The scan does not identify who made those choices or whether AI was involved.</p>
<ol class="article-checklist">
<li><strong>Start with what resolves.</strong> Inspect the lockfile and installed tree. For an npm project, <code>npm ls next eslint recharts</code> shows the installed versions of the packages discussed here.</li>
<li><strong>Read the notice.</strong> Identify whether it concerns security, support, replacement, or a withdrawn release. Follow the maintainer's guidance for the relevant version.</li>
<li><strong>Choose the right update.</strong> A patched release and a major migration are different jobs. Check compatibility and affected features before changing the constraint.</li>
<li><strong>Verify the result.</strong> Reinstall from the updated lockfile, run tests, and check security advisories against resolved versions. Record the change in the project's maintenance notes.</li>
</ol>
<h2>Scope and data</h2>
<p>The searches used creation-date windows, language filters, and <code>fork:false size:&gt;10</code>. They exclude forks and repositories of 10 KB or less. A repository's creation date does not date its code. Manifest discovery tried common paths and branches; it did not cover every possible project layout.</p>
<p>The scanner checked one detected manifest per repository. Missing files and failed requests were not always distinguishable. Registry metadata was fetched after the target date. This is a selected repository sample, not a complete census or a representative measure of all new software.</p>
<p>Download the <a href="/research/repository-sample-2026-09-04/observations.sqlite.gz">observations (SQLite, gzip)</a>, <a href="/research/repository-sample-2026-09-04/languages.csv">language totals (CSV)</a>, and <a href="/research/repository-sample-2026-09-04/verify.sql">verification queries</a>. The <a href="/research/repository-sample-2026-09-04/README.txt">methodology notes</a> document selection, parsing, matching, and count reconciliation. Repository names in the export are replaced with stable SHA-256 IDs.</p>
<details><summary>Counting and reproduction details</summary><p>The 171,796 lookup rows include 154 exact repeated observations, leaving 171,642 distinct full observations. Per-project sample counters sum to 171,766, a 30-row difference. The collector replaces project summaries but appends lookup rows, which can leave counters out of sync after reprocessing. The 2,187 flagged rows have no repeated repository/package/version keys.</p><p>The stored records let you reproduce the totals and package examples. Raw manifests, pinned source commits, and full search responses are not included in the database. The parser's declaration counts are heuristic, so they are not presented as a count of installed or unique dependencies.</p></details>
<h2>New code still needs maintenance</h2>
<p>The useful result is not that every flagged project is unsafe. It is that a new repository can already carry dependency choices worth checking. The registry notices provide specific places to start: resolve the version, read the warning, and make the right change before release.</p>
HTML,
            ],
        ];
    }

    public function getDescription(): string
    {
        return 'Rewrite the repository study around dependency maintenance findings without retrospective or Go framing';
    }

    public function up(Schema $schema): void
    {
        // The census was edited outside seed migrations. Preserve exact stored public
        // article values instead of guessing its original content or timestamps.
        $this->addSql('CREATE TABLE ' . self::BACKUP_TABLE . ' AS SELECT slug, locale, ' . implode(', ', self::FIELDS) . ' FROM blog_articles WHERE 1 = 0');
        foreach (self::corrections() as $slug => $correction) {
            $this->addSql('INSERT INTO ' . self::BACKUP_TABLE . ' SELECT slug, locale, ' . implode(', ', self::FIELDS) . ' FROM blog_articles WHERE slug = ? AND locale = ?', [$slug, 'en']);
            $this->addSql(
                'UPDATE blog_articles SET title = ?, description = ?, category = ?, read_time_minutes = ?, updated_at = CURRENT_TIMESTAMP, content_html = ?, cta_label = ?, cta_path = ?, visual_lines = ?, how_to_steps = ? WHERE slug = ? AND locale = ?',
                [$correction['title'], $correction['description'], $correction['category'], $correction['read_time_minutes'], $correction['content_html'], '', '', '[]', '[]', $slug, 'en']
            );
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::corrections() as $slug => $correction) {
            $originalExists = $this->connection->fetchOne('SELECT slug FROM ' . self::BACKUP_TABLE . ' WHERE slug = ? AND locale = ?', [$slug, 'en']);
            if ($originalExists === false) {
                continue;
            }
            $current = $this->connection->fetchAssociative('SELECT title, description, category, read_time_minutes, content_html, cta_label, cta_path, visual_lines, how_to_steps FROM blog_articles WHERE slug = ? AND locale = ?', [$slug, 'en']);
            $this->abortIf(
                $current === false || $current['title'] !== $correction['title'] || $current['description'] !== $correction['description'] || $current['category'] !== $correction['category'] || (int) $current['read_time_minutes'] !== $correction['read_time_minutes'] || $current['content_html'] !== $correction['content_html'] || $current['cta_label'] !== '' || $current['cta_path'] !== '' || (string) $current['visual_lines'] !== '[]' || (string) $current['how_to_steps'] !== '[]',
                'The corrected article has since changed; restore its snapshot manually rather than overwriting newer edits.'
            );
            $assignments = array_map(static fn (string $field): string => $field . ' = (SELECT ' . $field . ' FROM ' . self::BACKUP_TABLE . ' WHERE slug = ? AND locale = ?)', self::FIELDS);
            $parameters = [];
            foreach (self::FIELDS as $field) {
                $parameters[] = $slug;
                $parameters[] = 'en';
            }
            $parameters[] = $slug;
            $parameters[] = 'en';
            $this->addSql('UPDATE blog_articles SET ' . implode(', ', $assignments) . ' WHERE slug = ? AND locale = ?', $parameters);
        }
        $this->addSql('DROP TABLE ' . self::BACKUP_TABLE);
    }
}

// phpcs:enable Generic.Files.LineLength.TooLong
