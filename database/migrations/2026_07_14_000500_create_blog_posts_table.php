<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('content');
            $table->string('category', 100)->index();
            $table->string('cover_image_path')->nullable();
            $table->string('cover_image_alt')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('content_updated_at')->nullable();
            $table->string('primary_keyword')->nullable();
            $table->json('related_keywords')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('social_image_path')->nullable();
            $table->boolean('should_index')->default(true)->index();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['category', 'status', 'published_at']);
        });

        Schema::create('blog_post_tool', function (Blueprint $table): void {
            $table->foreignId('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->string('tool_slug', 120);
            $table->timestamps();

            $table->primary(['blog_post_id', 'tool_slug']);
            $table->index('tool_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_tool');
        Schema::dropIfExists('blog_posts');
    }
};
