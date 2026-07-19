<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analytics_visitors', function (Blueprint $table): void {
            $table->string('first_medium', 120)->nullable()->after('first_source');
            $table->string('first_campaign', 255)->nullable()->after('first_medium');
            $table->json('first_utm')->nullable()->after('first_campaign');
            $table->string('last_medium', 120)->nullable()->after('last_source');
            $table->string('last_campaign', 255)->nullable()->after('last_medium');
            $table->json('last_utm')->nullable()->after('last_campaign');
            $table->index(['first_medium', 'first_seen_at']);
            $table->index(['last_medium', 'last_seen_at']);
        });

        Schema::table('analytics_sessions', function (Blueprint $table): void {
            $table->string('utm_source', 120)->nullable()->after('campaign');
            $table->string('utm_medium', 120)->nullable()->after('utm_source');
            $table->string('utm_campaign', 255)->nullable()->after('utm_medium');
            $table->string('utm_term', 255)->nullable()->after('utm_campaign');
            $table->string('utm_content', 255)->nullable()->after('utm_term');
            $table->index(['medium', 'started_at']);
            $table->index(['campaign', 'started_at']);
            $table->index(['utm_source', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table): void {
            $table->dropIndex(['medium', 'started_at']);
            $table->dropIndex(['campaign', 'started_at']);
            $table->dropIndex(['utm_source', 'started_at']);
            $table->dropColumn(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content']);
        });

        Schema::table('analytics_visitors', function (Blueprint $table): void {
            $table->dropIndex(['first_medium', 'first_seen_at']);
            $table->dropIndex(['last_medium', 'last_seen_at']);
            $table->dropColumn(['first_medium', 'first_campaign', 'first_utm', 'last_medium', 'last_campaign', 'last_utm']);
        });
    }
};
