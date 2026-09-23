# BMad versus GSD Core: controlled Codex study protocol

Status: prepared, not executed. No result, winner, or token total may be published before measured runs finish.

## Question

On the same real coding tasks, how do the two *documented* coordination flows differ in delivered quality, total Codex tokens, elapsed time, and required human intervention? The unit of analysis is a completed task run, not an individual model message or a page of planning prose.

## Pre-registration before the first measured run

Record the exact BMad and GSD Core release/commit, Codex build, model and reasoning settings for every role, worktree starting commit, task briefs, hidden tests, run order, concurrency cap, time limit, reviewer rubric, and stop rules. Freeze them. Do not update a framework between arms or change the task brief after seeing the first result.

Start with two pilot tasks to test workflow fidelity, isolation, and token collection; exclude pilots from headline comparisons. Then run four bounded tasks in three independent repetitions per arm (24 measured runs). Select tasks of varied shape (bug fix, user-facing UI, integration, and testability/maintenance) from real repo needs. Give each arm the same intent and acceptance criteria, not a prescribed common sequence of steps. Randomize arm order within each task/repetition pair and use a clean worktree from the same commit for every run. Do not expose one arm's artifacts to the other.

The coordinator chooses the framework's documented small or full route. Record that choice. Any material user decision required by one framework must be answered by a predefined decision sheet shared with both arms; count additional unscripted questions as human interventions. Do not silently answer them on the framework's behalf.

## Outcome collection

- Primary: independent acceptance verdict against frozen hidden tests and a blinded review of requirement coverage, functional defects, and regressions. Reviewers must not know which framework produced a diff.
- Secondary: total tokens, uncached input tokens, output tokens, elapsed wall time, agent count, retries, and human interventions. Report the full distribution and task-paired differences, not only a mean or a cherry-picked winner.
- Include all coordinator, specialist, executor, verifier, review, and repair work in the arm total. Keep independent outcome judging outside both arm totals and report its cost separately.
- Preserve task brief, worktree commit/diff, framework artifacts, command/test results, reviewer verdict, and a per-agent session ledger. Do not deploy or merge experimental changes automatically.

## Subscription-Codex token accounting (no API key or third-party proxy)

Use Codex's local rollout JSONL files as a passive measurement source. Before each run, record the exact rollout file path for the coordinator and every spawned agent. A completed rollout's `event_msg` / `token_count` / `info.total_token_usage` is cumulative **within that rollout**; use its final value once, not the sum of successive cumulative events. `cached_input_tokens` is a subset of input; `reasoning_output_tokens` is a subset of output. The read-only helper `token_ledger.py` accepts an explicit role-to-file manifest and emits only usage totals, never conversation content.

This is a local Codex-reported count, not an independent provider bill. Pilot-check one ordinary run and one multi-agent run against Codex's own displayed session total. Verify whether a parent rollout includes delegated usage before summing children; if it does, use non-overlapping per-agent counters or mark attribution unresolved. A missing final usage event, counter reset, duplicate path, inaccessible child rollout, or unexplained mismatch makes the trial's token result **unknown**, not zero. Re-run or omit that pair from token comparisons while retaining quality outcomes. Preserve the raw rollout files locally but publish only redacted usage summaries and stable artifact hashes; session files can contain private prompts and tool outputs.

Do not use account quota percentages as token counts. Do not use a context-compressing proxy in one arm: it changes the treatment. A future pass-through proxy may provide a cross-check only after confirming it preserves prompts, tool traffic, authentication, and model responses, and only when both arms use it identically.

## Reporting gate

Publish the study only with the frozen protocol, exact task-level measurements, rejected/missing-usage trials, quality evidence, and uncertainty. Do not claim that one framework is faster or cheaper merely because it produced fewer planning artifacts or because one successful run used fewer tokens.
