<?php

declare(strict_types=1);

return [
    'schema_version' => '2.0.0',
    'release_readiness' => 'lot_10_audited',
    'source' => 'README.md',
    'continuity_log' => 'docs/IMPLEMENTATION-LOTS.md',

    'official' => [
        ['id' => 1, 'key' => 'employee-cost', 'name' => 'Calculadora de Custo de Funcionário CLT', 'slug' => 'custo-funcionario-clt', 'module' => 'EmployeeCostCalculator', 'state' => 'implemented'],
        ['id' => 2, 'key' => 'factor-r', 'name' => 'Simulador de Fator R', 'slug' => 'simulador-fator-r', 'module' => 'FactorRSimulator', 'state' => 'implemented'],
        ['id' => 3, 'key' => 'late-das', 'name' => 'Calculadora de DAS em Atraso', 'slug' => 'das-em-atraso', 'module' => 'LateDasCalculator', 'state' => 'implemented'],
        ['id' => 4, 'key' => 'labor-charges', 'name' => 'Calculadora de Encargos Trabalhistas', 'slug' => 'encargos-trabalhistas', 'module' => 'LaborChargesCalculator', 'state' => 'implemented'],
        ['id' => 5, 'key' => 'employment-model', 'name' => 'Simulador CLT × PJ × Autônomo', 'slug' => 'comparador-clt-pj-autonomo', 'module' => 'EmploymentModelComparator', 'state' => 'implemented'],
        ['id' => 6, 'key' => 'employer-inss', 'name' => 'Calculadora de INSS Patronal', 'slug' => 'inss-patronal', 'module' => 'EmployerInssCalculator', 'state' => 'implemented'],
        ['id' => 7, 'key' => 'working-capital', 'name' => 'Calculadora de Capital de Giro', 'slug' => 'capital-de-giro', 'module' => 'WorkingCapitalCalculator', 'state' => 'implemented'],
        ['id' => 8, 'key' => 'cash-flow', 'name' => 'Calculadora de Fluxo de Caixa', 'slug' => 'fluxo-de-caixa', 'module' => 'CashFlowCalculator', 'state' => 'implemented'],
        ['id' => 9, 'key' => 'break-even', 'name' => 'Calculadora de Ponto de Equilíbrio', 'slug' => 'ponto-de-equilibrio', 'module' => 'BreakEvenCalculator', 'state' => 'implemented'],
        ['id' => 10, 'key' => 'product-pricing', 'name' => 'Calculadora de Precificação de Produtos', 'slug' => 'calculadora-margem-markup', 'module' => 'MarginMarkupCalculator', 'state' => 'implemented'],
        ['id' => 11, 'key' => 'pro-labore', 'name' => 'Simulador de Pró-Labore Ideal', 'slug' => 'simulador-pro-labore-ideal', 'module' => 'ProLaboreSimulator', 'state' => 'implemented'],
        ['id' => 12, 'key' => 'sales-commission', 'name' => 'Calculadora de Comissão de Vendedores', 'slug' => 'comissao-vendedores', 'module' => 'SalesCommissionCalculator', 'state' => 'implemented'],
        ['id' => 13, 'key' => 'payslip', 'name' => 'Gerador de Holerite', 'slug' => 'gerador-holerite', 'module' => 'PayslipGenerator', 'state' => 'implemented'],
        ['id' => 14, 'key' => 'admission', 'name' => 'Simulador de Admissão', 'slug' => 'simulador-admissao', 'module' => 'AdmissionSimulator', 'state' => 'implemented'],
        ['id' => 15, 'key' => 'termination', 'name' => 'Simulador de Demissão', 'slug' => 'calculadora-de-rescisao', 'module' => 'LaborTerminationCalculator', 'state' => 'implemented'],
        ['id' => 16, 'key' => 'salary-adjustment', 'name' => 'Calculadora de Reajuste Salarial', 'slug' => 'reajuste-salarial', 'module' => 'SalaryAdjustmentCalculator', 'state' => 'implemented'],
        ['id' => 17, 'key' => 'profit-distribution', 'name' => 'Calculadora de Distribuição de Lucros', 'slug' => 'distribuicao-de-lucros', 'module' => 'ProfitDistributionCalculator', 'state' => 'implemented'],
        ['id' => 18, 'key' => 'income-statement', 'name' => 'Gerador de Declaração de Rendimentos', 'slug' => 'declaracao-rendimentos', 'module' => 'IncomeStatementGenerator', 'state' => 'implemented'],
        ['id' => 19, 'key' => 'work-income-statement', 'name' => 'Gerador de Declaração de Trabalho/Renda', 'slug' => 'declaracao-trabalho-renda', 'module' => 'WorkIncomeStatementGenerator', 'state' => 'implemented'],
        ['id' => 20, 'key' => 'tax-regime', 'name' => 'Simulador Tributário (Simples × Lucro Presumido × Lucro Real)', 'slug' => 'comparador-tributario', 'module' => 'TaxRegimeComparator', 'state' => 'implemented'],
    ],

    'additional_modules' => [
        ['module' => 'ProLaboreProfitDistributionCalculator', 'classification' => 'legacy_compatibility', 'catalog_visibility' => 'preserve_until_migration_audit', 'replacement_modules' => ['ProLaboreSimulator', 'ProfitDistributionCalculator']],
        ['module' => 'AccountingFeesCalculator', 'classification' => 'complementary', 'catalog_visibility' => 'preserve'],
        ['module' => 'BusinessDocumentValidator', 'classification' => 'complementary', 'catalog_visibility' => 'preserve'],
        ['module' => 'ContractGenerator', 'classification' => 'complementary', 'catalog_visibility' => 'preserve'],
        ['module' => 'FederalPaymentGuideGenerator', 'classification' => 'complementary', 'catalog_visibility' => 'preserve'],
        ['module' => 'FiscalXmlConverter', 'classification' => 'complementary', 'catalog_visibility' => 'preserve'],
        ['module' => 'ReceiptIssuer', 'classification' => 'complementary', 'catalog_visibility' => 'preserve'],
        ['module' => 'SimplesNacionalCalculator', 'classification' => 'supporting_candidate', 'catalog_visibility' => 'preserve', 'review_in_lot' => 6],
        ['module' => 'VacationCalculator', 'classification' => 'complementary', 'catalog_visibility' => 'preserve'],
    ],
];
