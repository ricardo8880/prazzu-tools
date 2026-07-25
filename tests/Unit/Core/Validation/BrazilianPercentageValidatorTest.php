<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Validation;

use App\Core\Validation\BrazilianPercentageValidator;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class BrazilianPercentageValidatorTest extends TestCase
{
    #[DataProvider('validPercentages')]
    public function test_it_accepts_exactly_the_formats_supported_by_percentage(string|int $value): void
    {
        self::assertTrue((new BrazilianPercentageValidator)->isValid($value));
    }

    #[DataProvider('invalidPercentages')]
    public function test_it_rejects_values_that_percentage_cannot_parse(mixed $value): void
    {
        self::assertFalse((new BrazilianPercentageValidator)->isValid($value));
    }

    public function test_it_compares_minimum_and_maximum_using_scaled_integer_units(): void
    {
        $validator = new BrazilianPercentageValidator;

        self::assertTrue($validator->hasMinimum('-0,000001', ['-0.000001']));
        self::assertTrue($validator->hasMinimum('28,000001', ['28.000001']));
        self::assertFalse($validator->hasMinimum('27,999999', ['28']));
        self::assertTrue($validator->hasMaximum('99,999999', ['99.999999']));
        self::assertTrue($validator->hasMaximum('100', ['100.000000']));
        self::assertFalse($validator->hasMaximum('100,000001', ['100']));
    }

    public function test_limits_fail_closed_when_the_parameter_or_value_is_invalid(): void
    {
        $validator = new BrazilianPercentageValidator;

        self::assertFalse($validator->hasMinimum('10', []));
        self::assertFalse($validator->hasMaximum('10', []));
        self::assertFalse($validator->hasMinimum('10', ['1e1']));
        self::assertFalse($validator->hasMaximum('10', ['inválido']));
        self::assertFalse($validator->hasMinimum('1.0000001', ['0']));
        self::assertFalse($validator->hasMaximum([], ['100']));
    }

    public function test_laravel_rules_are_registered_and_share_the_value_object_contract(): void
    {
        self::assertTrue(Validator::make(
            ['rate' => '28,123456'],
            ['rate' => ['brazilian_percentage', 'percentage_min:0', 'percentage_max:100']],
        )->passes());

        self::assertTrue(Validator::make(
            ['rate' => '28.123456'],
            ['rate' => ['brazilian_percentage', 'percentage_min:28.123456', 'percentage_max:28.123456']],
        )->passes());

        self::assertFalse(Validator::make(
            ['rate' => '28,1234567'],
            ['rate' => ['brazilian_percentage']],
        )->passes());

        self::assertFalse(Validator::make(
            ['rate' => '1e2'],
            ['rate' => ['brazilian_percentage']],
        )->passes());

        self::assertFalse(Validator::make(
            ['rate' => '100,000001'],
            ['rate' => ['brazilian_percentage', 'percentage_max:100']],
        )->passes());
    }

    /** @return array<string, array{string|int}> */
    public static function validPercentages(): array
    {
        return [
            'zero string' => ['0'],
            'zero integer' => [0],
            'positive integer' => [100],
            'decimal point' => ['28.5'],
            'decimal comma' => ['28,5'],
            'maximum precision point' => ['99.123456'],
            'maximum precision comma' => ['99,123456'],
            'explicit positive sign' => ['+1,25'],
            'negative' => ['-0.000001'],
            'surrounding whitespace' => ['  5,8  '],
            'leading zeros' => ['00028,000000'],
        ];
    }

    /** @return array<string, array{mixed}> */
    public static function invalidPercentages(): array
    {
        return [
            'null' => [null],
            'empty' => [''],
            'whitespace only' => ['   '],
            'boolean' => [true],
            'float' => [28.5],
            'array' => [[]],
            'text' => ['vinte e oito'],
            'percent symbol' => ['28%'],
            'currency symbol' => ['R$ 28,00'],
            'thousands separator' => ['1.000,00'],
            'missing integer part' => [',5'],
            'trailing separator' => ['5,'],
            'two separators' => ['1,2,3'],
            'seven decimal places point' => ['1.1234567'],
            'seven decimal places comma' => ['1,1234567'],
            'scientific lowercase' => ['1e2'],
            'scientific uppercase' => ['1E2'],
            'overflow' => ['999999999999999999999999,00'],
        ];
    }
}
