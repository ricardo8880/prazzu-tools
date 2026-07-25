<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Validation;

use App\Core\Validation\BrazilianMoneyValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BrazilianMoneyValidatorTest extends TestCase
{
    #[DataProvider('validAmounts')]
    public function test_it_accepts_supported_brazilian_money_inputs(string|int $value): void
    {
        self::assertTrue((new BrazilianMoneyValidator)->isValid($value));
    }

    #[DataProvider('invalidAmounts')]
    public function test_it_rejects_invalid_or_ambiguous_inputs(mixed $value): void
    {
        self::assertFalse((new BrazilianMoneyValidator)->isValid($value));
    }

    public function test_it_compares_minimum_using_minor_units(): void
    {
        $validator = new BrazilianMoneyValidator;

        self::assertTrue($validator->hasMinimum('0,01', ['0.01']));
        self::assertTrue($validator->hasMinimum('1.234,56', ['1234.56']));
        self::assertFalse($validator->hasMinimum('0,00', ['0.01']));
        self::assertFalse($validator->hasMinimum('inválido', ['0.01']));
        self::assertFalse($validator->hasMinimum('10,00', []));
    }

    /** @return array<string, array{string|int}> */
    public static function validAmounts(): array
    {
        return [
            'zero' => ['0'],
            'integer' => [1500],
            'decimal dot' => ['1500.25'],
            'decimal comma' => ['1500,25'],
            'brazilian thousands' => ['1.500,25'],
            'currency symbol' => ['R$ 1.500,25'],
            'negative' => ['-10,50'],
        ];
    }

    /** @return array<string, array{mixed}> */
    public static function invalidAmounts(): array
    {
        return [
            'null' => [null],
            'empty' => [''],
            'text' => ['mil reais'],
            'three decimals' => ['1,234'],
            'array' => [[]],
            'boolean' => [true],
            'float' => [10.5],
            'overflow' => ['999999999999999999999999,00'],
        ];
    }
}
