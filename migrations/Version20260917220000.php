<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917220000 extends AbstractMigration
{
    private const BACKUP_TABLE = 'blog_census_recovery_backup_20260917';
    private const FIELDS = ['title', 'description', 'category', 'read_time_minutes', 'updated_at', 'content_html', 'cta_label', 'cta_path', 'visual_lines', 'how_to_steps'];

    /** @return array<string, array{title: string, description: string, category: string, read_time_minutes: int, content_html: string}> */
    public static function corrections(): array
    {
        return [
            'ai-coding-agents-deprecated-dependencies-census' => [
                'title' => 'What a Scan of 34,187 New Repositories Actually Measured',
                'description' => 'Recovered data from a September 2026 repository scan: sampled dependencies, registry maintenance flags, Go minimum versions, and downloadable evidence.',
                'category' => 'Dependency research',
                'read_time_minutes' => 7,
                'content_html' => <<<'HTML'
<p class="article-lead">The original scan was real. Its database records 34,187 repositories selected by creation date, with 25,322 detected build manifests. We recovered the collection scripts and data, then checked the counts. Here is what those records show, and where the original claims went too far.</p>
<div class="article-callout article-callout-accent"><strong>Recovered data, corrected claims</strong><span>The earlier article confused sampled dependency rows with all declarations. It also treated Go minimum-version lines as observed compilers and blamed AI without authorship evidence. This revision restores the measured results, not those conclusions.</span></div>
<h2>The measured sample</h2>
<p>The queries targeted repositories created on September 4, 2026, across eight languages. Collection ran on September 5, from 14:00 to 19:20 UTC. The retained database has 34,187 distinct repository names. Of these, 25,322 have a detected manifest. The remaining 8,865 have no manifest found by the scanner.</p>
<div class="census-table-scroll" role="region" aria-label="Repository sample by language" tabindex="0"><table class="census-table"><caption>Retained records from the recovered database</caption><thead><tr><th scope="col">Language</th><th scope="col">Repositories</th><th scope="col">Manifests found</th><th scope="col">Analysis rows</th></tr></thead><tbody>
<tr><th scope="row">TypeScript</th><td>12,569</td><td>12,209</td><td>107,692</td></tr>
<tr><th scope="row">Python</th><td>9,296</td><td>5,793</td><td>35,812</td></tr>
<tr><th scope="row">JavaScript</th><td>8,706</td><td>3,704</td><td>22,272</td></tr>
<tr><th scope="row">Java</th><td>1,267</td><td>1,267</td><td>0</td></tr>
<tr><th scope="row">C#</th><td>798</td><td>798</td><td>0</td></tr>
<tr><th scope="row">Rust</th><td>623</td><td>623</td><td>3,456</td></tr>
<tr><th scope="row">Go</th><td>575</td><td>575</td><td>0</td></tr>
<tr><th scope="row">PHP</th><td>353</td><td>353</td><td>2,564</td></tr>
</tbody><tfoot><tr><th scope="row">Total</th><td>34,187</td><td>25,322</td><td>171,796</td></tr></tfoot></table></div>
<p>Zero analysis rows means no package-registry analysis was recorded for that language. It does not mean no dependencies or no security risks.</p>
<h2>What the dependency count means</h2>
<p>The parsers counted 401,703 declaration entries across the detected manifests. This is a parser output, not a count of unique packages. In particular, the Python parser uses broad text patterns that can mistake other TOML strings for dependencies.</p>
<p>The scripts checked at most the first ten parsed dependencies per repository. Successful registry lookups produced 171,796 analysis rows. These rows include 154 exact repeats, excluding the row ID. Counting each full recorded observation once gives 171,642. Repeated entries can come from manifest sections or reprocessing; the stored records do not distinguish those causes.</p>
<p>Per-repository sample counters sum to 171,766, which is 30 fewer than the stored rows. The collectors replace repository summaries but append dependency rows. That design can leave rows and counters out of sync after reprocessing. We keep both figures visible rather than silently choosing one.</p>
<h2>Maintenance flags are not vulnerability findings</h2>
<p>The scanner flagged 2,187 rows across 1,981 repositories. Those flagged rows have no repeated repository/package/version keys. But the flag mixes different registry signals: npm deprecation, PyPI and crates.io yanking, and Packagist abandonment. These are not one shared measure of security.</p>
<p>Version matching was also approximate. For npm and PyPI, the scripts stripped leading range symbols and tried an exact version. If that failed, they used the registry's latest version. Other registries used different rules. This is not a lockfile resolver, and it does not prove which version was installed.</p>
<p>The records are useful leads for a maintenance review. They do not establish 1,981 vulnerable applications. Nor do the stored age values justify ranking languages for safety: parsing, matching, and package coverage differ.</p>
<h2>The Go result, interpreted correctly</h2>
<p>All 575 retained Go records have a detected <code>go.mod</code>. The scanner extracted a <code>go</code> line for 574 of them. The stored distribution is 291 at Go 1.25 or below, 177 at Go 1.26, 106 at Go 1.27, and one without a parsed version. That makes the first group 50.6% of the 575 records.</p>
<figure class="article-figure"><img src="/images/blog/go-minimum-version-sample-2026.svg" width="900" height="360" alt="Go minimum-version groups: 291 at 1.25 or below, 177 at 1.26, 106 at 1.27, and one without a parsed version."><figcaption>Groups decoded from the scanner's stored labels. They describe minimum-version lines, not observed builds or supported-runtime counts.</figcaption></figure>
<p>The old chart called the first group EOL compilers. That was wrong. A module's <code>go</code> line sets its minimum version and affects language rules. A newer toolchain can build it. The scanner did not read the <code>toolchain</code> line, run a build, or inspect CI logs.</p>
<p>To check a real project, run <code>go version</code> in its build environment. Also check <code>go env GOTOOLCHAIN</code> and any workspace or toolchain settings. A manifest-only survey cannot replace that evidence. See the <a href="https://go.dev/doc/toolchain">Go toolchain documentation</a>.</p>
<h2>Why this is a sample, not a global census</h2>
<p>The search query included <code>fork:false size:&gt;10</code>. It excludes forks and repositories of 10 KB or less. It also uses GitHub's language filter. Repository creation time does not date the code: a new repository can contain old work.</p>
<p>Searches used two-hour slices, refined to 30-minute slices during peak hours for three languages. Each query fetched at most ten pages of 100 results. The 150 stored slice records show none with 1,000 fetched results. But the scripts did not retain GitHub's <code>total_count</code> or <code>incomplete_results</code>. That prevents a completeness claim.</p>
<p>Manifest searches tried known paths on <code>main</code> and <code>master</code>, with tree fallbacks in the earlier collector. Failed requests and missing files can both yield no manifest found. Only the refinement for TypeScript, JavaScript, and Python retained those missing-manifest records. Other languages retained detected manifests only. The table therefore cannot compare manifest adoption rates fairly.</p>
<h2>Inspect the evidence</h2>
<p>Download the <a href="/research/repository-sample-2026-09-04/observations.sqlite.gz">recorded observations (SQLite, gzip)</a>, <a href="/research/repository-sample-2026-09-04/languages.csv">language totals (CSV)</a>, and <a href="/research/repository-sample-2026-09-04/verify.sql">verification queries</a>. The <a href="/research/repository-sample-2026-09-04/README.txt">methods and provenance notes</a> explain the fields, hashes, and checks.</p>
<p>The export replaces repository names with stable SHA-256 IDs. It preserves the recorded measurements and includes the slice summaries. It omits registry-cache responses. Raw manifest contents, pinned commit IDs, and full search responses were not retained in this database. You can reproduce its totals, but not fully replay the original collection from these files.</p>
<h2>What we can conclude</h2>
<p>This scan shows how much the meaning of a count depends on collection rules. A declaration, a successful registry lookup, a maintenance flag, and an observed runtime are different things. Combining them hides gaps.</p>
<p>No stored field identifies AI authorship. The sample cannot show that AI caused older version choices or that Rust compilers prevented them. Those remain untested ideas. The practical lesson is to check resolved versions, registry notices, and actual toolchains before making a security claim.</p>
<p class="article-sources">Recovered and checked September 17, 2026. Sources: the archived scan database and scripts; <a href="https://docs.github.com/en/rest/search/search">GitHub search API limits</a>; <a href="https://go.dev/doc/toolchain">Go toolchain semantics</a>. The public export supports the reported record counts, not AI causation or a vulnerability verdict.</p>
HTML,
            ],
        ];
    }

    public function getDescription(): string
    {
        return 'Restore verified repository-sample measurements with public evidence and corrected methodology';
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
