# Illustration plan: I Think Debugging Is Almost Dead

The 18 prose paragraphs are counted from “I enjoyed debugging”; the sources note is excluded. Illustrations explain the reasoning without changing the essay's claims or inventing evidence for the external accounts.

## Paragraph-by-paragraph direction

| Paragraph | Subject | Concrete visual | Placement decision |
| --- | --- | --- | --- |
| 1 | The personal pleasure of debugging | A real debugging session: breakpoint, call stack, failing test, and the line that explains it. | Keep the opening personal and uncluttered. A future cover could use a clearly labeled illustrative session. |
| 2 | Giving an agent a usable investigation environment | The actual inputs to an investigation: deployed commit, configuration, runtime records, reproducer, and experiment permissions. | Combine with the payment evidence diagram at paragraph 11. |
| 3 | Complex bugs are not automatically protected from automation | A concrete cross-service failure path, with the evidence needed at each boundary. | Do not draw a speculative human-versus-AI capability chart. |
| 4 | The reported Millennium crash investigation | Core dump and vendor binary feeding a disassembly comparison. | A schematic could explain the reported method. Do not invent a core dump, assembly listing, or screenshot from this case. |
| 5 | Investigating code the team does not own | Show the boundary between the application and an external library, and the crash evidence crossing it. | Combine with paragraph 4 if that illustration is later selected. |
| 6 | Datadog's reported comparison with engineer diagnoses | Two written root-cause conclusions compared against the same incident. | No invented scores or success percentages; the text supplies none. |
| 7 | Reports, projects, and confirmed upstream patches | Separately labeled counts: 2,300 reports, 392 projects, 421 known patched. | A future factual figure must preserve the snapshot date and different units; do not make a conversion funnel. |
| 8 | Firefox's testing infrastructure | Test case generation, execution in the browser harness, failure artifact, and revised theory. | Explain the mechanism; do not fabricate an actual Mozilla test run. |
| 9 | Checking a proposed fix | The same failing request under the original code, a request-dropping workaround, and a valid correction. Check both absence of a crash and intended processing. | **Diagram: debugging-fix-verification.svg.** |
| 10 | The repository may describe a different system | Compare repository state with the incident's deployed commit, loaded configuration, dependencies, and data. | Fold this into the payment evidence diagram. |
| 11 | Payment succeeds but no order exists | Payment success, callback HTTP 200, missing order; then the release, event trail, and database evidence needed to connect them. | **Diagram: debugging-payment-evidence.svg.** Clearly mark as a hypothetical incident. |
| 12 | Reproducing an intermittent race | Two workers read the same counter value, one pauses, both write the same incremented value. The test forces this ordering. | **Diagram: debugging-race-reproducer.svg.** The lost-update example is illustrative, not a claim about a cited incident. |
| 13 | Useful access instead of endless screenshots | Read-only logs and event-linked records queried from a faithful test environment. | Reuse the evidence diagram; avoid a generic “AI agent connected to everything” picture. |
| 14 | Missing business definitions | A small worked refund/revenue example showing two plausible definitions with different results. | A future diagram needs explicit hypothetical amounts and accounting assumptions. |
| 15 | How internal investigation changes outsourcing | A real sequence: first attempt, handoff, access setup, investigation. | Do not invent a hiring or contractor-demand trend chart. |
| 16 | More formerly uneconomic bugs may be investigated | A concrete backlog item moving from “not worth investigating” to “investigation running.” | Keep as prose: the essay states an expectation, not measured economics. |
| 17 | Human judgment and responsibility | The request-dropping workaround passes the wrong check but fails the intended outcome. | Refer back to the verification diagram at paragraph 9. |
| 18 | Responsibility without personally finding every explanation | A proposed patch and its evidence awaiting a person's release decision. | Keep the conclusion personal; no extra closing illustration. |

## Included assets

- `public/images/blog/debugging-fix-verification.svg`
- `public/images/blog/debugging-payment-evidence.svg`
- `public/images/blog/debugging-race-reproducer.svg`

Each diagram also has a `-mobile.svg` layout with larger labels and a vertical reading order. The responsive HTML is saved in `docs/blog/debugging-illustration-fragments.html`.

These are editable technical diagrams. No generated decorative artwork is used. The first and third diagrams are explicitly illustrative examples. The payment diagram follows the essay's hypothetical case and does not claim to identify its cause.

Migration `Version20260909134500` inserts these after paragraphs 9, 11, and 12 of the English article. It preserves the original prose, skips a missing article, rejects changed paragraph anchors, and can remove its exact additions on rollback. Other articles and the homepage are unchanged.
