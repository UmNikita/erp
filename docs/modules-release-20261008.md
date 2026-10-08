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

## Deployed

- Git release: `69ad68a`, branch `codex/tasks-v1` (includes accounting commits).
- Target: `/opt/reklam-hub`, `https://hub.reklamynet.ru`.
- Backup: `/var/backups/hub-modules-20261008.jRzOrZ` (source + PostgreSQL dump).
- Seven task migrations applied; all 15 versions recorded.
- Four authenticated production API checks and seven page-rendering checks pass.
- Public login and task JavaScript return HTTP 200; unauthenticated task pages
  redirect to login. Existing six accounting operations retained.
- PHP/Nginx containers started with persistent restart policies.
- Daily duty timer: `reklam-hub-duties.timer`, 00:05 Europe/Moscow.
- Existing server Git checkout and runtime settings preserved; release sources
  deployed from Git archive. Main branch has not been merged automatically.

## Rollback

Do not blindly restore the database: new work may have been added since release.
The backup directory contains `source.tgz`, `database.dump`, and Git status before
deployment. Restore code first with services stopped; reverse task migrations or
restore the database only after explicitly accepting loss of post-release data.
Server environment/JWT/infrastructure files were not overwritten by this release.
