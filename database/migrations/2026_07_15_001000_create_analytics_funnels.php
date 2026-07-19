<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_funnels', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('description', 500)->nullable();
            $table->string('identity_type', 20)->default('visitor');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('analytics_funnel_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('funnel_id')->constrained('analytics_funnels')->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('name', 120);
            $table->json('event_names');
            $table->timestamps();
            $table->unique(['funnel_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_funnel_steps');
        Schema::dropIfExists('analytics_funnels');
    }
};
