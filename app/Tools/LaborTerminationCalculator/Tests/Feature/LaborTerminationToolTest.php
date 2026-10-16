<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class LaborTerminationToolTest extends TestCase
{
    #[CoversPlusFeature('final-pay-severance-calculator', 'history')]
    #[CoversPlusFeature('final-pay-severance-calculator', 'repeat_history')]
    #[CoversPlusFeature('final-pay-severance-calculator', 'historical_report')]
    public function test_page_uses_us_final_pay_inputs(): void
    {
        $this->get(route('tools.final-pay-severance-calculator.index'))
            ->assertOk()
            ->assertSee('Final Pay &amp; Severance Calculator', false)
            ->assertSee('Unused PTO hours')
            ->assertSee('Severance weeks')
            ->assertDontSee('FGTS')
            ->assertDontSee('13º');
    }

    public function test_calculation_result_is_rendered_after_post(): void
    {
        $this->post(route('tools.final-pay-severance-calculator.calculate'), [
            'termination_date' => '2026-10-16',
            'separation_type' => 'layoff',
            'hourly_rate' => '25.00',
            'regular_hours_due' => '8',
            'unused_pto_hours' => '4',
            'pto_payout_percent' => '100',
            'weekly_base_pay' => '1000.00',
            'severance_weeks' => '2',
            'bonus_commission_due' => '0.00',
            'other_pay' => '0.00',
            'federal_withholding' => '0.00',
            'state_local_withholding' => '0.00',
            'other_deductions' => '0.00',
        ])->assertOk()->assertSee('Final pay estimate')->assertSee('$2,300.00');
    }
}
