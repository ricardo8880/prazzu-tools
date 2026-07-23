<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite('emissor-de-recibos', [
            new GoldenCase('typical', 'Recibo comum', GoldenCaseKind::Typical,
                ['amount_minor' => 125045, 'number' => 'REC-2026-0001'],
                ['formatted' => 'R$ 1.250,45', 'words' => 'mil duzentos e cinquenta reais e quarenta e cinco centavos'],
                'Caso revisado conforme regras de domínio do Emissor de Recibos v0.1.0.'),
            new GoldenCase('boundary', 'Um centavo', GoldenCaseKind::Boundary,
                ['amount_minor' => 1], ['words' => 'um centavo'],
                'Caso de fronteira revisado conforme regras de domínio do Emissor de Recibos v0.1.0.'),
            new GoldenCase('rounding', 'Valor monetário preservado em centavos', GoldenCaseKind::Rounding,
                ['amount_minor' => 125091],
                ['formatted' => 'R$ 1.250,91', 'words' => 'mil duzentos e cinquenta reais e noventa e um centavos'],
                'Política monetária do domínio: valores são recebidos e processados em menor unidade, sem uso de float.',
                null,
                'Sem arredondamento intermediário: o valor inteiro em centavos é preservado até a apresentação.'),
            new GoldenCase('invalid-input', 'Valor zerado', GoldenCaseKind::InvalidInput,
                ['amount_minor' => 0], ['exception' => 'InvalidArgumentException'],
                'Regra de domínio: recibos exigem valor positivo.'),
            new GoldenCase('non-applicable', 'Moeda estrangeira fora do escopo', GoldenCaseKind::NonApplicable,
                ['currency' => 'USD', 'amount_minor' => 125000],
                ['supported' => false, 'reason' => 'o Emissor de Recibos gera valores monetários somente em reais brasileiros'],
                'Contrato de domínio do Emissor de Recibos: formatação monetária e valor por extenso são definidos exclusivamente para BRL.'),
            new GoldenCase('normative-transition', 'Transição entre identificação por CPF e CNPJ', GoldenCaseKind::NormativeTransition,
                ['party_type' => 'legal_entity', 'document_type' => 'cnpj', 'document' => '11222333000181'],
                ['accepted' => true, 'formatted_document' => '11.222.333/0001-81'],
                'Contrato normativo do domínio: a identificação das partes deve respeitar o tipo documental brasileiro informado.',
                'receipt-domain-v0.6.0'),
        ]);
    }
}
