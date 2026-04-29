<?php

declare(strict_types=1);

use Golded\Ftn\Database\Models\Area;
use Golded\Ftn\Database\Models\Message;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

it('runs the package migration on MySQL when credentials are available', function (): void {
    $database = getenv('FTN_DATABASE_MYSQL_DATABASE');

    if ($database === false || $database === '') {
        $this->markTestSkipped('Set FTN_DATABASE_MYSQL_DATABASE to run the MySQL migration check.');
    }

    Config::set('database.connections.ftn_database_mysql', [
        'driver' => 'mysql',
        'host' => getenv('FTN_DATABASE_MYSQL_HOST') ?: '127.0.0.1',
        'port' => getenv('FTN_DATABASE_MYSQL_PORT') ?: '3306',
        'database' => $database,
        'username' => getenv('FTN_DATABASE_MYSQL_USERNAME') ?: 'root',
        'password' => getenv('FTN_DATABASE_MYSQL_PASSWORD') ?: '',
        'unix_socket' => getenv('FTN_DATABASE_MYSQL_SOCKET') ?: '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
    ]);

    Config::set('database.default', 'ftn_database_mysql');
    DB::purge('ftn_database_mysql');

    Artisan::call('migrate:fresh', [
        '--database' => 'ftn_database_mysql',
        '--path' => __DIR__.'/../../database/migrations',
        '--realpath' => true,
    ]);

    $area = Area::create(areaRecord());
    $otherArea = Area::create(areaRecord(['code' => 'OTHER']));

    Message::create(messageRecord($area, ['external_id' => null]));
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
        ->and(fn () => Message::create(messageRecord($area, [
            'source_uid' => 'jam:offset:128',
            'external_id' => '2:230/150 def',
        ])))->toThrow(QueryException::class)
        ->and(fn () => Message::create(messageRecord($area, [
            'source_uid' => 'jam:offset:1024',
            'external_id' => '2:230/150 abc',
        ])))->toThrow(QueryException::class);
});
