<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table): void {
            $table->string('vertical_slug')->nullable()->after('campaign');
            $table->index(['vertical_slug', 'started_at'], 'analytics_sessions_vertical_started');
        });

        Schema::table('platform_analytics_events', function (Blueprint $table): void {
            $table->string('vertical_slug')->nullable()->after('campaign');
            $table->index(['vertical_slug', 'occurred_at'], 'analytics_events_vertical_occurred');
        });
    }

    public function down(): void
    {
        Schema::table('platform_analytics_events', function (Blueprint $table): void {
            $table->dropIndex('analytics_events_vertical_occurred');
            $table->dropColumn('vertical_slug');
        });

        Schema::table('analytics_sessions', function (Blueprint $table): void {
            $table->dropIndex('analytics_sessions_vertical_started');
            $table->dropColumn('vertical_slug');
        });
    }
};
