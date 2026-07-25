<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_company_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 160);
            $table->string('legal_name', 200)->nullable();
            $table->text('document')->nullable();
            $table->string('office_name', 160)->nullable();
            $table->string('accountant_name', 160)->nullable();
            $table->string('accountant_registration', 40)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'name'], 'tool_company_profiles_owner_name_unique');
            $table->index(['user_id', 'updated_at'], 'tool_company_profiles_owner_lookup');
        });

        Schema::create('tool_employee_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_profile_id')->nullable()
                ->constrained('tool_company_profiles')->nullOnDelete();
            $table->string('name', 160);
            $table->text('document')->nullable();
            $table->string('department', 120)->nullable();
            $table->string('role', 120)->nullable();
            $table->text('defaults')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'company_profile_id', 'updated_at'], 'tool_employee_profiles_owner_company_lookup');
            $table->unique(['user_id', 'company_profile_id', 'name'], 'tool_employee_profiles_owner_name_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_employee_profiles');
        Schema::dropIfExists('tool_company_profiles');
    }
};
