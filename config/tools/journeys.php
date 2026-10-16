<?php

return [
    'take-home-pay-calculator' => ['employee-cost-calculator', 'overtime-pay-calculator', 'pto-calculator', 'final-pay-severance-calculator'],
    'employee-cost-calculator' => ['take-home-pay-calculator', 'employer-payroll-tax-calculator', 'new-hire-cost-calculator'],
    'overtime-pay-calculator' => ['take-home-pay-calculator', 'employee-cost-calculator', 'pto-calculator', 'pay-stub-generator'],
    'pto-calculator' => ['take-home-pay-calculator', 'final-pay-severance-calculator', 'pay-stub-generator', 'employee-cost-calculator'],
    'final-pay-severance-calculator' => ['pto-calculator', 'take-home-pay-calculator', 'employee-cost-calculator', 'pay-stub-generator'],
    'new-hire-cost-calculator' => ['employee-cost-calculator', 'take-home-pay-calculator', 'employer-payroll-tax-calculator'],
    'employer-payroll-tax-calculator' => ['employee-cost-calculator', 'take-home-pay-calculator', 'new-hire-cost-calculator'],
    'salary-increase-calculator' => ['take-home-pay-calculator', 'employee-cost-calculator', 'pay-stub-generator', 'new-hire-cost-calculator'],
    'pay-stub-generator' => ['take-home-pay-calculator', 'overtime-pay-calculator', 'pto-calculator', 'employee-cost-calculator'],
    'sales-commission-calculator' => ['pay-stub-generator', 'take-home-pay-calculator', 'employee-cost-calculator', 'salary-increase-calculator'],
    'employee-vs-contractor-calculator' => ['employee-cost-calculator', 'take-home-pay-calculator'],

    'backup-withholding-calculator' => [],
    'sales-tax-calculator' => [],
    'irs-payment-plan-calculator' => [],

    'owner-distribution-calculator' => ['owner-distribution-planner', 'earnings-statement-generator'],
    'owner-distribution-planner' => ['owner-distribution-calculator', 'earnings-statement-generator'],
    'earnings-statement-generator' => ['owner-distribution-calculator', 'receipt-generator', 'contract-generator'],
    'employment-income-letter-generator' => ['earnings-statement-generator', 'receipt-generator', 'contract-generator', 'take-home-pay-calculator'],

    'working-capital-calculator' => ['cash-flow-calculator', 'break-even-calculator', 'margin-markup-calculator', 'accounting-fee-calculator'],
    'cash-flow-calculator' => ['working-capital-calculator', 'break-even-calculator', 'margin-markup-calculator', 'accounting-fee-calculator'],
    'break-even-calculator' => ['margin-markup-calculator', 'cash-flow-calculator', 'working-capital-calculator', 'accounting-fee-calculator'],
    'margin-markup-calculator' => ['break-even-calculator', 'cash-flow-calculator', 'working-capital-calculator', 'accounting-fee-calculator'],
    'accounting-fee-calculator' => ['margin-markup-calculator', 'break-even-calculator', 'cash-flow-calculator', 'working-capital-calculator'],
    'asset-depreciation-calculator' => ['cash-flow-calculator', 'break-even-calculator', 'working-capital-calculator'],

    'us-tax-id-validator' => ['contract-generator', 'receipt-generator', 'earnings-statement-generator'],
    'contract-generator' => ['us-tax-id-validator', 'receipt-generator', 'earnings-statement-generator', 'employment-income-letter-generator'],
    'receipt-generator' => ['contract-generator', 'earnings-statement-generator', 'employment-income-letter-generator', 'us-tax-id-validator'],

    // A vertical de RH possui hoje uma única ferramenta oficial. O catálogo não
    // cruza verticais automaticamente; quando novos módulos de RH entrarem, este
    // fluxo pode ser enriquecido sem alterar a heurística global.
    'employee-turnover-calculator' => [],
];
