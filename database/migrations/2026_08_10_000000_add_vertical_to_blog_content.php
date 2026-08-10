<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_categories', function (Blueprint $table): void {
            $table->string('vertical_slug', 120)->nullable()->after('description')->index();
        });

        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->string('vertical_slug', 120)->nullable()->after('category')->index();
        });

        DB::table('blog_categories')
            ->whereNull('vertical_slug')
            ->update(['vertical_slug' => 'contabilidade']);

        DB::table('blog_posts')
            ->whereNull('vertical_slug')
            ->update(['vertical_slug' => 'contabilidade']);
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->dropColumn('vertical_slug');
        });

        Schema::table('blog_categories', function (Blueprint $table): void {
            $table->dropColumn('vertical_slug');
        });
    }
};
