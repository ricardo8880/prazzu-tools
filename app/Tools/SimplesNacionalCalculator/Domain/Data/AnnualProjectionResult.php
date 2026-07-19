<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Data;

use App\Core\Money\Money;

final readonly class AnnualProjectionResult
{
    /** @param list<SimplesNacionalResult> $months */
    public function __construct(
        public array $months,
        public Money $totalRevenue,
        public Money $totalDas,
    ) {}

    /** @return array{months:list<array<string, int|string>>, total_revenue:string, total_das:string} */
    public function toArray(): array
    {
        return [
            'months' => array_map(
                static fn (SimplesNacionalResult $result, int $index): array => [
                    'month' => $index + 1,
                    ...$result->toArray(),
                ],
                $this->months,
                array_keys($this->months),
            ),
            'total_revenue' => $this->totalRevenue->formatPtBr(),
            'total_das' => $this->totalDas->formatPtBr(),
        ];
    }
}
