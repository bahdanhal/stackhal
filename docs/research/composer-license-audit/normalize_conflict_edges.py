#!/usr/bin/env python3
"""Create a normalized edge table from the immutable first-pass JSON snapshot."""

import csv
import json
from pathlib import Path


STRONG_PREFIXES = ("gpl-", "agpl-", "osl-", "sspl", "eupl-", "cc-by-sa-")
WEAK_PREFIXES = ("lgpl-", "mpl-", "epl-", "cddl-")


def normalized_type(license_name: str) -> str:
    normalized = license_name.strip().lower()
    if " or proprietary" in normalized:
        return "DUAL_LICENSE_REVIEW"
    if normalized.startswith(WEAK_PREFIXES):
        return "WEAK_COPYLEFT"
    if normalized.startswith(STRONG_PREFIXES):
        return "STRONG_COPYLEFT"
    return "UNCLASSIFIED_REVIEW"


def main() -> None:
    directory = Path(__file__).resolve().parent
    data = json.loads((directory / "audit_summary.json").read_text(encoding="utf-8"))
    rows = []
    for package in data["tainted_packages"]:
        for conflict in package["conflicts"]:
            rows.append({
                "rank": package["rank"],
                "package_name": package["name"],
                "package_version": package["version"],
                "declared_license": package["declared"],
                "dependency_name": conflict["package"],
                "dependency_version": conflict["version"],
                "dependency_license": conflict["license"],
                "normalized_signal": normalized_type(conflict["license"]),
                "dependency_path": conflict["path"],
            })

    output = directory / "normalized_conflict_edges.csv"
    with output.open("w", encoding="utf-8", newline="") as handle:
        writer = csv.DictWriter(handle, fieldnames=rows[0].keys())
        writer.writeheader()
        writer.writerows(rows)

    counts = {}
    for row in rows:
        signal = row["normalized_signal"]
        counts[signal] = counts.get(signal, 0) + 1
    print(json.dumps({"total_edges": len(rows), "counts": counts}, sort_keys=True))


if __name__ == "__main__":
    main()
