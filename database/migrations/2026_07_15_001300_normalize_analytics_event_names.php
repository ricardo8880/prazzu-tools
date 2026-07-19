<?php

declare(strict_types=1);

use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('platform_analytics_events')) {
            return;
        }

        $resolver = new AnalyticsEventNameResolver;

        foreach ($resolver->legacyAliases() as $legacy => $canonical) {
            if ($legacy === $canonical) {
                continue;
            }

            DB::table('platform_analytics_events')
                ->where('event_name', $legacy)
                ->update(['event_name' => $canonical]);
        }
    }

    public function down(): void
    {
        // A normalização é deliberadamente irreversível: vários aliases podem
        // representar o mesmo evento canônico e não há como reconstruí-los.
    }
};
