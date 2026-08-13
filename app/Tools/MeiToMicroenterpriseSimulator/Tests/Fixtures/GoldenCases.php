<?php

declare(strict_types=1);

namespace App\Tools\MeiToMicroenterpriseSimulator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $official = 'Gov.br/Portal do Empreendedor e Simples Nacional, referências vigentes consultadas em 12/08/2026.';

        return new GoldenCaseSuite(toolSlug: 'simulador-mei-microempresa', cases: [
            new GoldenCase('typical', 'Projeção de R$ 90 mil em 2026', GoldenCaseKind::Typical, ['projected' => '90000'], ['band' => 'excess_up_to_20'], $official, '1.0.0'),
            new GoldenCase('boundary', 'Exatamente no teto de 2026', GoldenCaseKind::Boundary, ['projected' => '81000'], ['band' => 'within_limit'], $official, '1.0.0'),
            new GoldenCase('invalid-input', 'Faturamento projetado zero é rejeitado', GoldenCaseKind::InvalidInput, ['projected' => '0'], ['outcome' => 'validation-error'], 'ExecuteToolRequest exige valor maior que zero.', '1.0.0'),
            new GoldenCase('rounding', 'Custos anuais em centavos', GoldenCaseKind::Rounding, ['accounting' => '500', 'other' => '250'], ['annual_fixed_costs_minor' => 900000], 'CalculatorTest do módulo.', '1.0.0'),
            new GoldenCase('non-applicable', 'Alíquota do Simples não é inferida', GoldenCaseKind::NonApplicable, ['activity' => 'unknown'], ['outcome' => 'user-supplied-rate'], 'README do módulo define cálculo paramétrico.', '1.0.0'),
            new GoldenCase('normative-transition', 'Limites divulgados 2027/2028 são versionados', GoldenCaseKind::NormativeTransition, ['year' => 2027], ['limit' => '110000'], $official, '1.0.0'),
            new GoldenCase('regression','Acima de 20% do teto',GoldenCaseKind::Regression,['projected' => '100000'],['band' => 'excess_over_20'],$official,'1.0.0'),
        ]);
    }
}
