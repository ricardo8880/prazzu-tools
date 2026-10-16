<?php

declare(strict_types=1);

return [
    'schema_version' => '3.29.0',
    'release_readiness' => 'beta_activation_lot_2_low_risk_hardened',
    'source' => 'README.md',
    'continuity_log' => 'docs/IMPLEMENTATION-LOTS.md',
    'inventory_document' => 'docs/PRODUCT-TOOLS-INVENTORY.md',
    'expected_module_count' => 28,

    'official' => [
        ['id' => 1, 'key' => 'employee-cost', 'name' => 'Employee Cost Calculator', 'slug' => 'employee-cost-calculator', 'module' => 'EmployeeCostCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 6],
        ['id' => 2, 'key' => 'labor-charges', 'name' => 'Employer Payroll Tax Calculator', 'slug' => 'employer-payroll-tax-calculator', 'module' => 'LaborChargesCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 7],
        ['id' => 3, 'key' => 'employment-model', 'name' => 'W-2 vs 1099 Contractor Calculator', 'slug' => 'employee-vs-contractor-calculator', 'module' => 'EmploymentModelComparator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 8],
        ['id' => 4, 'key' => 'working-capital', 'name' => 'Working Capital Calculator', 'slug' => 'working-capital-calculator', 'module' => 'WorkingCapitalCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 14],
        ['id' => 5, 'key' => 'cash-flow', 'name' => 'Cash Flow Calculator', 'slug' => 'cash-flow-calculator', 'module' => 'CashFlowCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 15],
        ['id' => 6, 'key' => 'break-even', 'name' => 'Break-Even Calculator', 'slug' => 'break-even-calculator', 'module' => 'BreakEvenCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 16],
        ['id' => 7, 'key' => 'product-pricing', 'name' => 'Margin & Markup Calculator', 'slug' => 'margin-markup-calculator', 'module' => 'MarginMarkupCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 17],
        ['id' => 8, 'key' => 'sales-commission', 'name' => 'Sales Commission Calculator', 'slug' => 'sales-commission-calculator', 'module' => 'SalesCommissionCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 18],
        ['id' => 9, 'key' => 'payslip', 'name' => 'Pay Stub Generator', 'slug' => 'pay-stub-generator', 'module' => 'PayslipGenerator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 10],
        ['id' => 10, 'key' => 'admission', 'name' => 'New Hire Cost Calculator', 'slug' => 'new-hire-cost-calculator', 'module' => 'AdmissionSimulator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 11],
        ['id' => 11, 'key' => 'termination', 'name' => 'Final Pay & Severance Calculator', 'slug' => 'final-pay-severance-calculator', 'module' => 'LaborTerminationCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 12],
        ['id' => 12, 'key' => 'salary-adjustment', 'name' => 'Salary Increase Calculator', 'slug' => 'salary-increase-calculator', 'module' => 'SalaryAdjustmentCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 13],
        ['id' => 13, 'key' => 'profit-distribution', 'name' => 'Owner Distribution Calculator', 'slug' => 'owner-distribution-calculator', 'module' => 'ProfitDistributionCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 9],
        ['id' => 14, 'key' => 'income-statement', 'name' => 'Earnings Statement Generator', 'slug' => 'earnings-statement-generator', 'module' => 'IncomeStatementGenerator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 19],
        ['id' => 15, 'key' => 'work-income-statement', 'name' => 'Employment Income Letter Generator', 'slug' => 'employment-income-letter-generator', 'module' => 'WorkIncomeStatementGenerator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 20],
        ['id' => 16, 'key' => 'net-salary', 'name' => 'Take-Home Pay Calculator', 'slug' => 'take-home-pay-calculator', 'module' => 'NetSalaryCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 21],
        ['id' => 17, 'key' => 'overtime', 'name' => 'Overtime Pay Calculator', 'slug' => 'overtime-pay-calculator', 'module' => 'OvertimeCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 22],
        ['id' => 18, 'key' => 'accounting-fees', 'name' => 'Accounting Fee Calculator', 'slug' => 'accounting-fee-calculator', 'module' => 'AccountingFeesCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 1],
        ['id' => 19, 'key' => 'business-document-validator', 'name' => 'EIN Format Validator', 'slug' => 'us-tax-id-validator', 'module' => 'BusinessDocumentValidator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 2],
        ['id' => 20, 'key' => 'contract-generator', 'name' => 'Contract Generator', 'slug' => 'contract-generator', 'module' => 'ContractGenerator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 3],
        ['id' => 21, 'key' => 'receipt-issuer', 'name' => 'Receipt Generator', 'slug' => 'receipt-generator', 'module' => 'ReceiptIssuer', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 4],
        ['id' => 22, 'key' => 'vacation', 'name' => 'PTO Calculator', 'slug' => 'pto-calculator', 'module' => 'VacationCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 5],
        ['id' => 23, 'key' => 'turnover', 'name' => 'Employee Turnover Calculator', 'slug' => 'employee-turnover-calculator', 'module' => 'TurnoverCalculator', 'state' => 'implemented', 'vertical' => 'rh', 'release_order' => 23],
        ['id' => 24, 'key' => 'invoice-withholding', 'name' => 'Backup Withholding Calculator', 'slug' => 'backup-withholding-calculator', 'module' => 'InvoiceWithholdingCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 24],
        ['id' => 25, 'key' => 'asset-depreciation', 'name' => 'Asset Depreciation Calculator', 'slug' => 'asset-depreciation-calculator', 'module' => 'AssetDepreciationCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 25],
        ['id' => 26, 'key' => 'tax-installment', 'name' => 'IRS Payment Plan Calculator', 'slug' => 'irs-payment-plan-calculator', 'module' => 'TaxInstallmentCalculator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 26],
        ['id' => 27, 'key' => 'profit-distribution-balance-comparison', 'name' => 'Owner Distribution Planner', 'slug' => 'owner-distribution-planner', 'module' => 'ProfitDistributionBalanceSimulator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 27],
        ['id' => 28, 'key' => 'tax-reform-consumption', 'name' => 'Sales Tax Calculator', 'slug' => 'sales-tax-calculator', 'module' => 'TaxReformSimulator', 'state' => 'implemented', 'vertical' => 'contabilidade', 'release_order' => 28],
    ],

    'functional_overlap_reviews' => [
        [
            'module' => 'ProfitDistributionBalanceSimulator',
            'classification' => 'resolved_distinct_balance_comparison_scope',
            'state' => 'resolved',
            'related_modules' => ['ProfitDistributionCalculator'],
            'distinction' => 'A calculadora histórica apura distribuição societária de um lucro contábil; o novo simulador compara explicitamente um cenário com balanço contra uma referência parametrizada sem balanço e projeta os dois cenários.',
        ],
    ],
];
