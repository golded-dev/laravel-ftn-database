<?php

declare(strict_types=1);

use Golded\Ftn\Database\Models\Area;
use Golded\Ftn\Database\Models\Message;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

it('runs the package migration on PostgreSQL when credentials are available', function (): void {
    $database = getenv('FTN_DATABASE_POSTGRES_DATABASE');

    if ($database === false || $database === '') {
        $this->markTestSkipped('Set FTN_DATABASE_POSTGRES_DATABASE to run the PostgreSQL migration check.');
    }

    Config::set('database.connections.ftn_database_postgres', [
        'driver' => 'pgsql',
        'host' => getenv('FTN_DATABASE_POSTGRES_HOST') ?: '127.0.0.1',
        'port' => getenv('FTN_DATABASE_POSTGRES_PORT') ?: '5432',
        'database' => $database,
        'username' => getenv('FTN_DATABASE_POSTGRES_USERNAME') ?: 'postgres',
        'password' => getenv('FTN_DATABASE_POSTGRES_PASSWORD') ?: '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
    ]);

    Config::set('database.default', 'ftn_database_postgres');
    DB::purge('ftn_database_postgres');

    Artisan::call('migrate:fresh', [
        '--database' => 'ftn_database_postgres',
        '--path' => __DIR__.'/../../database/migrations',
        '--realpath' => true,
    ]);

    $area = Area::create(areaRecord());
    $otherArea = Area::create(areaRecord(['code' => 'OTHER']));

    Message::create(messageRecord($area, [
        'external_id' => null,
        'control_lines_json' => ['msgid' => '2:230/150 12345678'],
        'provenance_json' => ['sourceType' => 'jam', 'sourceOffset' => 128],
    ]));
    Message::create(messageRecord($area, [
        'source_uid' => 'jam:offset:512',
        'external_id' => null,
    ]));
    Message::create(messageRecord($area, [
        'source_uid' => 'jam:offset:768',
        'external_id' => '2:230/150 abc',
    ]));
    Message::create(messageRecord($otherArea, [
        'source_uid' => 'jam:offset:768',
        'external_id' => '2:230/150 abc',
    ]));

    expect(Message::whereNull('external_id')->count())->toBe(2)
        ->and(Message::first()?->control_lines_json)->toBe(['msgid' => '2:230/150 12345678'])
        ->and(Message::first()?->provenance_json)->toBe(['sourceType' => 'jam', 'sourceOffset' => 128])
        ->and(fn () => Message::create(messageRecord($area, [
            'source_uid' => 'jam:offset:128',
            'external_id' => '2:230/150 def',
        ])))->toThrow(QueryException::class)
        ->and(fn () => Message::create(messageRecord($area, [
            'source_uid' => 'jam:offset:1024',
            'external_id' => '2:230/150 abc',
        ])))->toThrow(QueryException::class);
});
