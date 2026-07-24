<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Money;

use App\Core\Money\BrazilianMoneyInWords;
use App\Core\Money\Money;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BrazilianMoneyInWordsTest extends TestCase
{
    #[DataProvider('amounts')]
    public function test_it_writes_positive_brl_amounts_in_portuguese(string $amount, string $expected): void
    {
        self::assertSame($expected, (new BrazilianMoneyInWords())->convert(Money::fromDecimal($amount)));
    }

    /** @return iterable<string, array{string, string}> */
    public static function amounts(): iterable
    {
        yield 'one real' => ['1,00', 'um real'];
        yield 'cents only' => ['0,01', 'um centavo'];
        yield 'hundred' => ['100,00', 'cem reais'];
        yield 'thousand and cents' => ['1.250,45', 'mil duzentos e cinquenta reais e quarenta e cinco centavos'];
        yield 'million' => ['1.000.000,00', 'um milhão de reais'];
    }

    public function test_it_rejects_zero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new BrazilianMoneyInWords())->convert(Money::zero());
    }
}
