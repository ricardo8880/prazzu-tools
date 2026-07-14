<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('platform_analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_name', 80);
            $table->string('channel', 40);
            $table->string('subject_type', 80)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_slug')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->string('path', 2048)->nullable();
            $table->string('referrer', 2048)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->index(['channel', 'event_name', 'occurred_at']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('subject_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_analytics_events');
    }
};
