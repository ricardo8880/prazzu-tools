<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_seo_metric_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->date('metric_date');
            $table->string('source', 40)->default('google_search_console');
            $table->string('search_type', 30)->default('web');
            $table->string('device', 30)->default('all');
            $table->string('country_code', 2)->nullable();
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('impressions')->default(0);
            $table->decimal('average_position', 8, 2)->nullable();
            $table->unsignedBigInteger('discover_clicks')->default(0);
            $table->unsignedBigInteger('discover_impressions')->default(0);
            $table->unsignedBigInteger('news_clicks')->default(0);
            $table->unsignedBigInteger('news_impressions')->default(0);
            $table->unsignedBigInteger('rich_result_clicks')->default(0);
            $table->unsignedBigInteger('rich_result_impressions')->default(0);
            $table->timestamps();

            $table->unique(
                ['blog_post_id', 'metric_date', 'source', 'search_type', 'device', 'country_code'],
                'seo_metric_snapshot_dimension_unique'
            );
            $table->index(['metric_date', 'source']);
            $table->index(['blog_post_id', 'metric_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_seo_metric_snapshots');
    }
};
