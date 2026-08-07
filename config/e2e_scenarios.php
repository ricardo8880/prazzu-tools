<?php

declare(strict_types=1);

use App\Core\Quality\E2E\Data\ToolDownloadExpectation;
use App\Core\Quality\E2E\Data\ToolScenario;

$productTools = require __DIR__.'/product_tools.php';
$tools = [];

foreach (($productTools['official'] ?? []) as $tool) {
    $slug = (string) ($tool['slug'] ?? '');
    if ($slug === '' || $slug === 'custo-funcionario-clt') {
        continue;
    }

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


$tools['gerador-de-contratos'] = [
    new ToolScenario(
        id: 'fluxo-principal-valido',
        title: 'Seleciona a modalidade e executa o questionário principal',
        kind: 'valid',
        toolSlug: 'gerador-de-contratos',
        tags: ['lot-10', 'regression', 'minimum-coverage'],
        steps: [
            ['action' => 'click', 'test_id' => 'contract-type-service'],
            ['action' => 'auto_fill_form', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
        ],
        expectations: [
            ['type' => 'url', 'contains' => '/ferramentas/gerador-de-contratos'],
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
        toolSlug: 'gerador-de-contratos',
        tags: ['lot-10', 'regression', 'minimum-coverage'],
        steps: [
            ['action' => 'click', 'test_id' => 'contract-type-service'],
            ['action' => 'auto_fill_form', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'invalidate_required', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
        ],
        expectations: [
            ['type' => 'form_invalid', 'test_id' => 'tool-form-panel'],
            ['type' => 'url', 'contains' => '/ferramentas/gerador-de-contratos'],
        ],
    ),
];

$tools['custo-funcionario-clt'] = [
    new ToolScenario(
        id: 'calculo-individual-valido',
        title: 'Calcula o custo individual com dados válidos',
        kind: 'valid',
        toolSlug: 'custo-funcionario-clt',
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
            ['type' => 'url', 'contains' => '/ferramentas/custo-funcionario-clt'],
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
        toolSlug: 'custo-funcionario-clt',
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

ksort($tools);

return [
    'schema_version' => '1.2.0',
    'allowed_step_actions' => ['fill', 'select', 'check', 'uncheck', 'click', 'submit', 'auto_fill_form', 'invalidate_required'],
    'allowed_expectations' => ['visible', 'hidden', 'text', 'url', 'field_value', 'form_invalid', 'in_viewport'],
    'minimum_coverage' => [
        'expected_tool_count' => 32,
        'required_kinds' => ['valid', 'invalid'],
    ],
    'tools' => $tools,
    'runtime' => [
        'scenario_manifest' => 'storage/app/e2e/runtime/tool-scenarios.json',
    ],
];
