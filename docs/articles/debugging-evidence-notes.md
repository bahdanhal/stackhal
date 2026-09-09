# Evidence notes: I Think Debugging Is Almost Dead

## Current article: September 9 editorial rebuild

The article was rebuilt after the author rejected marketplace samples and general programming activity as evidence about debugging. It now leads with September customer accounts about actual diagnosis and uses an August disclosure ledger for the distinction between reports and fixes. The earlier Upwork/Reddit research is retained below as research history, not evidence supporting the article's thesis.

### September 2026: Fable 5.1 customer accounts

Primary page inspected September 9:

https://www.anthropic.com/claude-fable-and-mythos-5-1

The page visibly labels the release SEPTEMBER 2026. No exact publication day was established; do not relabel it August 31 from an earlier search result, infer a September 9 release, or assume the customer experiments occurred on the publication date.

Millennium: the page attributes the account to Damien, Senior Portfolio Manager. He describes an extremely rare crash, approximately one in a million runs, unexplained within the team for four to five years. Earlier models he tried, including Fable 5, missed it. Fable 5.1 disassembled an external vendor library, matched it against a core dump and traced the crash to a bug in that library. The account says the time needed for such analysis would have been hard to justify. It does NOT establish four to five years of continuous engineering effort, the duration of the model's run, a publicly inspectable reproducer, a submitted vendor patch, or a shipped fix.

Datadog: Daniel Shan, Staff Engineer, describes incident-investigation evaluations using real production incidents and the Bits Investigation agent, comparing outputs against root causes identified by engineers. He says Fable 5.1 diagnosed the most complex incidents tested and showed stronger reasoning than Opus 5. The quote gives no sample size or numerical success rate. This is retrospective evaluation evidence, not a claim that the model autonomously resolved live incidents or that all current production problems are solvable.

These are named customer accounts curated and published by the model supplier. They are stronger matches for the article's subject than general coding scores, but are not independent audited studies. The article says so and does not present them as a measurement of the fraction of debugging already automated.

Other customer quotes inspected but not used: Red Hat reported correct root-cause identification on every broken build tested without a disclosed denominator; Ramp described a long-running task that pulled logs for an unowned alert and prescribed a fix. These were omitted to avoid turning the essay into a collection of launch testimonials.

### August 26: coordinated vulnerability disclosure dashboard

Primary source inspected September 9:

https://red.anthropic.com/2026/cvd/

The visible snapshot is dated August 26, 2026, 11:55 PT, revision 32. It aggregates work across Mythos Preview, other Mythos-class models and other Claude models. Its default discovery window is November 1, 2025 through August 26, 2026. It is NOT an August-only cohort or a Fable 5.1 benchmark.

Visible aggregate counts, with no narrowed filters:

| Stage | Count |
| --- | ---: |
| Candidate findings | 26,153 |
| Reviewed by external security firms | 5,008 |
| Confirmed valid by those firms | 4,576 |
| Reported through the reviewed route | 1,022 |
| Reported directly at maintainers' request | 1,278 |
| Total reported to maintainers | 2,300 |
| Projects receiving reports | 392 |
| Acknowledged by maintainers | 1,815 |
| Known patched upstream | 421 |
| CVE/GHSA identifiers issued | 462 |

The dashboard's 91.4% true-positive figure is 4,576 / 5,008 externally reviewed candidates. It includes findings that may be duplicates or outside a maintainer's threat model; it is not an independently established precision for all candidates. Directly reported findings can include false positives. Acknowledgement is not confirmation. Identifiers can overlap for the same finding, so 462 is not a count of distinct patched bugs. Known patched upstream means maintainers created and released fixes; it does not guarantee deployment by users or mean the model authored those fixes.

The source explicitly describes independent human triage and review as the rate-limiting step in reporting findings. This supports a change in where the process is constrained, not a claim that all human work has disappeared. The article uses 2,300 reports and 421 known fixes, clearly distinguished, rather than the much larger candidate count.

Machine-readable payload linked by the page: https://red.anthropic.com/2026/cvd/data/payload.json

Visible SHA3-512 manifest checksum: f89b26dc49393d86613a9687a7b6e152d60e1b8de4066640707ded608fd51f5bd354b29ac790490fc0fc6ee3b01dcf7ae11617318a95129c9cbc0ae353b6d281

The rendered dashboard was inspected. The payload and checksum were not independently downloaded/validated.

