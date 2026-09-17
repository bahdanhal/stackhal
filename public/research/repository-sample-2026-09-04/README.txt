Repository creation-date sample: September 4, 2026
Recovered and verified: September 17, 2026

FILES AND REPRODUCTION
observations.sqlite.gz contains recorded observations, not raw source manifests.
Download and decompress it, then run:
  sqlite3 -readonly -header -column observations.sqlite < verify.sql
languages.csv contains the language aggregate query output, with no total row.

SHA-256 of the decompressed public database:
ef50e8c23862a6698a05ac89b2b684ff90c863bd39b826aa1d6a52f0f9d76dfb
SHA-256 of the consistent archived-source backup used for this export:
52725e64aad97e52cd00ae20f87166463a5c6645cdbc0b0b001072e38a28b708

PROVENANCE
The original task retained sept4_exhaustive.db, its WAL journal, the earlier
sept4_full_scan.db, collection scripts and registry caches. A SQLite backup
included committed WAL records; no collector was rerun during recovery.
The archived scripts exhaustive_scan_sept4.py and complete_100pct_census.py
establish the selection, parsing and sampling behavior described below.
The source backup passes PRAGMA quick_check and has no orphan dependency rows.
The public export keeps repos, dependencies and scanned_slices, preserving row
IDs and recorded fields. It replaces each repo_name with its SHA-256 digest and
omits registry_cache. Hashing reduces identity exposure; it is not a guarantee
of anonymity. The export script is versioned in the Stackhal repository at
scripts/export-census-evidence.php. The archived source remains preserved locally.

COLLECTION SCOPE
Target: created:2026-09-04TSTARTZ..2026-09-04TENDZ language:LANG fork:false size:>10
Languages: TypeScript, Python, JavaScript, Java, C#, Rust, Go and PHP.
Search parameters: per_page=100, page<=10, sort=updated.
Initial slices: twelve two-hour windows per language.
Refinement: TypeScript, Python and JavaScript daytime windows (06:00-18:00 UTC)
replaced with 30-minute windows. The final database has 150 stored slice records.
Completed slice times: 2026-09-05T14:00:21.428990+00:00 through
2026-09-05T19:20:19.448002+00:00.
Slice counters sum to 39,044 fetched entries and 25,327 detected-manifest entries.
They are not distinct repository totals and do not reconcile one-to-one with
retained records. Reprocessing, overlapping language results, replacement of
repository summaries, and omitted missing manifests complicate that comparison.
No slice counter reaches 1,000. total_count, incomplete_results, original search
responses, creation timestamps and source commit IDs were not stored in repos.
This cannot establish complete coverage, including within the selected filters.

MANIFEST COVERAGE
The collectors try common manifest paths on main and master. The earlier script
also uses tree fallbacks. Only one detected manifest is analyzed per repository.
Fetch errors can look like missing manifests. The refinement stores NONE records
for TypeScript, Python and JavaScript; the initial collector omits repositories
without detected manifests for the other five languages. Missing-manifest rates
cannot be compared across languages. A new repository can contain older code.

DEPENDENCY ANALYSIS
total_deps is the number of entries produced by heuristic manifest parsers, not
unique resolved packages. The Python TOML parser is a broad quoted-string regex;
its declaration count may include non-dependency strings.
Only the first ten parsed entries are considered, in manifest order. Successful
registry lookups create dependency rows. Go, Java and C# have no registry-analysis
rows: their zeros are absence of measurement, not evidence of safe dependencies.
npm and PyPI strip leading non-digits and try an exact registry version; if it
does not match, they fall back to latest. crates.io uses prefix matching and a
fallback entry. Packagist flags package abandonment and its release-age lookup
does not resolve the declared constraint. These are not package-manager solvers.
Registry flags were fetched after the target creation date, not necessarily as
they stood at repository creation. npm deprecation, PyPI/crates.io yanking and
Packagist abandonment must not be treated as a shared vulnerability classifier.
No advisory matching, actual build, deployed runtime or exploitability test ran.
Recorded age uses a midnight UTC target date and available registry metadata;
future or missing release times yield no age. Cross-language age/safety rankings
are not justified by these different parsers and version-resolution rules.

COUNT RECONCILIATION
Unique repository rows: 34,187. Detected manifests: 25,322. NONE rows: 8,865.
Parsed declaration entries: 401,703. Stored analysis rows: 171,796.
Per-repository sampled_deps counters: 171,766 (30 fewer than analysis rows).
Repository summaries use INSERT OR REPLACE, but dependency rows are appended.
This can preserve extra rows after reprocessing; the exact history is not stored.
Exact repeated rows (all observation fields except id): 154 extra rows.
Distinct full recorded observations: 171,642. This is an observation-level
deduplication, not a count of installed packages or unique dependency declarations.
Repeats may also come from repeated manifest sections; section origin is not stored.
Scanner-flagged rows: 2,187. Distinct flagged repositories: 1,981.
Repeated flagged repository/ecosystem/package/spec keys: 0.
Registry breakdown (rows / flagged rows / flagged repositories):
  npm:       129,964 / 2,070 / 1,868
  pypi:       35,812 /   106 /   102
  packagist:   2,564 /     5 /     5
  crates:      3,456 /     6 /     6

GO INTERPRETATION
575 retained Go repositories; 574 version lines extracted; one DEFAULT record.
Version groups decoded from stored labels: <=1.25: 291; 1.26: 177; 1.27: 106.
291/575 = 50.6% rounded to one decimal place.
Labels EOL and Modern are original heuristic scanner output. The parser reads
^go VERSION from go.mod, ignores toolchain, and records no actual compiler use.
The grouping does not validate those original support-status labels. A newer
toolchain can build a module with an older minimum version. DEFAULT means no
parsed version; it is not a supported/unsupported-runtime classification.

LIMITS OF REPRODUCTION
The downloadable observations and queries allow independent count verification.
They do not allow full collection replay or independent reparsing of the original
manifests. Raw manifests and pinned commits are absent from the archived database.
There is no AI-authorship field or control group. AI causation, compiler-friction
causation and counts of vulnerable applications are not established.

PRIMARY DOCUMENTATION
https://docs.github.com/en/rest/search/search
https://go.dev/doc/toolchain
