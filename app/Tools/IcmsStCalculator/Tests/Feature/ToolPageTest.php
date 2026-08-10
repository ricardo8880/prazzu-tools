<?php

declare(strict_types=1);

namespace App\Tools\IcmsStCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_is_available(): void { $this->get(route('tools.calculadora-icms-st.index'))->assertOk()->assertSee('Calculadora de ICMS-ST')->assertSee('MVA original'); }
    public function test_internal_calculation_renders_expected_total(): void
    {
        $this->post(route('tools.calculadora-icms-st.calculate'),$this->payload())->assertOk()->assertSee('R$ 72,00')->assertSee('Memória de cálculo');
    }
    public function test_interstate_requires_distinct_states(): void
    {
        $p=$this->payload(); $p['operation_type']='interstate'; $p['origin_uf']='SP'; $p['destination_uf']='SP';
        $this->post(route('tools.calculadora-icms-st.calculate'),$p)->assertSessionHasErrors('destination_uf');
    }
    private function payload(): array
    {
        return ['competence'=>'2026-08','operation_type'=>'internal','origin_uf'=>'SP','destination_uf'=>'SP','merchandise_value'=>'1.000,00','freight'=>'0','insurance'=>'0','other_charges'=>'0','ipi'=>'0','discount'=>'0','original_mva'=>'40','internal_rate'=>'18','interstate_rate'=>'12','fcp_rate'=>'0','confirm_scope'=>'1'];
    }
}
