<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

final readonly class ProjectAnnualSimplesNacional
{
    public function __construct(private CalculateSimplesNacional $calculate) {}

    /** @return array{months:list<array<string, int|string>>, total_revenue:string, total_das:string} */
    public function execute(string $annex, string $monthlyRevenue, float $monthlyGrowth): array
    {
        $revenue = $this->parseMoney($monthlyRevenue);
        $totalRevenue = 0.0;
        $totalDas = 0.0;
        $months = [];

        for ($month = 1; $month <= 12; $month++) {
            if ($month > 1) {
                $revenue *= 1 + ($monthlyGrowth / 100);
            }

            $annualizedRbt12 = $revenue * 12;
            $result = $this->calculate->execute([
                'annex' => $annex,
                'rbt12' => number_format($annualizedRbt12, 2, '.', ''),
                'monthly_revenue' => number_format($revenue, 2, '.', ''),
            ])->toArray();

            $das = $this->parseMoney($result['estimated_das']);
            $totalRevenue += $revenue;
            $totalDas += $das;
            $months[] = [
                'month' => $month,
                'monthly_revenue' => $result['monthly_revenue'],
                'rbt12' => $result['rbt12'],
                'effective_rate' => $result['effective_rate'],
                'estimated_das' => $result['estimated_das'],
                'bracket' => $result['bracket'],
            ];
        }

        return [
            'months' => $months,
            'total_revenue' => $this->formatMoney($totalRevenue),
            'total_das' => $this->formatMoney($totalDas),
        ];
    }

    private function parseMoney(string $value): float
    {
        $normalized = str_replace(['R$', ' '], '', trim($value));
        if (str_contains($normalized, ',')) {
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        }

        return (float) $normalized;
    }

    private function formatMoney(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }
}
