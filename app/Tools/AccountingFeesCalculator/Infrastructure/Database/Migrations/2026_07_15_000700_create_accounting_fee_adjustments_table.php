<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_fee_adjustments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('session_key')->nullable()->index();
            $table->string('client_name', 150);
            $table->string('index_type', 20);
            $table->string('reference_period', 7);
            $table->decimal('percentage', 8, 4);
            $table->unsignedBigInteger('current_value_cents');
            $table->bigInteger('difference_cents');
            $table->unsignedBigInteger('adjusted_value_cents');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_fee_adjustments');
    }
};
