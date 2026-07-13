<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Money;

use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function test_it_parses_and_formats_brazilian_money_without_float(): void
    {
        $money = Money::fromDecimal('R$ 1.234,56');

        self::assertSame(123456, $money->minorAmount());
        self::assertSame('R$ 1.234,56', $money->formatPtBr());
        self::assertTrue($money->equals(Money::fromDecimal('1234.56')));
    }

    public function test_it_applies_percentage_with_explicit_rounding(): void
    {
        $base = Money::fromDecimal('100.05');
        $percentage = Percentage::fromString('10');

        self::assertSame(1001, $base->percentage($percentage)->minorAmount());
        self::assertSame(1000, $base->percentage($percentage, RoundingMode::Down)->minorAmount());
    }

    #[DataProvider('percentageProvider')]
    public function test_percentage_has_stable_representation(string $input, string $expected): void
    {
        self::assertSame($expected, Percentage::fromString($input)->toDecimalString());
    }

    public static function percentageProvider(): array
    {
        return [
            ['10', '10'],
            ['12,345600', '12.3456'],
            ['-0.125', '-0.125'],
        ];
    }
}
