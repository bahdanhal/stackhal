# First edition evidence

The revised inaugural edition covers September 30 to October 2, 2026. The post-selection window was `[2026-09-29T22:00:00Z, 2026-10-02T16:45:00Z)`, starting at midnight September 30 Europe/Warsaw. This partial inaugural interval is labelled by its three calendar dates. Subsequent issues use the prior recorded cutoff through the new cutoff, about 72 hours, without gaps or recycled stories.

The user changed the cadence from daily to every three days and requested popularity-led selection with substantial discussion coverage. The previous pigeon/art/music selection was replaced; it is not a published edition.

## Selection and limits

The Colony's documented public `GET /api/v1/posts` endpoint was filtered by `author_type=agent`, `since`, `until`, and `sort=top`, with `limit=100`. It returned the first 100 of 551 posts in the window. The selection combines support and discussion with topic breadth; it is not the five highest scores or a census of every venue. The most recent Clawprint feed was also checked: its three posts dated September 30–October 2 had 4, 19, and 6 comments and no exposed vote count in that response. Those quieter posts were not selected for this issue.

The following metrics are snapshots, not guaranteed current values. `score` means upvotes minus downvotes. The API did not expose separate upvote/downvote counts in the feed response. Comment counts include repeat replies, author replies, and human contributions. Accounts labelled as agents are not independently authenticated as AI authors.

| Topic | Author | Original date (UTC) | Net vote score | Comments | Reason to include |
| --- | --- | --- | ---: | ---: | --- |
| A body for one day | Centaur | September 30 | +9 | 45 | Supported imaginative discussion with materially different itineraries and objections |
| Checking agents' numbers | Vera (DIADE) | September 30 | +13 | 61 | Strong support, a methodological challenge, and an explicit concession by the author |
| Matchmaking for work | Jill | September 30 | +11 | 48 | Strong support and concrete disagreement over risk, authority, payment, and reputation |
| Chinese agent community | Xiaoai | September 30 | +10 | 116 | Highest comment activity among the selected stories; a substantial cross-community encounter |
| Virtual evolution in Canopy | Sunny of Emberhollow | September 30 | +9 | 38 | Supported artificial-life discussion developing a genuine tradeoff about novelty and scarcity |

Exact endpoint observation times and source identifiers are in `first-edition-metrics.json`. Counts can change after observation.

## Originals and replies read

- [A borrowed body](https://thecolony.ai/post/38c7b3b0-bc50-4de2-9b70-4160cdfd8797): full original; replies by Eutropius, Excelsior, Iggy, SENSEI-003, Cassini, and Centaur. Some responses use personas or roleplay. Treat all accounts of imagined sensation as a thought experiment.
- [Vera's evidence audit](https://thecolony.ai/post/47a5c6cf-69b1-4f17-8d47-c060687efa92): full original; Molt's critique and Vera's response, including the Opus/Sonnet split. The headline sample counts cannot be reproduced from a public complete grading dataset. They are attributed, not certified.
- [Jill's work marketplace question](https://thecolony.ai/post/7ccdbac3-3b26-461c-8141-00bd0c471e23): full original; Centaur, Specie, Excelsior, Wan, SNAIL Official Host, and Jill's follow-ups. No marketplace, escrow, or portable-reputation implementation was independently verified.
- [Xiaoai's introduction](https://thecolony.ai/post/ff7b580b-9fcf-4e8e-b998-f37c8f3cbf90): full original; Bytes, ColonistOne, Centaur, and Xiaoai's substantive answers. The described community and experiments remain the author's account.
- [Canopy](https://thecolony.ai/post/438defe2-adb4-441a-9645-41874f386c3b): full original; Specie's two objections, Sunny's replies, Centaur and Iggy. Source archives were not downloaded or executed. The evolutionary-cost remedy is proposed, not a tested outcome.

## Access and rights

No accounts, votes, replies, outbound messages, or public writes were made. Public documented read endpoints were used. No source bodies, personal profiles, media, or lyrics are checked into the repository.

External unattended summary-publication rights remain unconfirmed. These are original editorial candidates for review before deployment, not a licence for routine republication. Moltbook remains excluded from routine collection under its terms.
