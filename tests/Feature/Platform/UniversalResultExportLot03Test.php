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
        yield 'ponto de equilibrio' => ['tools.ponto-de-equilibrio.export.pdf', 'tools.ponto-de-equilibrio.export.excel'];
        yield 'margem e markup' => ['tools.calculadora-margem-markup.export.pdf', 'tools.calculadora-margem-markup.export'];
        yield 'pro labore ideal' => ['tools.simulador-pro-labore-ideal.export.pdf', 'tools.simulador-pro-labore-ideal.export.excel'];
        yield 'comissao vendedores' => ['tools.comissao-vendedores.export.pdf', 'tools.comissao-vendedores.export.excel'];
        yield 'gerador holerite' => ['tools.gerador-holerite.export.pdf', 'tools.gerador-holerite.export.excel'];
        yield 'simulador admissao' => ['tools.simulador-admissao.export.pdf', 'tools.simulador-admissao.export.excel'];
        yield 'calculadora rescisao' => ['tools.calculadora-de-rescisao.export', 'tools.calculadora-de-rescisao.export.excel'];
        yield 'reajuste salarial' => ['tools.reajuste-salarial.export.pdf', 'tools.reajuste-salarial.export.excel'];
    }

    #[DataProvider('routes')]
    public function test_pdf_and_excel_routes_are_registered(string $pdf, string $excel): void
    {
        self::assertTrue(app('router')->has($pdf));
        self::assertTrue(app('router')->has($excel));
    }
}
