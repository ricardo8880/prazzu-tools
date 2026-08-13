<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite(toolSlug: 'simulador-distribuicao-lucros-balanco', cases: [new GoldenCase('typical', 'Receita 240 mil e lucro 72 mil', GoldenCaseKind::Typical, ['case' => 'typical'], ['outcome' => 'calculated'], 'Parâmetros contábeis informados; legislação tributária deve ser confirmada para o caso concreto.', '1.0.0'), new GoldenCase('boundary', 'Valor mínimo válido', GoldenCaseKind::Boundary, ['value' => '0.01'], ['outcome' => 'calculated'], 'Validação e CalculatorTest do módulo.', '1.0.0'), new GoldenCase('invalid-input', 'Campo obrigatório ausente', GoldenCaseKind::InvalidInput, ['required' => 'missing'], ['outcome' => 'validation-error'], 'ExecuteToolRequest do módulo.', '1.0.0'), new GoldenCase('rounding', 'Arredondamento em centavos', GoldenCaseKind::Rounding, ['case' => 'rounding'], ['outcome' => 'integer-cents'], 'Value object Money do Core.', '1.0.0'), new GoldenCase('non-applicable', 'Regra não inferida automaticamente', GoldenCaseKind::NonApplicable, ['rule' => 'unknown'], ['outcome' => 'user-parameter'], 'README do módulo.', '1.0.0'), new GoldenCase('normative-transition', 'Mudança normativa exige confirmação', GoldenCaseKind::NormativeTransition, ['year' => 2027], ['outcome' => 'verify-rule'], 'README do módulo e fonte normativa indicada.', '1.0.0'), new GoldenCase('regression','Cenário principal permanece estável',GoldenCaseKind::Regression,['case' => 'regression'],['outcome' => 'calculated'],'CalculatorTest do módulo.','1.0.0')]);
    }
}
