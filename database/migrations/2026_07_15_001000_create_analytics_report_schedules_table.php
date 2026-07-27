<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_report_schedules', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('frequency', 20);
            $table->string('format', 20);
            $table->json('filters')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('next_run_at')->index();
            $table->timestamp('last_run_at')->nullable();
            $table->string('last_file_path', 500)->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_report_schedules');
    }
};
