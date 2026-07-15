<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_visitors', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('first_seen_at');
            $table->dateTime('last_seen_at');
            $table->string('first_source', 120)->nullable();
            $table->string('last_source', 120)->nullable();
            $table->string('first_referrer', 2048)->nullable();
            $table->string('last_referrer', 2048)->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('first_seen_at');
            $table->index('last_seen_at');
        });

        Schema::create('analytics_sessions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('visitor_id');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('started_at');
            $table->dateTime('last_activity_at');
            $table->dateTime('ended_at')->nullable();
            $table->string('landing_url', 2048)->nullable();
            $table->string('landing_path', 2048)->nullable();
            $table->string('referrer', 2048)->nullable();
            $table->string('source', 120)->nullable();
            $table->string('medium', 120)->nullable();
            $table->string('campaign', 255)->nullable();
            $table->string('device_type', 30)->nullable();
            $table->string('browser', 80)->nullable();
            $table->string('operating_system', 80)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('city', 160)->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->foreign('visitor_id')->references('id')->on('analytics_visitors')->cascadeOnDelete();
            $table->index(['visitor_id', 'started_at']);
            $table->index(['user_id', 'started_at']);
            $table->index('last_activity_at');
            $table->index(['source', 'started_at']);
        });

        Schema::table('platform_analytics_events', function (Blueprint $table): void {
            $table->uuid('event_id')->nullable()->after('id');
            $table->unsignedSmallInteger('schema_version')->default(1)->after('event_name');
            $table->uuid('visitor_id')->nullable()->after('subject_slug');
            $table->uuid('analytics_session_id')->nullable()->after('visitor_id');
            $table->string('url', 2048)->nullable()->after('session_id');
            $table->string('source', 120)->nullable()->after('referrer');
            $table->string('medium', 120)->nullable()->after('source');
            $table->string('campaign', 255)->nullable()->after('medium');
            $table->string('utm_source', 120)->nullable()->after('campaign');
            $table->string('utm_medium', 120)->nullable()->after('utm_source');
            $table->string('utm_campaign', 255)->nullable()->after('utm_medium');
            $table->string('utm_term', 255)->nullable()->after('utm_campaign');
            $table->string('utm_content', 255)->nullable()->after('utm_term');
            $table->string('device_type', 30)->nullable()->after('utm_content');
            $table->string('browser', 80)->nullable()->after('device_type');
            $table->string('operating_system', 80)->nullable()->after('browser');
            $table->string('language', 20)->nullable()->after('operating_system');
            $table->string('timezone', 80)->nullable()->after('language');
            $table->string('country_code', 2)->nullable()->after('timezone');
            $table->string('region', 120)->nullable()->after('country_code');
            $table->string('city', 160)->nullable()->after('region');
            $table->string('ip_hash', 64)->nullable()->after('city');
            $table->string('user_agent', 500)->nullable()->after('ip_hash');

            $table->unique('event_id', 'platform_analytics_events_event_id_unique');
            $table->foreign('visitor_id')->references('id')->on('analytics_visitors')->nullOnDelete();
            $table->foreign('analytics_session_id')->references('id')->on('analytics_sessions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('platform_analytics_events', function (Blueprint $table): void {
            $table->dropForeign(['visitor_id']);
            $table->dropForeign(['analytics_session_id']);
            $table->dropUnique('platform_analytics_events_event_id_unique');
            $table->dropColumn([
                'event_id', 'schema_version', 'visitor_id', 'analytics_session_id', 'url',
                'source', 'medium', 'campaign', 'utm_source', 'utm_medium', 'utm_campaign',
                'utm_term', 'utm_content', 'device_type', 'browser', 'operating_system',
                'language', 'timezone', 'country_code', 'region', 'city', 'ip_hash', 'user_agent',
            ]);
        });

        Schema::dropIfExists('analytics_sessions');
        Schema::dropIfExists('analytics_visitors');
    }
};
