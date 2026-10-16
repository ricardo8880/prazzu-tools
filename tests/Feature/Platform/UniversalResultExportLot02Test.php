<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class UniversalResultExportLot02Test extends TestCase
{
    /** @return iterable<string, array{string,string}> */
    public static function routes(): iterable
    {
        yield 'custo funcionario' => ['tools.employee-cost-calculator.download.pdf', 'tools.employee-cost-calculator.download.excel'];
        yield 'encargos' => ['tools.employer-payroll-tax-calculator.export.pdf', 'tools.employer-payroll-tax-calculator.export.excel'];
        yield 'modelos' => ['tools.employee-vs-contractor-calculator.export.pdf', 'tools.employee-vs-contractor-calculator.export.excel'];
        yield 'capital giro' => ['tools.working-capital-calculator.export.pdf', 'tools.working-capital-calculator.export.excel'];
        yield 'fluxo caixa' => ['tools.cash-flow-calculator.export.pdf', 'tools.cash-flow-calculator.export.excel'];
    }

    #[DataProvider('routes')]
    public function test_pdf_and_excel_routes_are_registered(string $pdf, string $excel): void
    {
        self::assertTrue(app('router')->has($pdf));
        self::assertTrue(app('router')->has($excel));
    }
}
