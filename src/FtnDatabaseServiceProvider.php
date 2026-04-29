<?php

declare(strict_types=1);

namespace Golded\Ftn\Database;

use Illuminate\Support\ServiceProvider;

class FtnDatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ftn-database.php', 'ftn-database');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/ftn-database.php' => config_path('ftn-database.php'),
        ], 'ftn-database-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'ftn-database-migrations');

        if (config('ftn-database.load_migrations', true)) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
