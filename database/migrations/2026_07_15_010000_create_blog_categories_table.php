<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->foreignId('category_id')
                ->nullable()
                ->after('content')
                ->constrained('blog_categories')
                ->restrictOnDelete();
        });

        DB::table('blog_posts')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->distinct()
            ->orderBy('category')
            ->get()
            ->each(function (object $postCategory): void {
                $name = trim((string) $postCategory->category);
                $slugBase = Str::slug($name) ?: 'categoria';
                $slug = $slugBase;
                $suffix = 2;

                while (DB::table('blog_categories')->where('slug', $slug)->exists()) {
                    $slug = "{$slugBase}-{$suffix}";
                    $suffix++;
                }

                $categoryId = DB::table('blog_categories')->insertGetId([
                    'name' => $name,
                    'slug' => $slug,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('blog_posts')
                    ->where('category', $postCategory->category)
                    ->update(['category_id' => $categoryId]);
            });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('category_id');
        });

        Schema::dropIfExists('blog_categories');
    }
};
