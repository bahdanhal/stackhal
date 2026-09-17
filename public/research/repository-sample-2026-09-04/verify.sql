-- Run against the decompressed public observations.sqlite export.
PRAGMA quick_check;
SELECT COUNT(*) AS repositories, COUNT(DISTINCT repo_name) AS unique_repositories,
       SUM(manifest_path != 'NONE') AS detected_manifests,
       SUM(total_deps) AS parsed_declarations, SUM(sampled_deps) AS sample_counters
FROM repos;
SELECT COUNT(*) AS analysis_rows, SUM(is_deprecated) AS scanner_flagged_rows,
       COUNT(DISTINCT CASE WHEN is_deprecated = 1 THEN repo_name END) AS flagged_repositories
FROM dependencies;
SELECT SUM(n - 1) AS repeated_full_rows
FROM (
    SELECT COUNT(*) AS n FROM dependencies
    GROUP BY repo_name, ecosystem, package_name, version_spec, matched_version,
             is_deprecated, dep_reason, published_at, age_days
    HAVING COUNT(*) > 1
);
SELECT COALESCE(SUM(n - 1), 0) AS repeated_flagged_keys
FROM (
    SELECT COUNT(*) AS n FROM dependencies WHERE is_deprecated = 1
    GROUP BY repo_name, ecosystem, package_name, version_spec
    HAVING COUNT(*) > 1
);
SELECT COUNT(*) AS orphan_dependency_rows FROM dependencies d
LEFT JOIN repos r USING (repo_name) WHERE r.repo_name IS NULL;
SELECT r.language, COUNT(*) AS repositories,
       SUM(r.manifest_path != 'NONE') AS detected_manifests,
       SUM(r.total_deps) AS parsed_declarations, SUM(r.sampled_deps) AS sampled_counter,
       (SELECT COUNT(*) FROM dependencies d JOIN repos dr USING (repo_name)
        WHERE dr.language = r.language) AS analysis_rows,
       (SELECT COUNT(*) FROM dependencies d JOIN repos dr USING (repo_name)
        WHERE dr.language = r.language AND d.is_deprecated = 1) AS scanner_flagged_rows
FROM repos r GROUP BY r.language ORDER BY r.language;
SELECT ecosystem, COUNT(*) AS analysis_rows, COUNT(DISTINCT repo_name) AS analyzed_repositories,
       SUM(is_deprecated) AS scanner_flagged_rows,
       COUNT(DISTINCT CASE WHEN is_deprecated = 1 THEN repo_name END) AS flagged_repositories
FROM dependencies GROUP BY ecosystem ORDER BY ecosystem;
-- Stored Go labels are historical scanner output, NOT observed compiler status.
SELECT runtime_status AS stored_scanner_label, COUNT(*) AS repositories
FROM repos WHERE language = 'Go' GROUP BY runtime_status ORDER BY runtime_status;
SELECT COUNT(*) AS stored_slices, SUM(repos_found) AS fetched_entries,
       SUM(repos_parsed) AS detected_manifest_entries,
       MIN(completed_at) AS first_completed_at, MAX(completed_at) AS last_completed_at,
       SUM(repos_found >= 1000) AS slices_at_result_cap
FROM scanned_slices;
