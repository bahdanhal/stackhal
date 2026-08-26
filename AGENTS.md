# AI Agent Guidelines & System Instructions — stackhal (stackhal.com)

This document provides mandatory directives for all AI coding agents interacting with the `stackhal` repository (`stackhal.com`).

---

## 1. Language Constraints (CRITICAL)

- **Strict English Requirement**: All code generated, edited, or reviewed MUST be in **English only**.
  - All identifiers (classes, methods, functions, variables, constants, properties, arguments).
  - All inline comments, block comments, docblocks (`/** ... */`), and PHPDoc tags.
  - All commit messages, PR descriptions, and markdown documentation.
- **Allowed Exceptions**:
  - Translation string files in `translations/` (e.g. `messages.pl.yaml`).
  - UI copy specifically intended for Polish routes (`/pl/...`).

---

## 2. Testing & Quality Invariants (100% Green CI Required)

Before completing any task, ensure the verification matrix passes cleanly in `stackhal`:

```bash
docker run --rm -v "$PWD:/app" -w /app/stackhal -e APP_ENV=test bahdan-landing-test vendor/bin/phpunit --fail-on-phpunit-notice
docker run --rm -v "$PWD:/app" -w /app/stackhal -e APP_ENV=test node:24-alpine sh -lc 'for test in tests/js/*.test.js; do node "$test" || exit 1; done'
docker run --rm -v "$PWD:/app" -w /app/stackhal bahdan-landing-test vendor/bin/phpstan analyse --no-progress --memory-limit=512M
docker run --rm -v "$PWD:/app" -w /app/stackhal bahdan-landing-test vendor/bin/phpcs
docker run --rm -v "$PWD:/app" -w /app/stackhal bahdan-landing-test php bin/console lint:twig templates
docker run --rm -v "$PWD:/app" -w /app/stackhal bahdan-landing-test php bin/console lint:yaml translations config
docker run --rm -v "$PWD:/app" -w /app/stackhal bahdan-landing-test composer validate --strict --no-check-publish
```

---

## 3. Application-Specific Invariants

- **Stackhal SEO audit metrics**: `total` counts unique `audit_id` runs, including failures that occur before `audit_requested` is logged. Derive run time from `requested_at`, falling back to `completed_at`; count terminal status by the final run state rather than raw event totals.
- **SSRF Guard & DNS Pinning**: All network operations must go through `SafeHttpFetcher` with `UrlGuard` to prevent SSRF and DNS rebinding attacks.
- **Spec-Driven Architecture**: All developer tool rules, schema transforms, and MCP schemas must remain synchronized with `specs/*.spec.json` and pass `SpecificationComplianceTest.php`.

