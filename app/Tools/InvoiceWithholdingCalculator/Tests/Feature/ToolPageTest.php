<?php

declare(strict_types=1);

namespace App\Tools\InvoiceWithholdingCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_is_available(): void { $this->get(route('tools.calculadora-retencoes-nota-fiscal.index'))->assertOk()->assertSee('Calculadora de Retenções na Nota Fiscal')->assertSee('IRRF'); }
    public function test_calculation_renders_expected_total(): void { $this->post(route('tools.calculadora-retencoes-nota-fiscal.calculate'),$this->payload())->assertOk()->assertSee('R$ 615,00')->assertSee('Memória de cálculo'); }
    private function payload(): array
    {
        return ['competence'=>'2026-08','invoice_number'=>'NF-1','service_description'=>'Consultoria','gross_value'=>'10.000,00','apply_irrf'=>'1','irrf_rate'=>'1.5','irrf_base_percent'=>'100','inss_rate'=>'11','inss_base_percent'=>'100','iss_rate'=>'2','iss_base_percent'=>'100','apply_pis'=>'1','pis_rate'=>'0.65','pis_base_percent'=>'100','apply_cofins'=>'1','cofins_rate'=>'3','cofins_base_percent'=>'100','apply_csll'=>'1','csll_rate'=>'1','csll_base_percent'=>'100','confirm_scope'=>'1'];
    }
}