### May 7: historical technical context, not current capability ceiling

https://hacks.mozilla.org/2026/05/behind-the-scenes-hardening-firefox/

Mozilla's own engineering report describes complex examples, including a race across IPC and a 15-year-old HTML legend-element bug involving distant browser subsystems. It explains a test-running agent harness built on existing fuzzing infrastructure, plus deduplication, triage, patching and release work. It says model upgrades improved discovery, test-case production and explanation. The article uses this to explain how an investigation environment can improve with newer models; it does not present May results as September capability measurements.

The report credits over 100 contributors across patch writing/review, pipeline construction, triage, testing and releases. Do not turn the automated discovery evidence into a claim of fully autonomous repair or zero human effort.

Earlier first-party reports inspected, now replaced in the article by the more detailed May account:

- https://blog.mozilla.org/en/firefox/hardening-firefox-anthropic-red-team/ (March 6)
- https://blog.mozilla.org/en/firefox/privacy-security/ai-security-zero-day-vulnerabilities/ (April 21)
- https://www.anthropic.com/news/mozilla-firefox-security (March 6)

### Other inspected sources not used

Meta's December 19, 2025 DrP report describes a mature automated investigation platform with engineer-authored analyzers and future AI-native ambitions. Its 50,000 daily analyses must not be represented as 50,000 autonomous LLM diagnoses.

The Singapore EDB SonarQube Remediation Agent article was dated May 21 despite a recent search-result timestamp and September site footer. It was rejected as September evidence.

OpenAI's current GPT-6 Astra documentation was inspected via official docs. It supplies current product capability descriptions, not a public debugging-specific success rate, so it was not used as numerical evidence. Sources: https://developers.openai.com/api/docs/guides/latest-model and https://learn.chatgpt.com/docs/whats-new

### Boundaries of the essay

The title and economic argument are the author's judgment, not measured market claims. Payment, race-test and business-reporting illustrations are hypothetical rather than invented personal case histories. The article does not assert that sufficient context guarantees a correct diagnosis or that testing removes all need for review.

## Earlier research, removed from the article

The following observations were collected September 8. They are retained to document the research and corrections, not used to establish that complex debugging is automated.

## Upwork snapshot

Authenticated job search, ordered by Most Recent, ten results per page, with no additional category, budget, experience, or location filters selected. Search results can change during pagination and can depend on account visibility. Counts are keyword matches, not counts of distinct troubleshooting contracts or completed hires.

- `debugging`: experience facets showed 96 entry, 1,538 intermediate, and 1,200 expert matches, totaling 2,834. Job-type facets showed 1,929 hourly and 905 fixed-price matches, also totaling 2,834. Pagination showed 284 pages; page 284 contained four listings.
- `"bug fix"`: pagination showed 50 pages. The first and last pages each contained ten listings. This does not establish a platform-wide 500-result ceiling; the earlier draft inferred one without sufficient evidence.
- Broad search: https://www.upwork.com/nx/search/jobs/?q=debugging&sort=recency
- Quoted search: https://www.upwork.com/nx/search/jobs/?q=%22bug%20fix%22&sort=recency

Manual classification of the ten newest quoted-search results:

| Listing | Classification from the visible description |
| --- | --- |
| Fix Bugs and Add New Features to Existing Mobile App | Explicit repairs plus new features; not standalone troubleshooting |
| GA4 / Google Tag Manager Tracking Audit & Fix | Investigation and repair of an existing tracking system |
| Bubble.io Developer for App Bug Fixes | Three named repairs to an existing app |
| Build a Business Website & Update Landing Pages | New website and updates |
| Google Search Console, Sitemap & Indexing Optimization | Insufficient description to classify as debugging |
| Tabula Rasa Godot Project | General development help; no specific defect described |
| Shopify Developer Needed to Build an Ecommerce Website from Existing Custom Designs | New build with subsequent bug-fix support |
| WordPress Developer to Launch HR Consulting Site - WooCommerce, Stripe & Forms | Implementation and integration of an existing static design |
| Back-End WordPress Developer | Ongoing development role including fixes |
| Front-End WordPress Developer | Ongoing development role including fixes |

The three explicit repair requests therefore include two repair-focused assignments and one mixed assignment. They must not be described as three standalone debugging jobs.

