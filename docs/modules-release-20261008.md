# Module release — 2026-10-08

Priority: tasks, then accounting. Website project is out of scope.

## Verified

- Production Vite build: CRM, accounting, tasks.
- Tasks and accounting TypeScript checks.
- All PHP files linted on PHP 8.4.25.
- Symfony container and module Twig templates.
- All 15 migrations applied successfully on isolated PostgreSQL 16.
- Doctrine entity mappings after correcting User inverse relations.
- 39 API assertions on isolated data: module permissions, shared tasks,
  completion credited to all owners, discussions, private tasks, invalid dates,
  overdue reason/rescheduling, private manager feedback, recurring night duties,
  idempotent generation, accounting allocations, over-allocation rejection,
  removal and restoration.
- PHPUnit completion snapshot regression: 1 test, 6 assertions.

## Fixes

- Reject impossible task deadlines instead of silently normalizing dates.
- API permission denial returns JSON 403 rather than an HTML redirect.
- Correct inverse mappings for user messages and email logs (no schema change).

## Reproduction

Use an isolated container network, a fresh database named `hub_validation`,
and `scripts/validation.env` copied to `.env`. Install Composer dependencies,
then run `sh scripts/validate-modules.sh`. The smoke script refuses other
database names. Do not run it on live data.

## Deployment precautions

- Preserve server-specific `.env`, JWT keys, Docker/Nginx configuration and
  application storage. Existing server source changes must not be discarded.
- Back up live database and source before applying the seven task migrations.
- Do not run `doctrine:schema:update --force`: old unrelated schema differences
  are not part of this module release.
- Schedule `app:tasks:materialize-duties` daily. It also runs on bootstrap;
  PostgreSQL advisory locking prevents duplicate concurrent generation.
- Verify login, `/tasks`, `/tasks/admin`, `/accounting`, and API permission checks.
- No attachments, CRM cross-module automation, or statutory accounting added.
