"""Summarize explicit Codex rollout files without printing conversation content."""

from __future__ import annotations

import argparse
import json
from pathlib import Path


FIELDS = (
    "input_tokens",
    "cached_input_tokens",
    "cache_write_input_tokens",
    "output_tokens",
    "reasoning_output_tokens",
    "total_tokens",
)


def read_rollout(path: Path) -> dict[str, object]:
    previous: dict[str, int] | None = None
    events = 0
    with path.open("r", encoding="utf-8") as source:
        for line_number, line in enumerate(source, 1):
            try:
                item = json.loads(line)
            except json.JSONDecodeError as error:
                raise ValueError(f"Invalid JSON on line {line_number}") from error
            if item.get("type") != "event_msg":
                continue
            payload = item.get("payload") or {}
            if payload.get("type") != "token_count":
                continue
            usage = (payload.get("info") or {}).get("total_token_usage")
            if usage is None:
                continue
            current = {field: int(usage.get(field, 0)) for field in FIELDS}
            if any(value < 0 for value in current.values()):
                raise ValueError(f"Negative token count on line {line_number}")
            if previous is not None and any(current[field] < previous[field] for field in FIELDS):
                raise ValueError(f"Token counter reset on line {line_number}")
            previous = current
            events += 1
    if previous is None:
        raise ValueError("No cumulative token usage events")
    if previous["cached_input_tokens"] > previous["input_tokens"]:
        raise ValueError("Cached input exceeds input")
    if previous["reasoning_output_tokens"] > previous["output_tokens"]:
        raise ValueError("Reasoning output exceeds output")
    if previous["total_tokens"] != previous["input_tokens"] + previous["output_tokens"]:
        raise ValueError("Total differs from input plus output")
    return {"usage": previous, "events": events}


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--session", action="append", required=True, metavar="ROLE=ROLLOUT_JSONL")
    args = parser.parse_args()
    sessions: dict[str, dict[str, object]] = {}
    seen_paths: set[Path] = set()
    for assignment in args.session:
        if "=" not in assignment:
            parser.error("Each --session must be ROLE=ROLLOUT_JSONL")
        role, raw_path = assignment.split("=", 1)
        path = Path(raw_path).expanduser().resolve()
        if not role or role in sessions or path in seen_paths:
            parser.error("Session roles and rollout paths must be unique")
        seen_paths.add(path)
        sessions[role] = read_rollout(path)
    totals = {field: sum(entry["usage"][field] for entry in sessions.values()) for field in FIELDS}
    print(json.dumps({"sessions": sessions, "total": totals}, indent=2, sort_keys=True))


if __name__ == "__main__":
    main()
