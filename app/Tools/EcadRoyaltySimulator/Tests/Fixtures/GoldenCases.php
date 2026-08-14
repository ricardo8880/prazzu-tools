<?php

declare(strict_types=1);

namespace App\Tools\EcadRoyaltySimulator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $official = 'Ecad, Regulamento de Arrecadação, revisão 12/01/2026: Capítulo VII; UDA R$ 107,31 vigente até dezembro de 2026. Lei 9.610/1998, art. 68.';
        return new GoldenCaseSuite(toolSlug: 'simulador-ecad-direitos-autorais', cases: [
            new GoldenCase(identifier: 'typical', title: 'Três UDAs na referência 2026', kind: GoldenCaseKind::Typical, input: ['method'=>'uda','uda_value'=>'107.31','uda_quantity'=>'3'], expected: ['amount'=>'R$ 321,93'], reference: $official, normativeRuleVersion: 'ecad.reference:2026.1.0', roundingPolicy: 'Inteiros em centavos e escala decimal 1/10.000; HalfUp.'),
            new GoldenCase(identifier: 'boundary', title: 'Uma UDA mínima de referência', kind: GoldenCaseKind::Boundary, input: ['method'=>'uda','uda_value'=>'107.31','uda_quantity'=>'0.0001'], expected: ['amount'=>'R$ 0,01'], reference: $official, normativeRuleVersion: 'ecad.reference:2026.1.0', roundingPolicy: 'HalfUp para centavos.'),
            new GoldenCase(identifier: 'invalid-input', title: 'Quantidade zero é rejeitada', kind: GoldenCaseKind::InvalidInput, input: ['method'=>'uda','uda_quantity'=>'0'], expected: ['outcome'=>'validation-error'], reference: 'Contrato do domínio exige multiplicador positivo.', normativeRuleVersion: '1.0.0'),
            new GoldenCase(identifier: 'rounding', title: 'Tabela em UDA por metro quadrado arredonda ao centavo', kind: GoldenCaseKind::Rounding, input: ['method'=>'uda_per_sqm','uda_value'=>'107.31','area'=>'100','uda_per_sqm'=>'0.012'], expected: ['amount'=>'R$ 128,77'], reference: $official, normativeRuleVersion: 'ecad.reference:2026.1.0', roundingPolicy: 'HalfUp para centavos.'),
            new GoldenCase(identifier: 'non-applicable', title: 'Ferramenta não escolhe enquadramento ECAD automaticamente', kind: GoldenCaseKind::NonApplicable, input: ['scenario'=>'infer-category-from-business-name'], expected: ['outcome'=>'explicitly-not-inferred'], reference: 'Regulamento do Ecad utiliza múltiplos critérios de enquadramento; o módulo exige que o usuário transcreva o parâmetro aplicável.', normativeRuleVersion: '1.0.0'),
            new GoldenCase(identifier: 'normative-transition', title: 'UDA 2026 possui vigência explícita', kind: GoldenCaseKind::NormativeTransition, input: ['reference_year'=>'2026'], expected: ['uda'=>'R$ 107,31','valid_until'=>'2026-12'], reference: $official, normativeRuleVersion: 'ecad.reference:2026.1.0'),
            new GoldenCase(identifier: 'regression', title: 'Percentual usa Money e Percentage sem ponto flutuante', kind: GoldenCaseKind::Regression, input: ['method'=>'percentage','base'=>'10000.00','rate'=>'2.5'], expected: ['amount'=>'R$ 250,00'], reference: 'CalculatorTest e infraestrutura Money/Percentage do Core.', normativeRuleVersion: '1.0.0', roundingPolicy: 'Money::percentage; HalfUp.'),
        ]);
    }
}