| Request | Advertised fixed budget, USD | Displayed age | Displayed proposals | Job ID |
| --- | ---: | --- | --- | --- |
| Flutter repairs and features | 50 | 30 minutes | 10 to 15 | ~022097358310190387717 |
| GA4/GTM audit and fix | 60 | 41 minutes | 10 to 15 | ~022097355697224104229 |
| Bubble repairs | 100 | 50 minutes | 20 to 50 | ~022097353654688169022 |

Budgets are client advertisements, not accepted prices, earnings, or evidence of AI price pressure. Proposal ranges are not exact applicant counts. The GA4 listing explicitly stated that GTM account access was still being recovered. No inference about whether the client had tried AI is supported.

### Follow-up: job activity versus client hiring history

The same three job detail pages were inspected later on September 8. The mobile listing displayed an age of 49 minutes; GA4 and Bubble displayed approximately one hour. This is a same-day follow-up, not a completed hiring-outcome survey.

| Listing | Proposals at follow-up | Interviews | Job-specific hires | Client historical hire rate | Client historical hires | Client total spent, USD |
| --- | --- | ---: | --- | ---: | ---: | ---: |
| Mobile app, $50 advertised | 20 to 50 | 0 | No Hires field displayed | 100% | 1 | 10 |
| GA4/GTM, $60 advertised | 10 to 15 | 0 | No Hires field displayed | 94% | 465 | 204K |
| Bubble, $100 advertised | 20 to 50 | 0 | No Hires field displayed | 50% | 3 | 131 |

All three also showed zero invitations sent and zero unanswered invitations. An absent job-specific Hires field is recorded as absent, not as a verified eventual zero. Interviews are a separate activity measure, not paid engagements. Client hire rates are the platform's displayed historical percentages, not conversion rates calculated from these three jobs; their denominators and update timing were not independently established. The mobile client's sidebar simultaneously showed one job posted and two open jobs, so no derived rate should be calculated from those displayed totals.

The mobile client's prior listing, https://www.upwork.com/jobs/~022081719164905538301/, displayed `Hires: 1`, 10 to 15 proposals, zero interviews and an age of two months. Its advertised budget was $10. The client's completed-contract history separately displayed a $10 fixed-price payment for August 2026. Its description requested changing a premium-plan label to a free-plan label; it is evidence of an earlier paid contract, not evidence of a complex debugging engagement. The prior job was selected through client history, so it must not be pooled with the three fresh jobs as a random hiring sample.

None of the three inspected current descriptions explicitly requested an unpaid trial or a completed fix as an application sample. This does not establish whether unpaid work was solicited privately. No prevalence estimate for free-work solicitation, abandoned posts, completed hires, accepted prices, or AI-related price pressure can be obtained from this sample. A hiring-outcome study would require a defined cohort followed over time, retaining closed and removed listings and distinguishing no hire from unobservable outcomes.

### Older Upwork follow-up, September 8

Selection rule: retain the quoted `"bug fix"` search, Most Recent order, no additional filters. Pagination now displayed 51 pages; page 51 held three listings, none explicitly repair-focused. Inspect all ten results on the last full page (50), then open every listing whose description explicitly included repairs or troubleshooting. Four qualified, at positions 1, 5, 6 and 9; none were selected based on hire counts.

Search URL: https://www.upwork.com/nx/search/jobs/?q=%22bug%20fix%22&sort=recency&page=50

| Listing | Classification | Detail-page age | Advertised budget/rate, USD | Proposals | Interviews | Job-specific hires |
| --- | --- | --- | --- | --- | ---: | --- |
| Mobile Game Developer: Multiplayer Integration & Bug Fixes for Pickleball Game | Repairs plus substantial new multiplayer development | 2 months | 15-32/hour | 10 to 15 | 5 | Not displayed |
| Part-Time WordPress/WooCommerce Developer | Ongoing maintenance, troubleshooting and improvements | 2 months | 70-100/hour | 15 to 20 | 5 | 1 |
| Shopify Website Operations Manager (Part-Time, Ongoing) | Ongoing operations, troubleshooting and improvements | 2 months | 15-25/hour | 20 to 50 | 24 | Not displayed |
| Full Stack Developer for SaaS Bug Fixing | Repair-focused assignment | 3 months | 50 fixed | 20 to 50 | 32 | 5 |

Job URLs, in table order:

