<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simples_nacional_calculations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_key', 120)->nullable()->index();
            $table->string('company_name', 160);
            $table->date('reference_month')->index();
            $table->string('annex', 5);
            $table->unsignedBigInteger('rbt12_cents');
            $table->unsignedBigInteger('monthly_revenue_cents');
            $table->unsignedBigInteger('estimated_das_cents');
            $table->decimal('effective_rate', 10, 4);
            $table->json('payload');
            $table->timestamps();

            $table->index(['user_id', 'reference_month']);
            $table->index(['session_key', 'reference_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simples_nacional_calculations');
    }
};
