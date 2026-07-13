<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 120)->index();
            $table->string('auditable_type', 180)->nullable();
            $table->string('auditable_id', 180)->nullable();
            $table->text('metadata')->nullable();
            $table->timestamp('occurred_at')->index();

            $table->index(['auditable_type', 'auditable_id'], 'audit_logs_auditable_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
