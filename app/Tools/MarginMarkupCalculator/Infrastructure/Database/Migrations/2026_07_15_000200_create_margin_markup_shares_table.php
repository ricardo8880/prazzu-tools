<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('margin_markup_shares', function (Blueprint $table): void {
            $table->id();
            $table->uuid('tool_run_id')->index();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('token')->unique();
            $table->string('access_code_hash')->nullable();
            $table->dateTime('expires_at')->nullable()->index();
            $table->dateTime('revoked_at')->nullable();
            $table->timestamps();

            $table->foreign('tool_run_id')->references('id')->on('tool_runs')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('margin_markup_shares');
    }
};
