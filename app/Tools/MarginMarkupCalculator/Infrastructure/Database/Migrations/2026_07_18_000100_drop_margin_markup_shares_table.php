<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('margin_markup_shares');
    }

    public function down(): void
    {
        if (Schema::hasTable('margin_markup_shares')) {
            return;
        }

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
};
