<?php

declare(strict_types=1);

namespace App\Core\Analytics\Infrastructure\Persistence;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

final class AnalyticsSchema
{
    private bool $ready = false;

    public function isReady(): bool
    {
        if ($this->ready) {
            return true;
        }

        try {
            return $this->ready = Schema::hasTable('analytics_visitors')
                && Schema::hasTable('analytics_sessions')
                && Schema::hasTable('platform_analytics_events');
        } catch (QueryException) {
            return false;
        }
    }
}
