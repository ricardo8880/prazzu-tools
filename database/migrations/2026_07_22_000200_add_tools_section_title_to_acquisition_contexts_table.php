<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('acquisition_contexts', function (Blueprint $table): void {
            $table->string('tools_section_title')->nullable()->after('hero_search_placeholder');
        });
    }

    public function down(): void
    {
        Schema::table('acquisition_contexts', function (Blueprint $table): void {
            $table->dropColumn('tools_section_title');
        });
    }
};
