<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_fee_calculations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->uuid('session_key')->nullable()->index();
            $table->json('input');
            $table->json('result');
            $table->boolean('is_favorite')->default(false)->index();
            $table->uuid('share_token')->nullable()->unique();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['session_key', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_fee_calculations');
    }
};
