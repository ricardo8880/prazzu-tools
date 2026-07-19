<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Data;

final readonly class TaxAlertAnalysis
{
    /** @param list<TaxAlert> $alerts */
    public function __construct(public array $alerts) {}

    /** @return array{summary:array<string,int>,alerts:list<array{level:string,title:string,message:string}>} */
    public function toArray(): array
    {
        $summary = ['danger' => 0, 'warning' => 0, 'info' => 0, 'primary' => 0, 'success' => 0];

        foreach ($this->alerts as $alert) {
            $summary[$alert->level]++;
        }

        return [
            'summary' => $summary,
            'alerts' => array_map(
                static fn (TaxAlert $alert): array => $alert->toArray(),
                $this->alerts,
            ),
        ];
    }
}
