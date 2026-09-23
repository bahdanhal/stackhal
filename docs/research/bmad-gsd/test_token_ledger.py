"""Tests for passive Codex token accounting."""

import json
import tempfile
import unittest
from pathlib import Path

from token_ledger import read_rollout


def event(input_tokens: int, output_tokens: int) -> str:
    usage = {
        "input_tokens": input_tokens,
        "cached_input_tokens": 0,
        "cache_write_input_tokens": 0,
        "output_tokens": output_tokens,
        "reasoning_output_tokens": 0,
        "total_tokens": input_tokens + output_tokens,
    }
    return json.dumps({
        "type": "event_msg",
        "payload": {"type": "token_count", "info": {"total_token_usage": usage}},
    })


class TokenLedgerTest(unittest.TestCase):
    def read_lines(self, lines: list[str]) -> dict[str, object]:
        with tempfile.TemporaryDirectory() as directory:
            path = Path(directory) / "rollout.jsonl"
            path.write_text("\n".join(lines) + "\n", encoding="utf-8")
            return read_rollout(path)

    def test_uses_final_cumulative_count_once(self) -> None:
        result = self.read_lines([event(10, 2), event(10, 2), event(25, 4)])
        self.assertEqual(result["usage"]["total_tokens"], 29)
        self.assertEqual(result["events"], 3)

    def test_rejects_counter_reset(self) -> None:
        with self.assertRaisesRegex(ValueError, "counter reset"):
            self.read_lines([event(25, 4), event(10, 2)])

    def test_missing_usage_is_not_zero(self) -> None:
        with self.assertRaisesRegex(ValueError, "No cumulative token usage"):
            self.read_lines([json.dumps({"type": "event_msg", "payload": {"type": "token_count"}})])


if __name__ == "__main__":
    unittest.main()
