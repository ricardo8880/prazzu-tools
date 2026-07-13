<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_runs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tool_slug', 120)->index();
            $table->string('tool_version', 50);
            $table->string('rule_version', 50);
            $table->date('reference_date')->index();
            $table->string('status', 30)->index();
            $table->text('input_payload')->nullable();
            $table->text('result_payload')->nullable();
            $table->text('normative_references')->nullable();
            $table->string('error_code', 100)->nullable();
            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->index(['tool_slug', 'tool_version', 'rule_version'], 'tool_runs_version_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_runs');
    }
};
