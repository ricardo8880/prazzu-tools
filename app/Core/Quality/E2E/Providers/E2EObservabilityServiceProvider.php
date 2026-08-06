<?php

declare(strict_types=1);

namespace App\Core\Quality\E2E\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

final class E2EObservabilityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->environment('e2e') || ! (bool) config('e2e_observability.enabled', false)) {
            return;
        }

        $threshold = (float) config('e2e_observability.slow_query_ms', 250);

        DB::listen(static function (QueryExecuted $query) use ($threshold): void {
            if ($query->time < $threshold) {
                return;
            }

            Log::channel('e2e')->warning('e2e.database.slow_query', [
                'connection' => $query->connectionName,
                'duration_ms' => $query->time,
                'sql' => $query->sql,
                'bindings_count' => count($query->bindings),
            ]);
        });
    }
}
