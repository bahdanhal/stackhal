# Composer License Metadata Screening Snapshot

This directory preserves the data used by the StackHal article published on 2026-08-30.

## Scope

- `all_scanned_packages.csv`: the 10,000-package input ranking.
- `audit_summary.json`: immutable output from the original dependency-metadata screen.
- `normalized_conflict_edges.csv`: derived edge table using corrected SPDX-family grouping.
- `wordpress_plugins_audit.csv`: automated header and metadata observations for 100 WordPress plugins.
- `deep_audit_320_packages.csv` and `deep_audit_summary.json`: legacy physical-archive heuristic output.
- `SHA256SUMS`: integrity hashes for the published snapshot and normalized table.

The original screen marked 320 packages for review and recorded 654 dependency edges. Its legacy classifier incorrectly placed LGPL-3.x and several other weak-copyleft identifiers in the strong-copyleft bucket. Running `normalize_conflict_edges.py` produces a corrected metadata grouping: 184 strong-copyleft signals, 444 weak-copyleft signals, and 26 dual GPL/proprietary expressions requiring option-specific review.

## Limitations

These files contain automated observations, not findings of infringement. A dependency edge does not determine derivative-work status, compatibility, linking treatment, distribution, or External Deployment. The physical-archive files retain their original heuristic labels for traceability; labels such as `CONFIRMED_VIOLATION` must not be treated as verified legal conclusions.

Package metadata and download archives can change after the snapshot. Reproduce a current result using exact `composer.lock` data and inspect the corresponding physical `LICENSE`, `COPYING`, `NOTICE`, and source headers.

## Rebuild the normalized edge table

```bash
python3 normalize_conflict_edges.py
```

Expected summary:

```json
{"counts": {"DUAL_LICENSE_REVIEW": 26, "STRONG_COPYLEFT": 184, "WEAK_COPYLEFT": 444}, "total_edges": 654}
```
