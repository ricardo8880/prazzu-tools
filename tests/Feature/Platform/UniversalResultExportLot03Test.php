<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class UniversalResultExportLot03Test extends TestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function routes(): iterable
    {
        yield 'ponto de equilibrio' => ['tools.break-even-calculator.export.pdf', 'tools.break-even-calculator.export.excel'];
        yield 'margem e markup' => ['tools.margin-markup-calculator.export.pdf', 'tools.margin-markup-calculator.export'];
        yield 'comissao vendedores' => ['tools.sales-commission-calculator.export.pdf', 'tools.sales-commission-calculator.export.excel'];
        yield 'gerador holerite' => ['tools.pay-stub-generator.export.pdf', 'tools.pay-stub-generator.export.excel'];
        yield 'simulador admissao' => ['tools.new-hire-cost-calculator.export.pdf', 'tools.new-hire-cost-calculator.export.excel'];
        yield 'calculadora rescisao' => ['tools.final-pay-severance-calculator.export', 'tools.final-pay-severance-calculator.export.excel'];
        yield 'reajuste salarial' => ['tools.salary-increase-calculator.export.pdf', 'tools.salary-increase-calculator.export.excel'];
    }

    #[DataProvider('routes')]
    public function test_pdf_and_excel_routes_are_registered(string $pdf, string $excel): void
    {
        self::assertTrue(app('router')->has($pdf));
        self::assertTrue(app('router')->has($excel));
    }
}
