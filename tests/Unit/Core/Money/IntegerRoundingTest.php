<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Money;

use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class IntegerRoundingTest extends TestCase
{
    #[DataProvider('roundingProvider')]
    public function test_rounding_is_explicit_and_symmetric(
        int $numerator,
        int $denominator,
        RoundingMode $mode,
        int $expected,
    ): void {
        self::assertSame($expected, IntegerRounding::divide($numerator, $denominator, $mode));
    }

    public static function roundingProvider(): array
    {
        return [
            [5, 2, RoundingMode::HalfUp, 3],
            [-5, 2, RoundingMode::HalfUp, -3],
            [5, 2, RoundingMode::HalfDown, 2],
            [5, 2, RoundingMode::HalfEven, 2],
            [7, 2, RoundingMode::HalfEven, 4],
            [1, 3, RoundingMode::Up, 1],
            [-1, 3, RoundingMode::Down, 0],
        ];
    }
}
