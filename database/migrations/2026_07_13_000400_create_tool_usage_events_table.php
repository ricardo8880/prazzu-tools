<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_usage_events', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('tool_slug', 120)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event', 60)->index();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->index(['tool_slug', 'event', 'occurred_at'], 'tool_usage_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_usage_events');
    }
};
