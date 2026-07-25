<?php

use App\Tools\MarginMarkupCalculator\Tool;

return [
    'general' => [
        Tool::class,
        App\Tools\BusinessDocumentValidator\Tool::class,
        App\Tools\WorkingCapitalCalculator\Tool::class,
        App\Tools\CashFlowCalculator\Tool::class,
        App\Tools\BreakEvenCalculator\Tool::class,
        App\Tools\SalesCommissionCalculator\Tool::class,
        // <tools:general>
    ],
    'fiscal' => [
        App\Tools\SimplesNacionalCalculator\Tool::class,
        App\Tools\TaxRegimeComparator\Tool::class,
        App\Tools\ProLaboreProfitDistributionCalculator\Tool::class,
        App\Tools\FiscalXmlConverter\Tool::class,
        App\Tools\FederalPaymentGuideGenerator\Tool::class,
        App\Tools\FactorRSimulator\Tool::class,
        App\Tools\LateDasCalculator\Tool::class,
        // <tools:fiscal>
    ],
    'labor' => [
        App\Tools\LaborTerminationCalculator\Tool::class,
        App\Tools\VacationCalculator\Tool::class,
        App\Tools\EmployeeCostCalculator\Tool::class,
        App\Tools\LaborChargesCalculator\Tool::class,
        App\Tools\EmploymentModelComparator\Tool::class,
        App\Tools\EmployerInssCalculator\Tool::class,
        App\Tools\AdmissionSimulator\Tool::class,
        App\Tools\SalaryAdjustmentCalculator\Tool::class,
        // <tools:labor>
    ],
    'corporate' => [
        App\Tools\AccountingFeesCalculator\Tool::class,
        // <tools:corporate>
    ],
    'documents' => [
        App\Tools\ReceiptIssuer\Tool::class,
        App\Tools\ContractGenerator\Tool::class,
        App\Tools\PayslipGenerator\Tool::class,
        App\Tools\IncomeStatementGenerator\Tool::class,
        App\Tools\WorkIncomeStatementGenerator\Tool::class,
        // <tools:documents>
    ],
];
