<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class AccountingFeesProductBoundaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_module_does_not_expose_crm_or_calculation_sharing(): void
    {
        self::assertFalse(Route::has('tools.calculadora-de-honorarios-contabeis.crm.index'));
        self::assertFalse(Route::has('tools.calculadora-de-honorarios-contabeis.history.share'));
        self::assertFalse(Route::has('tools.calculadora-de-honorarios-contabeis.shared'));

        self::assertFalse(Schema::hasTable('accounting_fee_clients'));
        self::assertFalse(Schema::hasColumn('accounting_fee_calculations', 'share_token'));
    }

    public function test_adjustment_history_uses_a_generic_scenario_label(): void
    {
        self::assertTrue(Schema::hasColumn('accounting_fee_adjustments', 'scenario_label'));
        self::assertFalse(Schema::hasColumn('accounting_fee_adjustments', 'client_name'));
    }

    public function test_removal_migration_is_reversible_and_preserves_adjustment_labels(): void
    {
        DB::table('accounting_fee_adjustments')->insert([
            'scenario_label' => 'Renovação anual — cenário A',
            'index_type' => 'ipca',
            'reference_period' => '2026-07',
            'percentage' => '4.6200',
            'current_value_cents' => 150000,
            'difference_cents' => 6930,
            'adjusted_value_cents' => 156930,
        ]);

        $migration = require dirname(__DIR__, 2).'/Infrastructure/Database/Migrations/2026_07_18_000100_remove_accounting_crm_and_sharing.php';

        $migration->down();

        self::assertTrue(Schema::hasTable('accounting_fee_clients'));
        self::assertTrue(Schema::hasColumn('accounting_fee_calculations', 'share_token'));
        self::assertSame(
            'Renovação anual — cenário A',
            DB::table('accounting_fee_adjustments')->value('client_name'),
        );

        $migration->up();

        self::assertFalse(Schema::hasTable('accounting_fee_clients'));
        self::assertFalse(Schema::hasColumn('accounting_fee_calculations', 'share_token'));
        self::assertSame(
            'Renovação anual — cenário A',
            DB::table('accounting_fee_adjustments')->value('scenario_label'),
        );
    }
}
