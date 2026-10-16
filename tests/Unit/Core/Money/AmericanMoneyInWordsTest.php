<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Money;

use App\Core\Money\AmericanMoneyInWords;
use App\Core\Money\Money;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AmericanMoneyInWordsTest extends TestCase
{
    #[DataProvider('amounts')]
    public function test_it_writes_positive_usd_amounts_in_english(string $amount, string $expected): void
    {
        self::assertSame($expected, (new AmericanMoneyInWords)->convert(Money::fromDecimal($amount)));
    }

    public static function amounts(): iterable
    {
        yield 'one dollar' => ['1.00', 'one dollar'];
        yield 'one cent' => ['0.01', 'one cent'];
        yield 'hundred' => ['100.00', 'one hundred dollars'];
        yield 'thousand and cents' => ['1250.45', 'one thousand two hundred fifty dollars and forty-five cents'];
        yield 'million' => ['1000000.00', 'one million dollars'];
    }

    public function test_it_rejects_zero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new AmericanMoneyInWords)->convert(Money::zero());
    }
}
