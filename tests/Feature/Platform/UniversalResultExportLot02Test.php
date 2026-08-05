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
        yield 'custo funcionario' => ['tools.custo-funcionario-clt.download.pdf', 'tools.custo-funcionario-clt.download.excel'];
        yield 'fator r' => ['tools.simulador-fator-r.export.pdf', 'tools.simulador-fator-r.export.excel'];
        yield 'das atraso' => ['tools.das-em-atraso.export.pdf', 'tools.das-em-atraso.export.excel'];
        yield 'encargos' => ['tools.encargos-trabalhistas.export.pdf', 'tools.encargos-trabalhistas.export.excel'];
        yield 'modelos' => ['tools.comparador-clt-pj-autonomo.export.pdf', 'tools.comparador-clt-pj-autonomo.export.excel'];
        yield 'inss patronal' => ['tools.inss-patronal.export.pdf', 'tools.inss-patronal.export.excel'];
        yield 'capital giro' => ['tools.capital-de-giro.export.pdf', 'tools.capital-de-giro.export.excel'];
        yield 'fluxo caixa' => ['tools.fluxo-de-caixa.export.pdf', 'tools.fluxo-de-caixa.export.excel'];
    }

    #[DataProvider('routes')]
    public function test_pdf_and_excel_routes_are_registered(string $pdf, string $excel): void
    {
        self::assertTrue(app('router')->has($pdf));
        self::assertTrue(app('router')->has($excel));
    }
}
