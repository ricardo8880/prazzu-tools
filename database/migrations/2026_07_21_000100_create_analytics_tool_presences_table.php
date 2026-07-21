<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_tool_presences', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('tool_slug', 120)->index();
            $table->uuid('visitor_id')->nullable()->index();
            $table->uuid('analytics_session_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('path', 500)->nullable();
            $table->string('source', 120)->nullable()->index();
            $table->string('country_code', 8)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->timestamp('last_seen_at')->index();
            $table->timestamps();

            $table->index(['last_seen_at', 'tool_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_tool_presences');
    }
};
