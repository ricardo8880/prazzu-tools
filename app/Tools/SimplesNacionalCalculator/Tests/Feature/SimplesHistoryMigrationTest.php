<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Feature;

use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Models\User;
use App\Tools\SimplesNacionalCalculator\Tool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class SimplesHistoryMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_history_is_migrated_without_company_identity_and_can_be_rolled_back(): void
    {
        self::assertFalse(Schema::hasTable('simples_nacional_calculations'));

        $migration = require dirname(__DIR__, 2).'/Infrastructure/Database/Migrations/2026_07_18_000200_migrate_simples_nacional_history_to_core.php';
        $migration->down();

        $user = User::factory()->create();
        DB::table('simples_nacional_calculations')->insert([
            'id' => 42,
            'user_id' => $user->getAuthIdentifier(),
            'session_key' => null,
            'company_name' => 'Empresa que não pode ser migrada',
            'reference_month' => '2026-07-01',
            'annex' => 'I',
            'rbt12_cents' => 18_000_000,
            'monthly_revenue_cents' => 1_500_000,
            'estimated_das_cents' => 60_000,
            'effective_rate' => '4.0000',
            'payload' => json_encode([
                'annex' => 'I',
                'monthly_revenue' => 'R$ 15.000,00',
                'effective_rate' => '4.000000%',
                'estimated_das' => 'R$ 600,00',
            ], JSON_THROW_ON_ERROR),
            'created_at' => '2026-07-01 10:00:00',
            'updated_at' => '2026-07-01 10:00:00',
        ]);

        $migration->up();

        self::assertFalse(Schema::hasTable('simples_nacional_calculations'));
        $entries = app(ToolRunHistory::class)->recentSucceeded(
            Tool::SLUG,
            (int) $user->getAuthIdentifier(),
        );
        self::assertCount(1, $entries);
        self::assertSame('2026-07', $entries[0]->input['reference_month']);
        self::assertSame('R$ 600,00', $entries[0]->result['estimated_das']);
        self::assertArrayNotHasKey('company_name', $entries[0]->input);
        self::assertStringNotContainsString(
            'Empresa que não pode ser migrada',
            json_encode([$entries[0]->input, $entries[0]->result], JSON_THROW_ON_ERROR),
        );

        $migration->down();

        self::assertTrue(Schema::hasTable('simples_nacional_calculations'));
        $legacy = DB::table('simples_nacional_calculations')->where('id', 42)->sole();
        self::assertSame('Cenário migrado', $legacy->company_name);
        self::assertSame(18_000_000, (int) $legacy->rbt12_cents);
        self::assertSame(1_500_000, (int) $legacy->monthly_revenue_cents);
        self::assertSame(60_000, (int) $legacy->estimated_das_cents);

        $migration->up();

        self::assertFalse(Schema::hasTable('simples_nacional_calculations'));
        self::assertCount(1, app(ToolRunHistory::class)->recentSucceeded(
            Tool::SLUG,
            (int) $user->getAuthIdentifier(),
        ));
    }
}
