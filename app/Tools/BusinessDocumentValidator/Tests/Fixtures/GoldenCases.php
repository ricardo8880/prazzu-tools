<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $documentReference = 'BusinessDocumentValidatorTest valida CPF 529.982.247-25 e CNPJ 04.252.011/0001-10 e rejeita dígitos incorretos e sequências repetidas.';
        $ieReference = 'StateRegistrationValidatorRegistryTest protege estratégias estaduais conhecidas e exige estado suportado sem inferência.';

        return new GoldenCaseSuite(
            toolSlug: 'validador-de-cnpj',
            cases: [
                new GoldenCase(
                    identifier: 'typical',
                    title: 'CPF conhecido é validado e formatado',
                    kind: GoldenCaseKind::Typical,
                    input: ['document_type' => 'automatic', 'document_number' => '52998224725'],
                    expected: ['valid' => true, 'type' => 'cpf', 'formatted' => '529.982.247-25'],
                    reference: $documentReference,
                    normativeRuleVersion: 'document-validator-v1',
                ),
                new GoldenCase(
                    identifier: 'boundary',
                    title: 'Tipo automático distingue documento pelo comprimento suportado',
                    kind: GoldenCaseKind::Boundary,
                    input: ['document_type' => 'automatic', 'document_number' => '04252011000110'],
                    expected: ['valid' => true, 'type' => 'cnpj', 'formatted' => '04.252.011/0001-10'],
                    reference: $documentReference,
                    normativeRuleVersion: 'document-validator-v1',
                ),
                new GoldenCase(
                    identifier: 'invalid-input',
                    title: 'Sequência repetida de CPF é inválida',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['document_type' => 'automatic', 'document_number' => '11111111111'],
                    expected: ['valid' => false],
                    reference: $documentReference,
                    normativeRuleVersion: 'document-validator-v1',
                ),
                new GoldenCase(
                    identifier: 'normative-transition',
                    title: 'UF sem estratégia local é declarada não suportada sem adivinhação',
                    kind: GoldenCaseKind::NormativeTransition,
                    input: ['state' => 'AC', 'state_registration' => '123456789'],
                    expected: ['valid' => false, 'supported' => false],
                    reference: $ieReference,
                    normativeRuleVersion: 'state-registration-strategies-v1',
                ),
                new GoldenCase(
                    identifier: 'regression',
                    title: 'Inscrição estadual de São Paulo conhecida permanece válida',
                    kind: GoldenCaseKind::Regression,
                    input: ['state' => 'SP', 'state_registration' => '110042490114'],
                    expected: ['valid' => true, 'supported' => true, 'state' => 'SP'],
                    reference: $ieReference,
                    normativeRuleVersion: 'state-registration-strategies-v1',
                ),
            ],
        );
    }
}
