<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class HistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_requires_authentication(): void
    {
        $this->get(route('tools.emissor-de-recibos.history.index'))->assertRedirect();
    }

    public function test_history_routes_are_registered(): void
    {
        $router = app('router');

        self::assertTrue($router->has('tools.emissor-de-recibos.history.index'));
        self::assertTrue($router->has('tools.emissor-de-recibos.history.repeat'));
        self::assertTrue($router->has('tools.emissor-de-recibos.history.destroy'));
        self::assertTrue($router->has('tools.emissor-de-recibos.history.export.pdf'));
    }

    #[CoversPlusFeature('emissor-de-recibos', 'history')]
    public function test_authenticated_user_can_open_history_when_feature_is_available(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('tools.emissor-de-recibos.issue'), [
            'number' => 'REC-HIST-1', 'payer_name' => 'Cliente', 'payee_name' => 'Prestador',
            'amount' => '100,00', 'description' => 'Serviço', 'issued_at' => '2026-07-23',
        ])->assertOk();
        $this->actingAs($user)->get(route('tools.emissor-de-recibos.history.index'))
            ->assertOk()->assertSee('REC-HIST-1');
    }
}
