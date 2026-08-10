<?php

declare(strict_types=1);

namespace App\Tools\InvoiceWithholdingCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE='TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';
    public static function suite(): GoldenCaseSuite
    {
        $ref='Lei 10.833/2003; Lei 8.212/1991 art. 31; LC 116/2003; IN RFB 1.234/2012; CalculatorTest do módulo.';
        return new GoldenCaseSuite(toolSlug:'calculadora-retencoes-nota-fiscal',cases:[
            new GoldenCase('typical','IRRF + PIS + Cofins + CSLL sobre R$ 10 mil',GoldenCaseKind::Typical,['gross'=>'10000','rates'=>'1.5+0.65+3+1'],['withheld'=>'R$ 615,00','net'=>'R$ 9.385,00'],$ref,'invoice_withholding.parametric_2026:2026.1.0','Money/Percentage sem float.'),
            new GoldenCase('boundary','Tributo desmarcado resulta em zero',GoldenCaseKind::Boundary,['apply_inss'=>false],['inss'=>'R$ 0,00'],$ref,'invoice_withholding.parametric_2026:2026.1.0'),
            new GoldenCase('invalid-input','Nota zerada é rejeitada',GoldenCaseKind::InvalidInput,['gross_value'=>'0'],['outcome'=>'validation-error'],'ExecuteToolRequest exige valor maior que zero.','1.0.0'),
            new GoldenCase('rounding','Percentuais monetários arredondam em centavos',GoldenCaseKind::Rounding,['gross'=>'10000','pis'=>'0.65'],['pis'=>'R$ 65,00'],$ref,'invoice_withholding.parametric_2026:2026.1.0'),
            new GoldenCase('non-applicable','Serviço sem retenção não deve ser inferido',GoldenCaseKind::NonApplicable,['scenario'=>'sem-incidencia'],['outcome'=>'explicit-user-confirmation'],'README exige seleção explícita dos tributos.','1.0.0'),
            new GoldenCase('normative-transition','Parâmetros devem ser revistos na competência',GoldenCaseKind::NormativeTransition,['competence'=>'2026-08'],['rule'=>'invoice_withholding.parametric_2026:2026.1.0'],$ref,'2026.1.0'),
            new GoldenCase('regression','Base de 50% com INSS 11%',GoldenCaseKind::Regression,['gross'=>'10000','base'=>'50%','rate'=>'11%'],['inss'=>'R$ 550,00'],$ref,'invoice_withholding.parametric_2026:2026.1.0'),
        ]);
    }
}
