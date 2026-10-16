<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_is_available(): void
    {
        $this->get(route('tools.irs-payment-plan-calculator.index'))
            ->assertOk()->assertSee('IRS Payment Plan Calculator')->assertSee('Tax balance and payment assumptions');
    }

    #[CoversPlusFeature('irs-payment-plan-calculator', 'scenario_comparison')]
    #[CoversPlusFeature('irs-payment-plan-calculator', 'export')]
    public function test_basic_calculation_renders_expected_values(): void
    {
        $this->post(route('tools.irs-payment-plan-calculator.calculate'), [
            'debt_amount' => '12,000.00', 'installments' => 12, 'monthly_charge' => '1.00', 'entry_amount' => '0.00',
        ])->assertOk()->assertSee('$1,065.00')->assertSee('$780.00')->assertSee('$12,780.00')->assertSee('Estimated payment plan');
    }

}

