<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table): void {
            $table->foreignId('acquisition_context_id')
                ->nullable()
                ->after('campaign')
                ->constrained('acquisition_contexts')
                ->nullOnDelete();
            $table->string('acquisition_keyword')->nullable()->after('acquisition_context_id');
            $table->string('acquisition_campaign_identifier')->nullable()->after('acquisition_keyword');
            $table->string('acquisition_primary_tool_slug')->nullable()->after('acquisition_campaign_identifier');

            $table->index(['acquisition_keyword', 'started_at'], 'analytics_sessions_acquisition_keyword_started');
            $table->index(['acquisition_campaign_identifier', 'started_at'], 'analytics_sessions_acquisition_campaign_started');
        });

        Schema::table('platform_analytics_events', function (Blueprint $table): void {
            $table->foreignId('acquisition_context_id')
                ->nullable()
                ->after('campaign')
                ->constrained('acquisition_contexts')
                ->nullOnDelete();
            $table->string('acquisition_keyword')->nullable()->after('acquisition_context_id');
            $table->string('acquisition_campaign_identifier')->nullable()->after('acquisition_keyword');
            $table->string('acquisition_primary_tool_slug')->nullable()->after('acquisition_campaign_identifier');

            $table->index(['acquisition_context_id', 'occurred_at'], 'analytics_events_acquisition_context_occurred');
            $table->index(['acquisition_keyword', 'occurred_at'], 'analytics_events_acquisition_keyword_occurred');
            $table->index(['acquisition_campaign_identifier', 'occurred_at'], 'analytics_events_acquisition_campaign_occurred');
        });
    }

    public function down(): void
    {
        Schema::table('platform_analytics_events', function (Blueprint $table): void {
            $table->dropForeign(['acquisition_context_id']);
            $table->dropIndex('analytics_events_acquisition_context_occurred');
            $table->dropIndex('analytics_events_acquisition_keyword_occurred');
            $table->dropIndex('analytics_events_acquisition_campaign_occurred');
            $table->dropColumn([
                'acquisition_context_id',
                'acquisition_keyword',
                'acquisition_campaign_identifier',
                'acquisition_primary_tool_slug',
            ]);
        });

        Schema::table('analytics_sessions', function (Blueprint $table): void {
            $table->dropForeign(['acquisition_context_id']);
            $table->dropIndex('analytics_sessions_acquisition_keyword_started');
            $table->dropIndex('analytics_sessions_acquisition_campaign_started');
            $table->dropColumn([
                'acquisition_context_id',
                'acquisition_keyword',
                'acquisition_campaign_identifier',
                'acquisition_primary_tool_slug',
            ]);
        });
    }
};
