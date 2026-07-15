<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table): void {
            $table->string('language', 20)->nullable()->after('operating_system');
            $table->string('timezone', 80)->nullable()->after('language');
            $table->string('screen_resolution', 20)->nullable()->after('timezone');
            $table->index(['country_code', 'region', 'started_at'], 'analytics_sessions_location_idx');
            $table->index(['device_type', 'started_at'], 'analytics_sessions_device_idx');
        });

        Schema::table('platform_analytics_events', function (Blueprint $table): void {
            $table->string('screen_resolution', 20)->nullable()->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('platform_analytics_events', fn (Blueprint $table) => $table->dropColumn('screen_resolution'));
        Schema::table('analytics_sessions', function (Blueprint $table): void {
            $table->dropIndex('analytics_sessions_location_idx');
            $table->dropIndex('analytics_sessions_device_idx');
            $table->dropColumn(['language', 'timezone', 'screen_resolution']);
        });
    }
};
