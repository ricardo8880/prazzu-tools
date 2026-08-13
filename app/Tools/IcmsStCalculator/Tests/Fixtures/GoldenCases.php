<?php

declare(strict_types=1);

namespace App\Tools\IcmsStCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $ref = 'Convênio ICMS 142/2018; SEFAZ-PE fórmula da MVA ajustada; Receita Estadual RS cálculo do ICMS-ST; CalculatorTest do módulo.';

        return new GoldenCaseSuite(toolSlug: 'calculadora-icms-st', cases: [
            new GoldenCase('typical', 'Operação interna com MVA 40%', GoldenCaseKind::Typical, ['base' => '1000', 'mva' => '40', 'interna' => '18'], ['base_st' => 'R$ 1.400,00', 'icms_st' => 'R$ 72,00'], $ref, 'icms_st.parametric_2026:2026.1.0', 'Money/Percentage sem float.'),
            new GoldenCase('boundary', 'FCP zero não altera total', GoldenCaseKind::Boundary, ['fcp' => '0'], ['fcp_st' => 'R$ 0,00'], $ref, 'icms_st.parametric_2026:2026.1.0'),
            new GoldenCase('invalid-input', 'Base zerada é rejeitada', GoldenCaseKind::InvalidInput, ['merchandise_value' => '0'], ['outcome' => 'validation-error'], 'ExecuteToolRequest exige valor positivo.', '1.0.0'),
            new GoldenCase('rounding', 'MVA ajustada preserva precisão inteira', GoldenCaseKind::Rounding, ['mva' => '40', 'inter' => '12', 'intra' => '18'], ['mva_ajustada' => '50.243902%'], $ref, 'icms_st.parametric_2026:2026.1.0', 'Percentual escalado em inteiros.'),
            new GoldenCase('non-applicable', 'NCM/CEST sem sujeição a ST não é inferido', GoldenCaseKind::NonApplicable, ['scenario' => 'produto-fora-da-st'], ['outcome' => 'explicitly-not-covered'], 'README do módulo exige confirmação da sujeição à ST.', '1.0.0'),
            new GoldenCase('normative-transition', 'Parâmetros estaduais devem ser confirmados em 2026', GoldenCaseKind::NormativeTransition, ['competence' => '2026-08'], ['rule' => 'icms_st.parametric_2026:2026.1.0'], $ref, '2026.1.0'),
            new GoldenCase('regression', 'ICMS próprio é abatido do ICMS interno presumido', GoldenCaseKind::Regression, ['base' => '1000', 'base_st' => '1400', 'rate' => '18'], ['icms_proprio' => 'R$ 180,00', 'icms_st' => 'R$ 72,00'], $ref, 'icms_st.parametric_2026:2026.1.0'),
        ]);
    }
}
