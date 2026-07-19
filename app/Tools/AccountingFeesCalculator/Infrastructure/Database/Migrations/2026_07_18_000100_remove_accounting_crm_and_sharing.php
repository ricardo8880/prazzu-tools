<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('accounting_fee_clients');

        if (Schema::hasTable('accounting_fee_calculations')
            && Schema::hasColumn('accounting_fee_calculations', 'share_token')) {
            if (Schema::hasIndex('accounting_fee_calculations', ['share_token'], 'unique')) {
                Schema::table('accounting_fee_calculations', function (Blueprint $table): void {
                    $table->dropUnique(['share_token']);
                });
            }

            Schema::table('accounting_fee_calculations', function (Blueprint $table): void {
                $table->dropColumn('share_token');
            });
        }

        if (Schema::hasTable('accounting_fee_adjustments')
            && Schema::hasColumn('accounting_fee_adjustments', 'client_name')
            && ! Schema::hasColumn('accounting_fee_adjustments', 'scenario_label')) {
            Schema::table('accounting_fee_adjustments', function (Blueprint $table): void {
                $table->renameColumn('client_name', 'scenario_label');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('accounting_fee_adjustments')
            && Schema::hasColumn('accounting_fee_adjustments', 'scenario_label')
            && ! Schema::hasColumn('accounting_fee_adjustments', 'client_name')) {
            Schema::table('accounting_fee_adjustments', function (Blueprint $table): void {
                $table->renameColumn('scenario_label', 'client_name');
            });
        }

        if (Schema::hasTable('accounting_fee_calculations')
            && ! Schema::hasColumn('accounting_fee_calculations', 'share_token')) {
            Schema::table('accounting_fee_calculations', function (Blueprint $table): void {
                $table->uuid('share_token')->nullable()->unique();
            });
        }

        if (! Schema::hasTable('accounting_fee_clients')) {
            Schema::create('accounting_fee_clients', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->uuid('session_key')->nullable()->index();
                $table->string('company_name', 150);
                $table->string('document', 30)->nullable();
                $table->string('contact_name', 120);
                $table->string('email', 150)->nullable();
                $table->string('phone', 30)->nullable();
                $table->unsignedBigInteger('monthly_fee_cents')->default(0);
                $table->string('proposal_status', 30)->default('not_created');
                $table->string('contract_status', 30)->default('not_created');
                $table->string('pipeline_status', 30)->default('prospect');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'pipeline_status']);
                $table->index(['session_key', 'pipeline_status']);
                $table->index('company_name');
            });
        }
    }
};
