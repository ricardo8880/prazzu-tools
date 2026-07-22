<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acquisition_contexts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('keyword')->unique();
            $table->string('campaign_identifier')->nullable()->index();
            $table->string('status', 20)->default('inactive')->index();

            $table->string('hero_title_before')->nullable();
            $table->string('hero_title_line')->nullable();
            $table->string('hero_title_highlight')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_search_placeholder')->nullable();

            $table->string('cta_title')->nullable();
            $table->text('cta_description')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('cta_tool_slug')->nullable()->index();
            $table->string('primary_tool_slug')->nullable()->index();

            $table->timestamps();
        });

        Schema::create('acquisition_context_tools', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('acquisition_context_id')
                ->constrained('acquisition_contexts')
                ->cascadeOnDelete();
            $table->string('tool_slug');
            $table->string('placement', 30);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(
                ['acquisition_context_id', 'placement', 'tool_slug'],
                'acquisition_context_tools_unique',
            );
            $table->index(
                ['acquisition_context_id', 'placement', 'position'],
                'acquisition_context_tools_order',
            );
        });

        Schema::create('acquisition_context_articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('acquisition_context_id')
                ->constrained('acquisition_contexts')
                ->cascadeOnDelete();
            $table->string('article_slug');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(
                ['acquisition_context_id', 'article_slug'],
                'acquisition_context_articles_unique',
            );
            $table->index(
                ['acquisition_context_id', 'position'],
                'acquisition_context_articles_order',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acquisition_context_articles');
        Schema::dropIfExists('acquisition_context_tools');
        Schema::dropIfExists('acquisition_contexts');
    }
};