- https://www.upwork.com/jobs/~022080194170310436777/
- https://www.upwork.com/jobs/~022075704620144188561/
- https://www.upwork.com/jobs/~022074908818424690472/
- https://www.upwork.com/jobs/~022072097968431373752/

The SaaS search card said two months while its detail page said three; use the detail page's displayed age and do not infer a precise posting date. The other six page-50 results were an ENT-practice website build, Flutter agency staffing, general Webflow development, a WordPress one-page website build, a print-on-demand store build, and a baseball coaching app build.

The SaaS client's recent history contained three completed contracts titled Full Stack Developer for SaaS Bug Fixing, each linking to exactly job ID ~022072097968431373752. Displayed fixed-price amounts were $50, $100 and $50, with July-August 2026 dates: $200 in those three completed-history entries. This is not a claim that $200 is the lifetime value of all five hires. One feedback entry described the client becoming unresponsive after two milestones; that is one freelancer's account, not a prevalence estimate for unpaid work.

Two of four inspected older repair-related listings displayed hires, six hires in total. Two omitted the Hires field. Do not turn this into a platform-wide 50% conversion rate or treat absent fields as verified zero outcomes. Search-visible older listings exclude some closed/removed jobs, and only one of these four is narrowly repair-focused. Interview and invitation numbers can exceed or interact with proposal ranges; do not derive exact applicant counts or success probabilities. None explicitly asked for an unpaid trial. The Shopify role explicitly proposed a paid technical audit.

## Published freelance research

Demirci, Hannane, and Zhu, *Who Is AI Replacing? The Impact of Generative AI on Online Freelancing Platforms*, Management Science 71(10), 8097-8108. Published online January 24, 2025. Publisher abstract inspected directly:

https://pubsonline.informs.org/doi/abs/10.1287/mnsc.2024.05420

The abstract reports 21% fewer posts for automation-prone writing and coding work relative to manual-intensive work within eight months of ChatGPT's introduction. Remaining jobs were more complex and offered higher pay. It does not isolate debugging, measure September 2026 agents, or name the platform in the abstract. Do not label this an Upwork-specific debugging estimate.

Teutloff and colleagues, *Winners and Losers of Generative AI: Early Evidence of Shifts in Freelancer Demand*, Journal of Economic Behavior & Organization, 2025. Research institution summary inspected directly:

https://www.etla.fi/en/latest/winners-and-losers-of-generative-ai-in-the-freelance-job-market-etla-participated-in-an-international-research-project/

More than three million postings, January 2021 through September 2023. The summary reports increased net demand, almost tripled demand for chatbot and natural language processing jobs, and different effects across skills and experience levels. This prevents interpreting selective declines as a collapse of the whole freelance market.

## Stack Overflow

https://data.stackexchange.com/stackoverflow/query/1882532/questions-per-month

The query displayed cached results during the research session. A fresh execution requested a CAPTCHA and was not completed. The existing result grid was inspected through normal scrolling; no CAPTCHA was solved. The cache's precise refresh time was not established. Values below are the displayed snapshot inspected September 8, not a claim about a live production database query.

Query: count `PostsWithDeleted` with `PostTypeId = 1`, grouped by the month of `CreationDate`.

| Month | Displayed questions |
| --- | ---: |
| August 2025 | 9,986 |
| August 2026 | 2,528 |

Calculation: `(9986 - 2528) / 9986 * 100 = 74.68%`, rounded to 74.7%. Both are complete calendar months; September 2026 is excluded. Counts include deleted posts and must not be mixed with surviving-question counts. SEDE data can lag and subsequently change. Question counts do not establish AI causation or successful resolution of the missing questions.

Gergely Orosz's May 2025 commentary was inspected directly and links to this query. It notes that the decline began before ChatGPT:

https://blog.pragmaticengineer.com/stack-overflow-is-almost-dead/

## Reddit: direct historical archive verification

The standalone weekly visitor counts and single-keyword search counts were discarded. A later pass in the same September 8 research session found a usable historical archive, superseding the earlier statement that no comparable series had been located.

Source: Arctic Shift public API. Documentation: https://github.com/ArthurHeitmann/arctic_shift/tree/master/api

Collection documentation: https://github.com/ArthurHeitmann/arctic_shift/blob/master/file_content_explanations.md

Reproducible aggregate responses, daily cross-checks and all monthly series are preserved in [debugging-reddit-archive-snapshot.json](debugging-reddit-archive-snapshot.json). These contain aggregate counts only, no usernames, post bodies or personal information.

