<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260908000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Publish the field note on complex debugging and agent investigations';
    }

    public function up(Schema $schema): void
    {
        $contentHtml = <<<'HTML'
<p class="article-lead">I enjoyed debugging, including the kind that ate an afternoon and made everything else late. There was a particular pleasure in finally understanding a failure that had seemed impossible, then going back through the evidence and seeing that it had been trying to tell you the same thing all along. I could resent the time it took and still want to be the person who figured it out. That is probably why I have been reluctant to admit how much less sense it makes to keep doing it myself.</p>

<p>By September 2026, I think debugging is almost dead as a craft that routinely needs to be performed by hand. I mean the difficult debugging too, not just looking up an error message or spotting a missing null check. The work increasingly consists of getting an agent into a position where it can investigate: the right version of the code, the actual state of the system, a way to reproduce the failure, and permission to run useful experiments. Having done that, I no longer see technical complexity alone as a good reason to take the investigation back.</p>

<p>The comfortable prediction was that AI would take the easy bugs and leave experienced engineers with the interesting ones. It fitted how we valued ourselves, so it was an easy prediction to like. But a bug does not acquire some protection against automation because it involves several services, a race condition, or code that takes a long time to understand. Those things make the investigation expensive. Whether a person must do it is a separate question.</p>

<p>One account in <a href="https://www.anthropic.com/claude-fable-and-mythos-5-1">Anthropic's September launch report for Claude Fable 5.1</a> gets unusually close to that question. A senior portfolio manager at Millennium describes a crash occurring roughly once in a million runs that had remained unexplained for four to five years. Earlier models, including Fable 5, had missed it. According to his account, Fable 5.1 disassembled an external vendor's library, matched it against a core dump and traced the crash to a bug in that library. His final observation was that the time needed to conduct that analysis would have been hard to justify.</p>

<p>That is a much more interesting change than being able to generate a plausible patch quickly. It was a long-unexplained failure whose investigation, by the customer's own account, was difficult to justify spending time on. The agent was able to work from the evidence of the crash into code the team did not own. Nothing in the account suggests that somebody had first reduced it to a neat little programming exercise. It describes the sort of investigation that was supposed to remain our advantage.</p>

<p>The same September report includes <a href="https://www.anthropic.com/claude-fable-and-mythos-5-1">Datadog's account of testing Fable 5.1 on real production incidents</a>. Its team compared the root causes produced by its investigation agent with those identified by engineers. Staff engineer Daniel Shan says the model successfully diagnosed the most complex incidents they had tested. These are customer accounts selected for a supplier's launch announcement, not independently published experiments, and neither gives us a general debugging success rate. The Millennium account establishes a reported diagnosis, not a publicly verified shipped fix. I still think they are substantially more relevant than assuming that whatever a model struggled with in March remains difficult for September's models.</p>

<p>There is also a more inspectable record of actual defects. In its <a href="https://red.anthropic.com/2026/cvd/">August 26 disclosure dashboard</a>, Anthropic reported 2,300 vulnerabilities submitted to maintainers across 392 open-source projects, with 421 known to have been patched upstream. Those are different stages of work: I would not count 2,300 reports as 2,300 completed repairs. The figures cover a program using multiple Claude models, not a score for Fable 5.1. What interests me is that the dashboard identifies independent human triage and review as the rate-limiting step in reporting findings. The people have plenty to do, but generating the next credible investigation is no longer necessarily what they are waiting for.</p>

<p>The Firefox work from earlier in the year helps explain how we got here, rather than defining the current limit. In <a href="https://hacks.mozilla.org/2026/05/behind-the-scenes-hardening-firefox/">Mozilla's May technical write-up</a>, the examples included an inter-process race and a 15-year-old bug requiring interactions between distant parts of the browser. Its engineers described a system that let models create and run test cases to test their own theories, built on the project's existing fuzzing infrastructure. They also said that model upgrades improved the whole process: finding potential bugs, producing demonstrations and explaining what had gone wrong. The useful investment was a working investigation environment that could take advantage of better models as they arrived.</p>

<p>That last part matters more to me than another leaderboard. An agent with a reliable way to check its work can reject its own bad ideas and keep investigating without waiting for a person to inspect every attempt. The original failure must stop occurring, but the rest of the program must still do its job; a patch that passes one check by breaking the other has achieved nothing. Finding an explanation, trying a change and checking the result was much of the debugging I enjoyed. There is less of that loop left outside the agent than the phrase "AI writes code" suggests.</p>

<p>This also changes how I interpret an agent getting stuck. It may have reached the limit of its reasoning, but I would want to rule out a much more ordinary problem first: it is investigating a different system from the one that failed. The repository says what the application can do; it does not necessarily say which version ran, which configuration loaded, what a dependency returned, or which records existed at the time. A human joining an unfamiliar incident has the same problem, except we tend to count the time spent resolving it as part of that person's debugging skill.</p>

<p>Take a hypothetical payment that succeeds without creating an order. The application code looks reasonable, the callback returned a successful response, and a local test passes. None of that establishes what happened to the customer's payment. The investigation needs the deployed version, the callback payload, the order of retries, the queue's behavior and enough state to connect the events. Once those are available, an agent can follow a theory across the services, write a test and try to disprove it. Before they are available, asking it to think harder may simply produce a more elaborate explanation of the wrong circumstances.</p>

<p>The same applies to an intermittent race. "It only happens once a week" describes how often the conditions occur in production, not how often they can occur in a controlled test. Someone needs to work out where two workers can be paused to force the relevant ordering, and that someone can increasingly be an agent. It can read the code, propose the ordering, add the instrumentation and build the reproducer. I do not think we can preserve complex debugging as a human specialty by assigning all those steps to the human in our description of the task.</p>

<p>Gathering evidence is therefore a more demanding job than preparing one enormous prompt, but it is also a less manual one than it first sounds. Often the useful contribution is a read-only route to the logs, a test environment that behaves like production, or a query that preserves the link between events without exposing customer details. The agent can then collect what it needs as the investigation develops. If it has to stop after every useful discovery so that somebody can copy another screen into the conversation, the limits of that arrangement should not be mistaken for the limits of its ability to debug.</p>

<p>There is an awkward consequence here for how we judge difficult work. A subtle memory bug in a well-instrumented test environment may now be easier to delegate than a mundane reporting error whose definition exists only in somebody's head. An agent might be perfectly capable of tracing the calculation while having no way to know that the business considers refunded orders part of last month's revenue. The developer's value in that case is getting the missing fact into the investigation. It would be strange to describe the calculation as beyond AI simply because nobody told it what the answer was supposed to mean.</p>

<p>I think this is also why the economics of troubleshooting will change more deeply than the number of advertised bug-fix jobs can tell us. The decision to pay someone often happens after an internal attempt has failed. If an agent can carry that first attempt much further, there are fewer reasons to hand over a contained problem to another developer, explain the system again, arrange access and wait. A repair request can still be posted and a contractor can still be hired without disproving that change. What matters is how much work reaches that point, and how much of the contractor's effort goes into the investigation itself rather than getting the system into an investigable state.</p>

<p>That may result in more bugs being investigated, not fewer. We tolerate odd failures when the cost of understanding them looks larger than the cost of living with them, and the Millennium account is an example of why that calculation can change. An agent makes it possible to spend effort on a problem without occupying an engineer for the whole investigation. There will still be paid work in getting access, establishing what correct behavior means, checking the result and taking responsibility for the change. I expect less value in selling another pair of eyes to trace through a contained failure. The amount of debugging being done could grow while the need to perform it by hand shrinks.</p>

<p>Experience remains useful throughout this. Knowing that a green test does not cover the failure, that a timestamp is misleading, or that the apparent fix merely drops the troublesome request can save a great deal of wasted effort. It also takes judgment to decide which experiments are safe and when there is enough evidence to release a change. None of that requires me to assume I should personally perform all the reasoning between the first symptom and the patch. The agent can do substantial work while I remain responsible for what we let it change.</p>

<p>I think that last distinction is going to be uncomfortable for engineers who liked debugging as much as I did. Being responsible for a result used to be closely tied to being the person who discovered the explanation, and there was real satisfaction in that. Now I can still care about getting the explanation right without being the best use of time for finding it. When the next difficult bug arrives, I expect to spend more of my effort making it possible for the agent to investigate, even if part of me would rather close the chat and enjoy working it out.</p>

<p class="article-sources">Written September 9, 2026. The current customer accounts are from Anthropic's September 2026 launch report; the disclosure totals are the August 26 snapshot inspected on September 9. Mozilla's May report supplies historical technical context, not a September capability estimate. The payment and reporting examples are hypothetical. The economic argument is the author's judgment, not a measured market trend.</p>
HTML;

        $publishedAt = new \DateTimeImmutable('2026-09-09 00:00:00+00:00');

        $this->addSql(
            'INSERT INTO blog_articles (slug, title, description, category, read_time_minutes, published_at, updated_at, content_html, cta_label, cta_path, visual_class, visual_lines, how_to_steps, locale, alternate_slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                'debugging-is-almost-dead-in-2026',
                'I Think Debugging Is Almost Dead',
                'The difficult bugs are not protected from AI. What increasingly matters is giving an agent the evidence, access and tests it needs to investigate them.',
                'AI engineering',
                10,
                $publishedAt,
                $publishedAt,
                $contentHtml,
                'Open AI Studio Local File Sync',
                '/ai-studio-local-file-sync',
                'terminal-card',
                ['The code is available', 'The production evidence is not', 'Build the case file'],
                [],
                'en',
                '',
            ],
            [
                Types::STRING,
                Types::STRING,
                Types::TEXT,
                Types::STRING,
                Types::SMALLINT,
                Types::DATETIMETZ_IMMUTABLE,
                Types::DATETIMETZ_IMMUTABLE,
                Types::TEXT,
                Types::STRING,
                Types::STRING,
                Types::STRING,
                Types::JSON,
                Types::JSON,
                Types::STRING,
                Types::STRING,
            ]
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'DELETE FROM blog_articles WHERE slug = ? AND locale = ?',
            ['debugging-is-almost-dead-in-2026', 'en'],
            [Types::STRING, Types::STRING]
        );
    }
}

// phpcs:enable Generic.Files.LineLength.TooLong
