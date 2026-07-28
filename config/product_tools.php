<?php

declare(strict_types=1);

return [
    'schema_version' => '3.3.0',
    'release_readiness' => 'surgical_lot_4_catalog_sanitized',
    'source' => 'README.md',
    'continuity_log' => 'docs/IMPLEMENTATION-LOTS.md',
    'inventory_document' => 'docs/PRODUCT-TOOLS-INVENTORY.md',
    'expected_module_count' => 32,

    'official' => [
        ['id' => 1, 'key' => 'employee-cost', 'name' => 'Calculadora de Custo de Funcionário CLT', 'slug' => 'custo-funcionario-clt', 'module' => 'EmployeeCostCalculator', 'state' => 'implemented', 'release_order' => 10],
        ['id' => 2, 'key' => 'factor-r', 'name' => 'Simulador de Fator R', 'slug' => 'simulador-fator-r', 'module' => 'FactorRSimulator', 'state' => 'implemented', 'release_order' => 20],
        ['id' => 3, 'key' => 'late-das', 'name' => 'Calculadora de DAS em Atraso', 'slug' => 'das-em-atraso', 'module' => 'LateDasCalculator', 'state' => 'implemented', 'release_order' => 21],
        ['id' => 4, 'key' => 'labor-charges', 'name' => 'Calculadora de Encargos Trabalhistas', 'slug' => 'encargos-trabalhistas', 'module' => 'LaborChargesCalculator', 'state' => 'implemented', 'release_order' => 11],
        ['id' => 5, 'key' => 'employment-model', 'name' => 'Simulador CLT × PJ × Autônomo', 'slug' => 'comparador-clt-pj-autonomo', 'module' => 'EmploymentModelComparator', 'state' => 'implemented', 'release_order' => 13],
        ['id' => 6, 'key' => 'employer-inss', 'name' => 'Calculadora de INSS Patronal', 'slug' => 'inss-patronal', 'module' => 'EmployerInssCalculator', 'state' => 'implemented', 'release_order' => 12],
        ['id' => 7, 'key' => 'working-capital', 'name' => 'Calculadora de Capital de Giro', 'slug' => 'capital-de-giro', 'module' => 'WorkingCapitalCalculator', 'state' => 'implemented', 'release_order' => 23],
        ['id' => 8, 'key' => 'cash-flow', 'name' => 'Calculadora de Fluxo de Caixa', 'slug' => 'fluxo-de-caixa', 'module' => 'CashFlowCalculator', 'state' => 'implemented', 'release_order' => 24],
        ['id' => 9, 'key' => 'break-even', 'name' => 'Calculadora de Ponto de Equilíbrio', 'slug' => 'ponto-de-equilibrio', 'module' => 'BreakEvenCalculator', 'state' => 'implemented', 'release_order' => 25],
        ['id' => 10, 'key' => 'product-pricing', 'name' => 'Calculadora de Precificação de Produtos', 'slug' => 'calculadora-margem-markup', 'module' => 'MarginMarkupCalculator', 'state' => 'implemented', 'release_order' => 26],
        ['id' => 11, 'key' => 'pro-labore', 'name' => 'Simulador de Pró-Labore Ideal', 'slug' => 'simulador-pro-labore-ideal', 'module' => 'ProLaboreSimulator', 'state' => 'implemented', 'release_order' => 14],
        ['id' => 12, 'key' => 'sales-commission', 'name' => 'Calculadora de Comissão de Vendedores', 'slug' => 'comissao-vendedores', 'module' => 'SalesCommissionCalculator', 'state' => 'implemented', 'release_order' => 27],
        ['id' => 13, 'key' => 'payslip', 'name' => 'Gerador de Holerite', 'slug' => 'gerador-holerite', 'module' => 'PayslipGenerator', 'state' => 'implemented', 'release_order' => 16],
        ['id' => 14, 'key' => 'admission', 'name' => 'Simulador de Admissão', 'slug' => 'simulador-admissao', 'module' => 'AdmissionSimulator', 'state' => 'implemented', 'release_order' => 17],
        ['id' => 15, 'key' => 'termination', 'name' => 'Simulador de Demissão', 'slug' => 'calculadora-de-rescisao', 'module' => 'LaborTerminationCalculator', 'state' => 'implemented', 'release_order' => 18],
        ['id' => 16, 'key' => 'salary-adjustment', 'name' => 'Calculadora de Reajuste Salarial', 'slug' => 'reajuste-salarial', 'module' => 'SalaryAdjustmentCalculator', 'state' => 'implemented', 'release_order' => 19],
        ['id' => 17, 'key' => 'profit-distribution', 'name' => 'Calculadora de Distribuição de Lucros', 'slug' => 'distribuicao-de-lucros', 'module' => 'ProfitDistributionCalculator', 'state' => 'implemented', 'release_order' => 15],
        ['id' => 18, 'key' => 'income-statement', 'name' => 'Gerador de Declaração de Rendimentos', 'slug' => 'declaracao-rendimentos', 'module' => 'IncomeStatementGenerator', 'state' => 'implemented', 'release_order' => 28],
        ['id' => 19, 'key' => 'work-income-statement', 'name' => 'Gerador de Declaração de Trabalho/Renda', 'slug' => 'declaracao-trabalho-renda', 'module' => 'WorkIncomeStatementGenerator', 'state' => 'implemented', 'release_order' => 29],
        ['id' => 20, 'key' => 'tax-regime', 'name' => 'Simulador Tributário (Simples × Lucro Presumido × Lucro Real)', 'slug' => 'comparador-tributario', 'module' => 'TaxRegimeComparator', 'state' => 'implemented', 'release_order' => 22],
        ['id' => 21, 'key' => 'net-salary', 'name' => 'Calculadora de Salário Líquido', 'slug' => 'calculadora-salario-liquido', 'module' => 'NetSalaryCalculator', 'state' => 'implemented', 'release_order' => 30],
        ['id' => 22, 'key' => 'overtime', 'name' => 'Calculadora de Hora Extra, Adicional Noturno e DSR', 'slug' => 'calculadora-hora-extra', 'module' => 'OvertimeCalculator', 'state' => 'implemented', 'release_order' => 31],
        ['id' => 23, 'key' => 'difal-icms', 'name' => 'Calculadora DIFAL / ICMS Interestadual + FCP', 'slug' => 'calculadora-difal-icms', 'module' => 'DifalIcmsCalculator', 'state' => 'implemented', 'release_order' => 32],
        ['id' => 24, 'key' => 'partner-withdrawal-planner', 'name' => 'Planejador de Retirada de Sócios', 'slug' => 'calculadora-pro-labore-distribuicao-lucros', 'module' => 'ProLaboreProfitDistributionCalculator', 'state' => 'implemented', 'release_order' => 1],
        ['id' => 25, 'key' => 'accounting-fees', 'name' => 'Calculadora de Honorários Contábeis', 'slug' => 'calculadora-de-honorarios-contabeis', 'module' => 'AccountingFeesCalculator', 'state' => 'implemented', 'release_order' => 2],
        ['id' => 26, 'key' => 'business-document-validator', 'name' => 'Validador Inteligente de CNPJ, CPF e IE', 'slug' => 'validador-de-cnpj', 'module' => 'BusinessDocumentValidator', 'state' => 'implemented', 'release_order' => 3],
        ['id' => 27, 'key' => 'contract-generator', 'name' => 'Gerador de Contratos', 'slug' => 'gerador-de-contratos', 'module' => 'ContractGenerator', 'state' => 'implemented', 'release_order' => 4],
        ['id' => 28, 'key' => 'federal-payment-guide', 'name' => 'Gerador Inteligente de DARF/GPS', 'slug' => 'gerador-darf-gps', 'module' => 'FederalPaymentGuideGenerator', 'state' => 'implemented', 'release_order' => 5],
        ['id' => 29, 'key' => 'fiscal-xml-converter', 'name' => 'Conversor Fiscal de XML', 'slug' => 'conversor-fiscal-xml', 'module' => 'FiscalXmlConverter', 'state' => 'implemented', 'release_order' => 6],
        ['id' => 30, 'key' => 'receipt-issuer', 'name' => 'Emissor de Recibos', 'slug' => 'emissor-de-recibos', 'module' => 'ReceiptIssuer', 'state' => 'implemented', 'release_order' => 7],
        ['id' => 31, 'key' => 'simples-nacional', 'name' => 'Calculadora de Simples Nacional', 'slug' => 'calculadora-simples-nacional', 'module' => 'SimplesNacionalCalculator', 'state' => 'implemented', 'release_order' => 8],
        ['id' => 32, 'key' => 'vacation', 'name' => 'Calculadora de Férias', 'slug' => 'calculadora-ferias', 'module' => 'VacationCalculator', 'state' => 'implemented', 'release_order' => 9],
    ],

    'functional_overlap_reviews' => [
        [
            'module' => 'ProLaboreProfitDistributionCalculator',
            'classification' => 'resolved_distinct_planning_scope',
            'state' => 'resolved',
            'related_modules' => ['ProLaboreSimulator', 'ProfitDistributionCalculator'],
            'distinction' => 'Os módulos especializados calculam cada domínio isoladamente; este módulo consolida a composição da retirada, o total líquido do sócio, o custo empresarial e o lucro remanescente em cenários comparáveis.',
        ],
    ],
];
