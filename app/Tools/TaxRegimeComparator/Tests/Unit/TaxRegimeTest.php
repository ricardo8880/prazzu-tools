<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Tools\TaxRegimeComparator\Domain\Enums\TaxRegime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TaxRegimeTest extends TestCase
{
    #[DataProvider('regimes')]
    public function test_it_exposes_stable_values_and_portuguese_labels(
        TaxRegime $regime,
        string $value,
        string $label,
    ): void {
        self::assertSame($value, $regime->value);
        self::assertSame($label, $regime->label());
    }

    /** @return iterable<string, array{TaxRegime, string, string}> */
    public static function regimes(): iterable
    {
        yield 'simples nacional' => [TaxRegime::SimplesNacional, 'simples_nacional', 'Simples Nacional'];
        yield 'lucro presumido' => [TaxRegime::PresumedProfit, 'lucro_presumido', 'Lucro Presumido'];
        yield 'lucro real' => [TaxRegime::ActualProfit, 'lucro_real', 'Lucro Real'];
    }
}
