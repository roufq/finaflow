<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private const SLOW_QUERY_THRESHOLD_MS = 500;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! class_exists(\Redis::class)) {
            Config::set('cache.default', 'array');

            if (Config::get('session.driver') === 'redis') {
                Config::set('session.driver', 'file');
            }

            if (Config::get('queue.default') === 'redis') {
                Config::set('queue.default', 'database');
            }
        }

        if ($this->app->environment('production')) {
            DB::whenQueryingForLongerThan(self::SLOW_QUERY_THRESHOLD_MS, function (string $connection, QueryExecuted $event) {
                \Log::channel('daily')->warning('Slow query detected', [
                    'sql' => $event->sql,
                    'time_ms' => $event->time,
                    'connection' => $connection,
                    'bindings' => $event->bindings,
                ]);
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        Schema::defaultStringLength(191);
    }
}
