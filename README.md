# Laravel FTN Database

Laravel schema and base Eloquent models for imported FTN archive facts.

This package owns the durable facts that come from FTN message archives: areas, messages, source identity, addressing, bodies, reply metadata, control lines, and import provenance.

It does not own reader UI state. Read flags, bookmarks, cached counters, and current positions belong in the consuming app.

## Installation

```bash
composer require golded-dev/laravel-ftn-database:^1.0
```

Requires PHP 8.4+ and Laravel 13 components.

Supported database targets:

- SQLite
- MySQL
- PostgreSQL

## What It Provides

- `Golded\Ftn\Database\FtnDatabaseServiceProvider`
- `Golded\Ftn\Database\Models\Area`
- `Golded\Ftn\Database\Models\Message`
- config publishing through the `ftn-database-config` tag
- migration publishing through the `ftn-database-migrations` tag
- package migrations that load by default

## Migrations

The package creates `areas` and `messages`.

Package migrations load automatically unless the consumer disables them:

```php
'load_migrations' => false,
```

Publish migrations when the app needs to own the files directly:

```bash
php artisan vendor:publish --tag=ftn-database-migrations
```

`control_lines_json` and `provenance_json` are text-backed JSON payloads with array casts on the model. That keeps SQLite, MySQL, and PostgreSQL behavior plain.

## Config

Publish the config:

```bash
php artisan vendor:publish --tag=ftn-database-config
```

Default config:

```php
return [
    'load_migrations' => true,

    'models' => [
        'area' => Golded\Ftn\Database\Models\Area::class,
        'message' => Golded\Ftn\Database\Models\Message::class,
    ],
];
```

Apps can point relationships at subclasses:

```php
'models' => [
    'area' => App\Models\Area::class,
    'message' => App\Models\Message::class,
],
```

The base relationships resolve through config. They do not know about `App\Models`.

## Models

Use the base models directly for archive-only apps:

```php
use Golded\Ftn\Database\Models\Area;
use Golded\Ftn\Database\Models\Message;

$area = Area::create([
    'code' => 'TEST',
    'name' => 'Test Area',
    'source_type' => 'jam',
]);

Message::create([
    'area_id' => $area->id,
    'source_type' => 'jam',
    'source_uid' => 'jam:offset:256',
    'body_text' => 'Hello',
]);
```

For GoldED-style apps, subclass the models and add only app-owned state:

```php
namespace App\Models;

use Golded\Ftn\Database\Models\Message as BaseMessage;

class Message extends BaseMessage
{
    protected $fillable = [
        'area_id',
        'msgno',
        'source_type',
        'source_uid',
        'body_text',
        'is_read',
        'is_marked',
        'is_bookmarked',
        'thread_key',
    ];
}
```

Keep imported archive facts in the package columns. Keep reader behavior in the app.

## Identity

The import identity is:

- `area_id`
- `source_type`
- `source_uid`

`msgno` is display and navigation metadata. It is not identity.

`external_id` is unique only inside an area. Multiple `null` external IDs are allowed.

## Development

Install dependencies:

```bash
composer install
```

Run tests:

```bash
composer test
```

Run static analysis:

```bash
composer test:types
```

Run Rector dry-run:

```bash
composer test:refactor
```

Run everything:

```bash
composer test:all
```

The MySQL migration check is skipped unless these environment variables are set:

- `FTN_DATABASE_MYSQL_DATABASE`
- `FTN_DATABASE_MYSQL_HOST`
- `FTN_DATABASE_MYSQL_PORT`
- `FTN_DATABASE_MYSQL_USERNAME`
- `FTN_DATABASE_MYSQL_PASSWORD`

The PostgreSQL migration check is skipped unless these environment variables are set:

- `FTN_DATABASE_POSTGRES_DATABASE`
- `FTN_DATABASE_POSTGRES_HOST`
- `FTN_DATABASE_POSTGRES_PORT`
- `FTN_DATABASE_POSTGRES_USERNAME`
- `FTN_DATABASE_POSTGRES_PASSWORD`

## Versioning

This package starts at `1.0.0` and uses semantic versioning.

Breaking changes include schema changes, model fillable or cast changes that affect persisted data, service provider behavior changes, and supported Laravel or PHP version changes.

## Contributing

Contributions are welcome when they keep the archive/app boundary sharp. See [CONTRIBUTING.md](CONTRIBUTING.md).

## Security

Do not report security issues in public tickets. See [SECURITY.md](SECURITY.md).

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

Released under the MIT License. See [LICENSE](LICENSE).
