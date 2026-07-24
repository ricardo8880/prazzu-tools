<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    use RefreshDatabase;
    public function test_public_page_displays_essential_form(): void
    {
        $this->get(route('tools.gerador-darf-gps.index'))
            ->assertOk()
            ->assertSee('Gerador Inteligente de DARF/GPS')
            ->assertSee('Calcular acréscimos')
            ->assertSee('0561');
    }

    public function test_calculation_displays_result_without_redirect(): void
    {
        $this->post(route('tools.gerador-darf-gps.calculate'), [
            'guide_type' => 'darf',
            'revenue_code' => '0561',
            'principal' => '1.000,00',
            'due_date' => '2026-07-01',
            'payment_date' => '2026-07-11',
            'selic_accumulated_percent' => '1',
            'confirm_official_check' => '1',
        ])->assertOk()
            ->assertSee('R$ 1.043,00')
            ->assertSee('10');
    }

    public function test_code_must_match_selected_guide_type(): void
    {
        $this->from(route('tools.gerador-darf-gps.index'))->post(route('tools.gerador-darf-gps.calculate'), [
            'guide_type' => 'gps',
            'revenue_code' => '0561',
            'principal' => '1.000,00',
            'due_date' => '2026-07-01',
            'payment_date' => '2026-07-01',
            'selic_accumulated_percent' => '0',
            'confirm_official_check' => '1',
        ])->assertRedirect(route('tools.gerador-darf-gps.index'))->assertSessionHasErrors('principal');
    }
}
