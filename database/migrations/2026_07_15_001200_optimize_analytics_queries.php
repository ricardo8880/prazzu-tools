<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_analytics_events', function (Blueprint $table): void {
            $table->index(['occurred_at', 'event_name'], 'analytics_events_period_event_idx');
            $table->index(['occurred_at', 'channel'], 'analytics_events_period_channel_idx');
            $table->index(['occurred_at', 'source'], 'analytics_events_period_source_idx');
            $table->index(['occurred_at', 'subject_slug'], 'analytics_events_period_subject_idx');
            $table->index(['visitor_id', 'occurred_at'], 'analytics_events_visitor_period_idx');
            $table->index(['analytics_session_id', 'occurred_at'], 'analytics_events_session_period_idx');
        });

        Schema::table('analytics_sessions', function (Blueprint $table): void {
            $table->index(['last_activity_at', 'visitor_id'], 'analytics_sessions_activity_visitor_idx');
        });
    }

    public function down(): void
    {
        Schema::table('platform_analytics_events', function (Blueprint $table): void {
            $table->dropIndex('analytics_events_period_event_idx');
            $table->dropIndex('analytics_events_period_channel_idx');
            $table->dropIndex('analytics_events_period_source_idx');
            $table->dropIndex('analytics_events_period_subject_idx');
            $table->dropIndex('analytics_events_visitor_period_idx');
            $table->dropIndex('analytics_events_session_period_idx');
        });

        Schema::table('analytics_sessions', function (Blueprint $table): void {
            $table->dropIndex('analytics_sessions_activity_visitor_idx');
        });
    }
};
