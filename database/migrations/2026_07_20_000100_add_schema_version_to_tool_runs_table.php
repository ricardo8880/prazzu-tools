<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tool_runs', function (Blueprint $table): void {
            $table->unsignedInteger('schema_version')->default(1)->after('tool_version');
            $table->index(['tool_slug', 'schema_version']);
        });
    }

    public function down(): void
    {
        Schema::table('tool_runs', function (Blueprint $table): void {
            $table->dropIndex(['tool_slug', 'schema_version']);
            $table->dropColumn('schema_version');
        });
    }
};
