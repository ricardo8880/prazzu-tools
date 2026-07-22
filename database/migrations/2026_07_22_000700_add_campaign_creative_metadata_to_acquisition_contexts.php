<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('acquisition_contexts', function (Blueprint $table): void {
            $table->string('campaign_source', 120)->nullable()->after('campaign_identifier');
            $table->string('campaign_medium', 120)->nullable()->after('campaign_source');
            $table->string('content_identifier')->nullable()->after('campaign_medium');
            $table->string('video_identifier')->nullable()->after('content_identifier');
            $table->string('banner_identifier')->nullable()->after('video_identifier');
            $table->string('cta_identifier')->nullable()->after('banner_identifier');

            $table->index(['campaign_source', 'campaign_medium'], 'acquisition_contexts_source_medium');
            $table->index('content_identifier');
            $table->index('video_identifier');
        });
    }

    public function down(): void
    {
        Schema::table('acquisition_contexts', function (Blueprint $table): void {
            $table->dropIndex('acquisition_contexts_source_medium');
            $table->dropIndex(['content_identifier']);
            $table->dropIndex(['video_identifier']);
            $table->dropColumn([
                'campaign_source',
                'campaign_medium',
                'content_identifier',
                'video_identifier',
                'banner_identifier',
                'cta_identifier',
            ]);
        });
    }
};
