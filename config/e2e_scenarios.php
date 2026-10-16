<?php

declare(strict_types=1);

use App\Core\Quality\E2E\Data\ToolDownloadExpectation;
use App\Core\Quality\E2E\Data\ToolScenario;

$productTools = require __DIR__.'/product_tools.php';
$tools = [];

foreach (($productTools['official'] ?? []) as $tool) {
    $slug = (string) ($tool['slug'] ?? '');
    if ($slug === '' || $slug === 'employee-cost-calculator') {
        continue;
    }

    $downloads = match ($slug) {
        'accounting-fee-calculator' => [
            new ToolDownloadExpectation(
                id: 'honorarios-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'honorarios-contabeis',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'honorarios-xlsx',
                testId: 'download-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'honorarios-contabeis',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'final-pay-severance-calculator' => [
            new ToolDownloadExpectation(
                id: 'rescisao-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'rescisao',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'rescisao-xlsx',
                testId: 'download-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'rescisao',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'overtime-pay-calculator' => [
            new ToolDownloadExpectation(
                id: 'hora-extra-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'hora-extra',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'hora-extra-xlsx',
                testId: 'download-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'hora-extra',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'take-home-pay-calculator' => [
            new ToolDownloadExpectation(
                id: 'salario-liquido-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'salario-liquido',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'salario-liquido-xlsx',
                testId: 'download-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'salario-liquido',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'pay-stub-generator' => [
            new ToolDownloadExpectation(
                id: 'holerite-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'holerite',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'holerite-xlsx',
                testId: 'download-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'holerite',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'margin-markup-calculator' => [
            new ToolDownloadExpectation(
                id: 'margem-markup-csv',
                testId: 'download-csv',
                format: 'csv',
                extension: 'csv',
                minimumBytes: 100,
                filenameContains: 'margem-markup',
                mimeType: 'text/csv',
            ),
            new ToolDownloadExpectation(
                id: 'margem-markup-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'margem-markup',
                mimeType: 'application/pdf',
            ),
        ],
        'us-tax-id-validator' => [
            new ToolDownloadExpectation(
                id: 'validador-documentos-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'validacao-documentos-lote',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'validador-documentos-csv',
                testId: 'download-csv',
                format: 'csv',
                extension: 'csv',
                minimumBytes: 80,
                filenameContains: 'validacao-documentos-completo',
                mimeType: 'text/csv',
            ),
        ],
        'owner-distribution-planner' => [
            new ToolDownloadExpectation(id: 'lucros-balanco-pdf', testId: 'download-pdf', format: 'pdf', extension: 'pdf', minimumBytes: 800, filenameContains: 'distribuicao-lucros-balanco', mimeType: 'application/pdf'),
            new ToolDownloadExpectation(id: 'lucros-balanco-xlsx', testId: 'download-xlsx', format: 'xlsx', extension: 'xlsx', minimumBytes: 1000, filenameContains: 'distribuicao-lucros-balanco', mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml']),
        ],
        'irs-payment-plan-calculator' => [
            new ToolDownloadExpectation(
                id: 'parcelamento-tributario-pdf', testId: 'download-pdf', format: 'pdf', extension: 'pdf', minimumBytes: 800,
                filenameContains: 'parcelamento-tributario', mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'parcelamento-tributario-xlsx', testId: 'download-xlsx', format: 'xlsx', extension: 'xlsx', minimumBytes: 1000,
                filenameContains: 'parcelamento-tributario', mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'asset-depreciation-calculator' => [
            new ToolDownloadExpectation(
                id: 'depreciacao-ativos-pdf', testId: 'download-pdf', format: 'pdf', extension: 'pdf', minimumBytes: 800,
                filenameContains: 'depreciacao-ativos', mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'depreciacao-ativos-xlsx', testId: 'download-xlsx', format: 'xlsx', extension: 'xlsx', minimumBytes: 1000,
                filenameContains: 'depreciacao-ativos', mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'backup-withholding-calculator' => [
            new ToolDownloadExpectation(
                id: 'retencoes-nota-pdf', testId: 'download-pdf', format: 'pdf', extension: 'pdf', minimumBytes: 800,
                filenameContains: 'retencoes-nota', mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'retencoes-nota-xlsx', testId: 'download-xlsx', format: 'xlsx', extension: 'xlsx', minimumBytes: 1000,
                filenameContains: 'retencoes-nota', mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        default => [],
    };

    $tools[$slug] = [
        new ToolScenario(
            id: 'fluxo-principal-valido',
            title: 'Executa o fluxo principal com preenchimento determinístico',
            kind: 'valid',
            toolSlug: $slug,
            tags: ['lot-10', 'regression', 'minimum-coverage'],
            steps: [
                ['action' => 'auto_fill_form', 'scope_test_id' => 'tool-form-panel'],
                ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
            ],
            expectations: [
                ['type' => 'url', 'contains' => '/ferramentas/'.$slug],
                ['type' => 'visible', 'test_id' => 'tool-form-panel'],
            ],
            downloads: $downloads,
        ),
        new ToolScenario(
            id: 'campo-obrigatorio-invalido',
            title: 'Impede o envio quando um campo obrigatório está inválido',
            kind: 'invalid',
            toolSlug: $slug,
            tags: ['lot-10', 'regression', 'minimum-coverage'],
            steps: [
                ['action' => 'auto_fill_form', 'scope_test_id' => 'tool-form-panel'],
                ['action' => 'invalidate_required', 'scope_test_id' => 'tool-form-panel'],
                ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
            ],
            expectations: [
                ['type' => 'form_invalid', 'test_id' => 'tool-form-panel'],
                ['type' => 'url', 'contains' => '/ferramentas/'.$slug],
            ],
        ),
    ];
}

$tools['us-tax-id-validator'] = [
    new ToolScenario(
        id: 'fluxo-principal-valido',
        title: 'Valida um documento e conclui o fluxo em lote com exportações reais',
        kind: 'valid',
        toolSlug: 'us-tax-id-validator',
        tags: ['lot-13', 'regression', 'minimum-coverage', 'downloads'],
        steps: [
            ['action' => 'auto_fill_form', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'auto_fill_form', 'scope_test_id' => 'batch-import-panel'],
            ['action' => 'submit', 'scope_test_id' => 'batch-import-panel'],
            ['action' => 'auto_fill_form', 'scope_test_id' => 'batch-process-form'],
            ['action' => 'submit', 'scope_test_id' => 'batch-process-form'],
        ],
        expectations: [
            ['type' => 'url', 'contains' => '/ferramentas/us-tax-id-validator'],
            ['type' => 'visible', 'test_id' => 'tool-result'],
            ['type' => 'visible', 'test_id' => 'download-actions'],
            ['type' => 'visible', 'test_id' => 'download-pdf'],
            ['type' => 'visible', 'test_id' => 'download-csv'],
        ],
        downloads: [
            new ToolDownloadExpectation(
                id: 'validador-documentos-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'validacao-documentos-lote',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'validador-documentos-csv',
                testId: 'download-csv',
                format: 'csv',
                extension: 'csv',
                minimumBytes: 80,
                filenameContains: 'validacao-documentos-completo',
                mimeType: 'text/csv',
            ),
        ],
    ),
    new ToolScenario(
        id: 'campo-obrigatorio-invalido',
        title: 'Impede o envio quando um campo obrigatório está inválido',
        kind: 'invalid',
        toolSlug: 'us-tax-id-validator',
        tags: ['lot-13', 'regression', 'minimum-coverage'],
        steps: [
            ['action' => 'auto_fill_form', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'invalidate_required', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
        ],
        expectations: [
            ['type' => 'form_invalid', 'test_id' => 'tool-form-panel'],
            ['type' => 'url', 'contains' => '/ferramentas/us-tax-id-validator'],
        ],
    ),
];


$tools['contract-generator'] = [
    new ToolScenario(
        id: 'fluxo-principal-valido',
        title: 'Seleciona a modalidade e executa o questionário principal',
        kind: 'valid',
        toolSlug: 'contract-generator',
        tags: ['lot-10', 'regression', 'minimum-coverage'],
        steps: [
            ['action' => 'click', 'test_id' => 'contract-type-service'],
            ['action' => 'auto_fill_form', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
        ],
        expectations: [
            ['type' => 'url', 'contains' => '/ferramentas/contract-generator'],
            ['type' => 'hidden', 'test_id' => 'tool-form-panel'],
            ['type' => 'visible', 'test_id' => 'contract-editor'],
            ['type' => 'in_viewport', 'test_id' => 'contract-editor'],
            ['type' => 'visible', 'test_id' => 'contract-preview'],
            ['type' => 'text', 'test_id' => 'contract-preview', 'value' => 'Teste E2E'],
            ['type' => 'visible', 'test_id' => 'contract-export-pdf'],
            ['type' => 'visible', 'test_id' => 'contract-export-xlsx'],
            ['type' => 'visible', 'test_id' => 'contract-export-docx'],
        ],
        downloads: [
            new ToolDownloadExpectation(
                id: 'contrato-pdf',
                testId: 'contract-export-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'contrato',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'contrato-xlsx',
                testId: 'contract-export-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'contrato',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
            new ToolDownloadExpectation(
                id: 'contrato-docx',
                testId: 'contract-export-docx',
                format: 'docx',
                extension: 'docx',
                minimumBytes: 1000,
                filenameContains: 'contrato',
                mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                requiredEntries: ['[Content_Types].xml', 'word/document.xml'],
            ),
        ],
    ),
    new ToolScenario(
        id: 'campo-obrigatorio-invalido',
        title: 'Impede o envio do questionário com campo obrigatório inválido',
        kind: 'invalid',
        toolSlug: 'contract-generator',
        tags: ['lot-10', 'regression', 'minimum-coverage'],
        steps: [
            ['action' => 'click', 'test_id' => 'contract-type-service'],
            ['action' => 'auto_fill_form', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'invalidate_required', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
        ],
        expectations: [
            ['type' => 'form_invalid', 'test_id' => 'tool-form-panel'],
            ['type' => 'url', 'contains' => '/ferramentas/contract-generator'],
        ],
    ),
];

$tools['employee-cost-calculator'] = [
    new ToolScenario(
        id: 'calculo-individual-valido',
        title: 'Calcula o custo individual com dados válidos',
        kind: 'valid',
        toolSlug: 'employee-cost-calculator',
        tags: ['pilot', 'regression', 'lot-10', 'minimum-coverage'],
        steps: [
            ['action' => 'fill', 'test_id' => 'field-salary', 'value' => '5000,00'],
            ['action' => 'fill', 'test_id' => 'field-variable-pay', 'value' => '0,00'],
            ['action' => 'fill', 'test_id' => 'field-benefits', 'value' => '800,00'],
            ['action' => 'select', 'test_id' => 'field-regime', 'value' => 'general'],
            ['action' => 'fill', 'test_id' => 'field-rat', 'value' => '1'],
            ['action' => 'fill', 'test_id' => 'field-third-parties', 'value' => '5.8'],
            ['action' => 'fill', 'test_id' => 'field-monthly-hours', 'value' => '220'],
            ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
        ],
        expectations: [
            ['type' => 'visible', 'test_id' => 'tool-result'],
            ['type' => 'url', 'contains' => '/ferramentas/employee-cost-calculator'],
        ],
        downloads: [
            new ToolDownloadExpectation(
                id: 'resultado-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'custo-funcionario',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'resultado-xlsx',
                testId: 'download-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'custo-funcionario',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
    ),
    new ToolScenario(
        id: 'salario-negativo-rejeitado',
        title: 'Rejeita salário negativo sem produzir resultado',
        kind: 'invalid',
        toolSlug: 'employee-cost-calculator',
        tags: ['pilot', 'regression', 'lot-10', 'minimum-coverage'],
        steps: [
            ['action' => 'fill', 'test_id' => 'field-salary', 'value' => '-1'],
            ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
        ],
        expectations: [
            ['type' => 'field_value', 'test_id' => 'field-salary', 'value' => '-1'],
            ['type' => 'hidden', 'test_id' => 'tool-result'],
        ],
    ),
];


$tools['sales-tax-calculator'] = [
    new ToolScenario(id: 'simulacao-valida', title: 'Simula transição tributária', kind: 'valid', toolSlug: 'sales-tax-calculator', tags: ['accounting-pains-lot-4','minimum-coverage'], steps: [['action'=>'auto_fill_form','scope_test_id'=>'tool-form-panel'],['action'=>'submit','scope_test_id'=>'tool-form-panel']], expectations: [['type'=>'visible','test_id'=>'tool-result']]),
    new ToolScenario(id: 'entrada-invalida', title: 'Rejeita entrada inválida', kind: 'invalid', toolSlug: 'sales-tax-calculator', tags: ['accounting-pains-lot-4','minimum-coverage'], steps: [['action'=>'auto_fill_form','scope_test_id'=>'tool-form-panel'],['action'=>'invalidate_required','scope_test_id'=>'tool-form-panel'],['action'=>'submit','scope_test_id'=>'tool-form-panel']], expectations: [['type'=>'form_invalid','test_id'=>'tool-form-panel']]),
];



ksort($tools);

return [
    'schema_version' => '1.2.0',
    'allowed_step_actions' => ['fill', 'select', 'check', 'uncheck', 'click', 'submit', 'auto_fill_form', 'invalidate_required'],
    'allowed_expectations' => ['visible', 'hidden', 'text', 'url', 'field_value', 'form_invalid', 'in_viewport'],
    'minimum_coverage' => [
        'expected_tool_count' => 28,
        'required_kinds' => ['valid', 'invalid'],
    ],
    'tools' => $tools,
    'runtime' => [
        'scenario_manifest' => 'storage/app/e2e/runtime/tool-scenarios.json',
    ],
];
