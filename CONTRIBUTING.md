# Contributing

This package is narrow on purpose.

Good contributions make imported FTN archive storage more correct without dragging reader UI state into the package.

## Scope

Good fits:

- archive schema fixes
- base model relationship fixes
- SQLite, MySQL, and PostgreSQL portability fixes
- source identity and external ID behavior fixes
- docs that sharpen the archive/app boundary
- tests for real storage behavior

Usually not a fit:

- read flags, bookmarks, counters, or cursor state
- GoldED screen behavior
- parser changes
- importer orchestration
- private archive fixtures
- broad abstractions without a concrete consumer

If a change makes the package care how a reader UI behaves, it is probably in the wrong repo.

## Development Setup

```bash
composer install
```

## Quality Gates

Run the full suite before opening a pull request:

```bash
composer test:all
```

For focused work:

```bash
composer test
composer test:types
composer test:refactor
```

## Coding Style

- Use strict types.
- Keep models literal.
- Resolve app model subclasses through config.
- Do not hardcode `App\Models`.
- Do not edit `vendor/`.
- Keep `composer.lock` in sync when `composer.json` changes.

## Public API Changes

Be careful with these:

- migration columns, indexes, and constraints
- config keys
- model fillable fields and casts
- service provider migration loading
- dependency constraints

Those can be breaking changes. Say so plainly in the pull request and changelog.

## Tests

Add tests for behavior, especially around:

- duplicate `area_id`, `source_type`, and `source_uid`
- scoped `external_id` uniqueness
- multiple `null` external IDs
- configured model relationships
- text-backed JSON casts
- fresh migrations on SQLite, MySQL, and PostgreSQL

## Pull Requests

Use a clear title and explain:

- what changed
- which archive-storage behavior it fixes
- whether schema or public API changed
- which commands passed

Keep pull requests focused. Unrelated cleanup can wait.

## Security Reports

Do not report security issues in public tickets. Use the private reporting path in [SECURITY.md](SECURITY.md).
