<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('acquisition_contexts', function (Blueprint $table): void {
            $table->unsignedBigInteger('monthly_investment_cents')->nullable()->after('cta_identifier');
            $table->char('investment_currency', 3)->default('BRL')->after('monthly_investment_cents');
        });
    }

    public function down(): void
    {
        Schema::table('acquisition_contexts', function (Blueprint $table): void {
            $table->dropColumn(['monthly_investment_cents', 'investment_currency']);
        });
    }
};
