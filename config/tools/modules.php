<?php

use App\Tools\MarginMarkupCalculator\Tool;

return [
    'general' => [
        Tool::class,
        App\Tools\BusinessDocumentValidator\Tool::class,
        App\Tools\WorkingCapitalCalculator\Tool::class,
        App\Tools\CashFlowCalculator\Tool::class,
        App\Tools\BreakEvenCalculator\Tool::class,
        App\Tools\AssetDepreciationCalculator\Tool::class,
        App\Tools\SalesCommissionCalculator\Tool::class,
        // <tools:general>
    ],
    'fiscal' => [
        App\Tools\SimplesNacionalCalculator\Tool::class,
        App\Tools\TaxRegimeComparator\Tool::class,
        App\Tools\ProLaboreSimulator\Tool::class,
        App\Tools\ProfitDistributionCalculator\Tool::class,
        App\Tools\ProLaboreProfitDistributionCalculator\Tool::class, // compatibilidade temporária
        App\Tools\FiscalXmlConverter\Tool::class,
        App\Tools\FederalPaymentGuideGenerator\Tool::class,
        App\Tools\FactorRSimulator\Tool::class,
        App\Tools\LateDasCalculator\Tool::class,
        App\Tools\TaxInstallmentCalculator\Tool::class,
        App\Tools\MeiToMicroenterpriseSimulator\Tool::class,
        App\Tools\IssCalculator\Tool::class,
        App\Tools\ProfitDistributionBalanceSimulator\Tool::class,
        App\Tools\RetroactiveDasRegularizationCalculator\Tool::class,
        App\Tools\DifalIcmsCalculator\Tool::class,
        App\Tools\PresumedProfitIrpjCsllCalculator\Tool::class,
        App\Tools\PisCofinsCalculator\Tool::class,
        App\Tools\IcmsStCalculator\Tool::class,
        App\Tools\InvoiceWithholdingCalculator\Tool::class,
        \App\Tools\DigitalCertificateAnalyzer\Tool::class,
        App\Tools\CfopAdvisor\Tool::class,
        App\Tools\SefazFiscalValidator\Tool::class,
        App\Tools\IcmsCalculator\Tool::class,
        App\Tools\ActualProfitCalculator\Tool::class,
        App\Tools\TaxReformSimulator\Tool::class,
        App\Tools\EcadRoyaltySimulator\Tool::class,
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
        App\Tools\NetSalaryCalculator\Tool::class,
        App\Tools\OvertimeCalculator\Tool::class,
        App\Tools\TurnoverCalculator\Tool::class,
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
