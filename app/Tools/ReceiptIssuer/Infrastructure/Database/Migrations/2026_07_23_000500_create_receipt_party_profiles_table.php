<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt_party_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('party_type', 10);
            $table->string('label', 80);
            $table->string('name', 160);
            $table->string('document_type', 4)->nullable();
            $table->text('document')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'party_type', 'updated_at']);
            $table->unique(['user_id', 'party_type', 'label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_party_profiles');
    }
};
