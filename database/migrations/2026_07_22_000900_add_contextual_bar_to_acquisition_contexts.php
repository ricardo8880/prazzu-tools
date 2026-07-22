<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('acquisition_contexts', function (Blueprint $table): void {
            $table->string('contextual_message')->nullable()->after('cta_tool_slug');
            $table->string('contextual_continue_label', 80)->nullable()->after('contextual_message');
            $table->string('contextual_continue_url', 2048)->nullable()->after('contextual_continue_label');
            $table->string('contextual_continue_tool_slug')->nullable()->after('contextual_continue_url');
        });
    }

    public function down(): void
    {
        Schema::table('acquisition_contexts', function (Blueprint $table): void {
            $table->dropColumn([
                'contextual_message',
                'contextual_continue_label',
                'contextual_continue_url',
                'contextual_continue_tool_slug',
            ]);
        });
    }
};
