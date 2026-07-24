<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite('gerador-de-contratos', [
            new GoldenCase('typical', 'Prestação de serviços comum', GoldenCaseKind::Typical,
                ['contract_type' => 'prestacao-servicos', 'amount_minor' => 250000],
                ['title' => 'CONTRATO PARTICULAR DE PRESTAÇÃO DE SERVIÇOS', 'amount' => 'R$ 2.500,00'],
                'Fluxo Essencial revisado no Gerador de Contratos v0.5.0.'),
            new GoldenCase('boundary', 'Prestação por prazo determinado no limite aceito', GoldenCaseKind::Boundary,
                ['start_date' => '2026-08-01', 'end_date' => '2030-08-01'],
                ['accepted' => true],
                'Regra de validação vigente do módulo para prestação de serviços no Gerador de Contratos v0.5.0.'),
            new GoldenCase('invalid-input', 'Documento de parte inválido', GoldenCaseKind::InvalidInput,
                ['document_type' => 'cnpj', 'document' => '11.111.111/1111-11'],
                ['accepted' => false],
                'Contrato de entrada do módulo: CPF/CNPJ são validados pelos value objects compartilhados do Core.'),
            new GoldenCase('normative-transition', 'Modelo geral não cobre regimes especiais', GoldenCaseKind::NormativeTransition,
                ['scenario' => 'relação sujeita a regime especial'],
                ['supported_as_standard_template' => false],
                'Política de produto do Gerador de Contratos v0.5.0: modelos gerais exigem revisão específica quando houver regime especial.',
                'contract-generator-model-v0.5.0'),
            new GoldenCase('regression', 'Compra e venda mantém modalidade e exportação editável', GoldenCaseKind::Regression,
                ['contract_type' => 'compra-venda-bem-movel', 'asset_description' => 'Notebook empresarial'],
                ['title' => 'CONTRATO PARTICULAR DE COMPRA E VENDA DE BEM MÓVEL', 'exports' => ['pdf', 'docx']],
                'Caso de regressão aprovado para o fluxo Essencial do Gerador de Contratos v0.5.0.'),
        ]);
    }
}
