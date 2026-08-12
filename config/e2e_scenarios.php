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

    $downloads = match ($slug) {
        'calculadora-de-honorarios-contabeis' => [
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
        'calculadora-simples-nacional' => [
            new ToolDownloadExpectation(
                id: 'simples-nacional-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'simples-nacional',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'simples-nacional-xlsx',
                testId: 'download-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'simples-nacional',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'calculadora-de-rescisao' => [
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
        'calculadora-hora-extra' => [
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
        'calculadora-salario-liquido' => [
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
        'gerador-holerite' => [
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
        'calculadora-margem-markup' => [
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
        'comparador-tributario' => [
            new ToolDownloadExpectation(
                id: 'comparador-tributario-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'comparacao-tributaria',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'comparador-tributario-xlsx',
                testId: 'download-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'comparacao-tributaria',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'validador-de-cnpj' => [
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
        'calculadora-das-retroativo-regularizacao-simples' => [
            new ToolDownloadExpectation(id: 'das-retroativo-pdf', testId: 'download-pdf', format: 'pdf', extension: 'pdf', minimumBytes: 800, filenameContains: 'das-retroativo-regularizacao-simples', mimeType: 'application/pdf'),
            new ToolDownloadExpectation(id: 'das-retroativo-xlsx', testId: 'download-xlsx', format: 'xlsx', extension: 'xlsx', minimumBytes: 1000, filenameContains: 'das-retroativo-regularizacao-simples', mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml']),
        ],
        'simulador-distribuicao-lucros-balanco' => [
            new ToolDownloadExpectation(id: 'lucros-balanco-pdf', testId: 'download-pdf', format: 'pdf', extension: 'pdf', minimumBytes: 800, filenameContains: 'distribuicao-lucros-balanco', mimeType: 'application/pdf'),
            new ToolDownloadExpectation(id: 'lucros-balanco-xlsx', testId: 'download-xlsx', format: 'xlsx', extension: 'xlsx', minimumBytes: 1000, filenameContains: 'distribuicao-lucros-balanco', mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml']),
        ],
        'calculadora-iss' => [
            new ToolDownloadExpectation(id: 'calculadora-iss-pdf', testId: 'download-pdf', format: 'pdf', extension: 'pdf', minimumBytes: 800, filenameContains: 'calculadora-iss', mimeType: 'application/pdf'),
            new ToolDownloadExpectation(id: 'calculadora-iss-xlsx', testId: 'download-xlsx', format: 'xlsx', extension: 'xlsx', minimumBytes: 1000, filenameContains: 'calculadora-iss', mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml']),
        ],
        'simulador-mei-microempresa' => [
            new ToolDownloadExpectation(
                id: 'simulador-mei-microempresa-pdf', testId: 'download-pdf', format: 'pdf', extension: 'pdf', minimumBytes: 800,
                filenameContains: 'simulacao-mei-microempresa', mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'simulador-mei-microempresa-xlsx', testId: 'download-xlsx', format: 'xlsx', extension: 'xlsx', minimumBytes: 1000,
                filenameContains: 'simulacao-mei-microempresa', mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'calculadora-parcelamento-tributario' => [
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
        'calculadora-depreciacao-ativos' => [
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
        'calculadora-retencoes-nota-fiscal' => [
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
        'calculadora-icms-st' => [
            new ToolDownloadExpectation(
                id: 'icms-st-pdf', testId: 'download-pdf', format: 'pdf', extension: 'pdf', minimumBytes: 800,
                filenameContains: 'icms-st', mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'icms-st-xlsx', testId: 'download-xlsx', format: 'xlsx', extension: 'xlsx', minimumBytes: 1000,
                filenameContains: 'icms-st', mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'calculadora-pis-cofins' => [
            new ToolDownloadExpectation(
                id: 'pis-cofins-pdf', testId: 'download-pdf', format: 'pdf', extension: 'pdf', minimumBytes: 800,
                filenameContains: 'pis-cofins', mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'pis-cofins-xlsx', testId: 'download-xlsx', format: 'xlsx', extension: 'xlsx', minimumBytes: 1000,
                filenameContains: 'pis-cofins', mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'calculadora-irpj-csll-lucro-presumido' => [
            new ToolDownloadExpectation(
                id: 'irpj-csll-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'irpj-csll-lucro-presumido',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'irpj-csll-xlsx',
                testId: 'download-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'irpj-csll-lucro-presumido',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
        'conversor-fiscal-xml' => [
            new ToolDownloadExpectation(
                id: 'xml-fiscal-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'xml-fiscal',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'xml-fiscal-xlsx',
                testId: 'download-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'xml-fiscal',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
            new ToolDownloadExpectation(
                id: 'xml-fiscal-csv',
                testId: 'download-csv',
                format: 'csv',
                extension: 'csv',
                minimumBytes: 100,
                filenameContains: 'xml-fiscal',
                mimeType: 'text/csv',
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



$tools['validador-de-cnpj'] = [
    new ToolScenario(
        id: 'fluxo-principal-valido',
        title: 'Valida um documento e conclui o fluxo em lote com exportações reais',
        kind: 'valid',
        toolSlug: 'validador-de-cnpj',
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
            ['type' => 'url', 'contains' => '/ferramentas/validador-de-cnpj'],
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
        toolSlug: 'validador-de-cnpj',
        tags: ['lot-13', 'regression', 'minimum-coverage'],
        steps: [
            ['action' => 'auto_fill_form', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'invalidate_required', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
        ],
        expectations: [
            ['type' => 'form_invalid', 'test_id' => 'tool-form-panel'],
            ['type' => 'url', 'contains' => '/ferramentas/validador-de-cnpj'],
        ],
    ),
];

$tools['calculadora-difal-icms'] = [
    new ToolScenario(
        id: 'fluxo-principal-valido',
        title: 'Calcula o DIFAL e valida as exportações do resultado',
        kind: 'valid',
        toolSlug: 'calculadora-difal-icms',
        tags: ['lot-13', 'regression', 'minimum-coverage', 'downloads'],
        steps: [
            ['action' => 'auto_fill_form', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
        ],
        expectations: [
            ['type' => 'url', 'contains' => '/ferramentas/calculadora-difal-icms'],
            ['type' => 'visible', 'test_id' => 'tool-result'],
            ['type' => 'visible', 'test_id' => 'download-actions'],
            ['type' => 'visible', 'test_id' => 'download-pdf'],
            ['type' => 'visible', 'test_id' => 'download-xlsx'],
        ],
        downloads: [
            new ToolDownloadExpectation(
                id: 'difal-pdf',
                testId: 'download-pdf',
                format: 'pdf',
                extension: 'pdf',
                minimumBytes: 800,
                filenameContains: 'difal-icms',
                mimeType: 'application/pdf',
            ),
            new ToolDownloadExpectation(
                id: 'difal-xlsx',
                testId: 'download-xlsx',
                format: 'xlsx',
                extension: 'xlsx',
                minimumBytes: 1000,
                filenameContains: 'difal-icms',
                mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                requiredEntries: ['[Content_Types].xml', 'xl/workbook.xml'],
            ),
        ],
    ),
    new ToolScenario(
        id: 'campo-obrigatorio-invalido',
        title: 'Impede o envio quando um campo obrigatório está inválido',
        kind: 'invalid',
        toolSlug: 'calculadora-difal-icms',
        tags: ['lot-13', 'regression', 'minimum-coverage'],
        steps: [
            ['action' => 'auto_fill_form', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'invalidate_required', 'scope_test_id' => 'tool-form-panel'],
            ['action' => 'submit', 'scope_test_id' => 'tool-form-panel'],
        ],
        expectations: [
            ['type' => 'form_invalid', 'test_id' => 'tool-form-panel'],
            ['type' => 'url', 'contains' => '/ferramentas/calculadora-difal-icms'],
        ],
    ),
];

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
        'expected_tool_count' => 43,
        'required_kinds' => ['valid', 'invalid'],
    ],
    'tools' => $tools,
    'runtime' => [
        'scenario_manifest' => 'storage/app/e2e/runtime/tool-scenarios.json',
    ],
];