### Primary comparison: direct counts in equal calendar months

| Community | August 2022 | August 2026 | Decline |
| --- | ---: | ---: | ---: |
| r/learnprogramming | 5,607 | 1,910 | 65.9% |
| r/programminghelp | 140 | 44 | 68.6% |
| r/AskProgramming | 1,035 | 275 | 73.4% |

Endpoint: `/api/posts/search/aggregate`, with `aggregate=subreddit`, each named `subreddit`, and explicit Unix-second date bounds.

- August 2022: `after=1659312000&before=1661990400`
- August 2026: `after=1785542400&before=1788220800`
- Example 2022: https://arctic-shift.photon-reddit.com/api/posts/search/aggregate?aggregate=subreddit&subreddit=learnprogramming&after=1659312000&before=1661990400
- Example 2026: https://arctic-shift.photon-reddit.com/api/posts/search/aggregate?aggregate=subreddit&subreddit=learnprogramming&after=1785542400&before=1788220800

Calculation: `100 * (1 - August2026 / August2022)`, rounded to one decimal place. These count archived submissions of all kinds in help-oriented communities. They are not visitors, unique questioners, classified debugging requests, resolved issues or agent success rates.

### Cross-checks and longer windows

The precomputed monthly endpoint `/api/time_series` was queried for `r/<subreddit>/posts/count`, `precision=month`, January 2022 through August 2026. Its August totals were 5,608/1,911 for learnprogramming, 140/44 for programminghelp, and 1,037/275 for AskProgramming. These differ from direct record aggregates by zero to two posts. The API warns that the time series may be imperfect and may take hours or days to update; the precise cause of these differences was not established. The article quotes direct counts for August and does not silently treat the two endpoints as identical.

Daily direct-record aggregates for learnprogramming returned 31 nonempty buckets in both August windows, summing to 5,607 and 1,910. Bucket labels used 22:00 UTC boundaries; a separate direct aggregate with Unix-second UTC month bounds returned the same totals, avoiding an assumption that those displayed bucket labels define UTC days.

| Community | Jan-Aug 2022 monthly-series total | Jan-Aug 2026 monthly-series total | Decline |
| --- | ---: | ---: | ---: |
| r/learnprogramming | 45,494 | 19,310 | 57.6% |
| r/programminghelp | 1,344 | 402 | 70.1% |
| r/AskProgramming | 7,898 | 3,216 | 59.3% |

This broader comparison reduces dependence on a single month; it is an internal sensitivity check, not an independent data source. As a contextual check, the archive's all-Reddit monthly series increased from 272,248,401 to 361,232,083 posts across those January-August windows (+32.7%), while r/programming fell from 22,647 to 13,609 (-39.9%). These are not matched causal controls and cannot establish stable coverage in any particular subreddit.

### Limits and historical research

The archive excludes private and quarantined communities. Its data documentation distinguishes older Pushshift dumps through March 2023 from the later collection system, and describes subsequent changes to re-retrieval and metadata. Both comparison years therefore do not have an independently verified identical capture process. Deleted/removed material and moderation changes can affect coverage or the composition of observed activity. The agreement of two API endpoints from the same archive does not independently validate archive completeness.

Defensible conclusion: these archive records show a large decline in recorded posting in the three selected help communities. They do not prove an equivalent decline in all Reddit troubleshooting, or identify AI as the cause. The communities were selected for their explicit programming-help purpose, not randomly; the two originally discussed communities were retained and AskProgramming added as an adjacent help community.

Gordon Burtch, Dokyun Lee and Zhichen Chen, *The consequences of generative AI for online knowledge communities*, Scientific Reports 14, 10413 (May 6, 2024): https://pmc.ncbi.nlm.nih.gov/articles/PMC11074245/

The study compared developer-community posting around ChatGPT's release with a corresponding year-earlier window, using data from October 2021 through March 2023. It found no detectable Reddit decline in its studied communities during that early period. This does not conflict with the possibility of later declines, and the archive comparison is not a replication of the paper's causal design or exact community sample.

## Editorial boundary

The missing-order example is explicitly hypothetical. The thesis that complex debugging is increasingly delegated to agents is the author's position, not a finding attributed to these studies. The article preserves verification and unresolved context as real work, without claiming that sufficient data guarantees agent success.
