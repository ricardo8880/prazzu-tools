<?php

declare(strict_types=1);

namespace App\Tools\SalaryAdjustmentCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public static function suite(): GoldenCaseSuite
    {
        $reference = 'CalculatorTest and the versioned calculation memory for the U.S. salary-increase calculator.';
        return new GoldenCaseSuite(toolSlug:'salary-increase-calculator', cases:[
            new GoldenCase('typical','Five percent salary increase',GoldenCaseKind::Typical,['current_salary'=>'3500.00','rate'=>'5'],['outcome'=>'calculated'],$reference,'1.1.0','Money in integer cents.'),
            new GoldenCase('boundary','Zero percent increase',GoldenCaseKind::Boundary,['current_salary'=>'3500.00','rate'=>'0'],['outcome'=>'calculated'],$reference,'1.1.0'),
            new GoldenCase('invalid-input','Invalid salary rejected',GoldenCaseKind::InvalidInput,['current_salary'=>'invalid'],['outcome'=>'validation-error'],$reference,'1.1.0'),
            new GoldenCase('rounding','Fractional percentage rounds to cents',GoldenCaseKind::Rounding,['current_salary'=>'3333.33','rate'=>'2.75'],['outcome'=>'integer-cents'],$reference,'1.1.0','Money in integer cents; percentage in basis points.'),
            new GoldenCase('non-applicable','No statutory increase is inferred',GoldenCaseKind::NonApplicable,['rate'=>null],['outcome'=>'user-parameter'],$reference,'1.1.0'),
            new GoldenCase('normative-transition','Future policy change requires user input',GoldenCaseKind::NormativeTransition,['year'=>2027],['outcome'=>'verify-input'],$reference,'1.1.0'),
            new GoldenCase('regression','Known salary projection remains stable',GoldenCaseKind::Regression,['current_salary'=>'5000.00','rate'=>'4'],['outcome'=>'calculated'],$reference,'1.1.0','Money in integer cents.'),
        ]);
    }
}
