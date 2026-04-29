# Changelog

Notable changes to `golded-dev/laravel-ftn-database`.

This project uses semantic versioning.

## 1.1.0 - 2026-04-29

### Added

- Add PostgreSQL as a supported database target.
- Add an env-driven PostgreSQL migration test.
- Add PostgreSQL migration coverage to CI.

## 1.0.0 - 2026-04-29

Initial stable release.

### Added

- Add Laravel service provider with config and migration publishing.
- Add package migration for canonical FTN archive `areas` and `messages` tables.
- Add base `Area` and `Message` models with configurable relationships.
- Add source identity and scoped external ID constraints.
- Add SQLite and env-driven MySQL migration tests.
- Add public package documentation, security policy, code of conduct, archive hygiene, and CI workflow.
