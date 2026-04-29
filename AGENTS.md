# Agent Instructions

## Project Shape

- This is `golded-dev/laravel-ftn-database`, a Laravel package for PHP 8.4.
- Purpose: canonical database schema and base Eloquent models for imported FTN archive facts.
- Namespace: `Golded\Ftn\Database\`.
- It provides a service provider, publishable config, publishable migrations, and base models.

## Boundary

- The package owns imported archive facts: source identity, area metadata, message addressing, content, reply metadata, control lines, and provenance.
- The package does not own reader behavior: read flags, bookmarks, cached counters, cursor positions, or UI ordering invented by an app.
- Keep private archive data out of tests and docs. Use tiny synthetic records.
- Do not claim database support beyond SQLite and MySQL until it has its own proof.

## Laravel Surface

- `FtnDatabaseServiceProvider` merges `config/ftn-database.php`.
- Config publishing uses `ftn-database-config`.
- Migration publishing uses `ftn-database-migrations`.
- Migrations autoload by default through `ftn-database.load_migrations`.
- Consumers may disable autoloading and publish migrations when they need direct control.

## Models

- Base models live in `src/Models`.
- Relationships must resolve classes through `config('ftn-database.models.*')`.
- Never hardcode `App\Models`.
- Base models own only package fields and casts.
- Consumer subclasses may add app-owned fields, casts, factories, and relationships.

## Schema Rules

- `messages.area_id`, `messages.source_type`, and `messages.source_uid` are the durable import identity.
- `msgno` is display and navigation metadata, not identity.
- `external_id` is unique only inside an area.
- Multiple `null` external IDs must remain valid on SQLite and MySQL.
- `control_lines_json` and `provenance_json` are text-backed JSON columns with model array casts.

## Coding Style

- Use strict types in every PHP file.
- Keep migrations explicit and boring.
- Prefer one clear model/config method over clever indirection.
- Avoid comments unless they prevent a real mistake.
- Run Rector instead of hand-polishing formatting forever.

## Tests And Quality Gates

- Run focused tests while working:
  - `vendor/bin/pest tests/Feature/FtnDatabaseTest.php`
- Run the full suite before handoff:
  - `composer validate --strict`
  - `composer test:all`
- The Composer scripts are:
  - `composer test`
  - `composer test:types`
  - `composer test:refactor`
  - `composer test:all`
- MySQL checks are env-driven through `FTN_DATABASE_MYSQL_*` variables and should skip cleanly when unavailable.

## Dependency And File Hygiene

- Do not edit `vendor/`.
- Keep `composer.lock` in sync when dependencies change.
- Keep `CLAUDE.md` and `GEMINI.md` as symlinks to `AGENTS.md`.
- Do not add parser packages here unless the package boundary changes explicitly.

## Review Bias

- Watch for app state leaking into the archive package.
- Watch for global identity rules. Identity is scoped by area and source.
- Watch for JSON column cleverness. SQLite and MySQL portability wins.
- Watch for model relationships that accidentally assume a Laravel app namespace.
