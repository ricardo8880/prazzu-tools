<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Quality;

use App\Core\Exceptions\InvalidValue;
use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;
use PHPUnit\Framework\TestCase;

final class GoldenCaseTest extends TestCase
{
    public function test_accepts_decimal_values_as_strings(): void
    {
        $case = new GoldenCase(
            identifier: 'calculo.normal',
            title: 'Cálculo normal',
            kind: GoldenCaseKind::Typical,
            input: ['amount' => '100.25'],
            expected: ['total' => '110.28'],
            reference: 'Planilha revisada pelo responsável técnico.',
            roundingPolicy: 'Duas casas decimais, meio para cima.',
        );

        $this->assertSame('100.25', $case->input['amount']);
    }

    public function test_rejects_float_values(): void
    {
        $this->expectException(InvalidValue::class);

        new GoldenCase(
            identifier: 'calculo.float',
            title: 'Cálculo inválido',
            kind: GoldenCaseKind::Typical,
            input: ['amount' => 100.25],
            expected: ['total' => '110.28'],
            reference: 'Referência técnica.',
        );
    }

    public function test_suite_rejects_duplicate_identifiers(): void
    {
        $this->expectException(InvalidValue::class);

        $case = new GoldenCase(
            identifier: 'caso.repetido',
            title: 'Caso repetido',
            kind: GoldenCaseKind::Typical,
            input: ['value' => 1],
            expected: ['value' => 1],
            reference: 'Referência técnica.',
        );

        new GoldenCaseSuite('ferramenta-exemplo', [$case, $case]);
    }
}
