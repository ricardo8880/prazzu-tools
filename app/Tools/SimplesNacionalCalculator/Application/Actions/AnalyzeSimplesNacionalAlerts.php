<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use App\Tools\SimplesNacionalCalculator\Domain\Rules\SimplesNacionalTaxTable;

final readonly class AnalyzeSimplesNacionalAlerts
{
    public function __construct(
        private CalculateSimplesNacional $calculate,
        private CalculateFactorR $factorR,
        private SimplesNacionalTaxTable $taxTable,
    ) {}

    /** @return array{summary: array<string, int>, alerts: list<array<string, string>>} */
    public function execute(array $input): array
    {
        $annex = TaxAnnex::from((string) $input['annex']);
        $rbt12 = $this->parseMoney((string) $input['rbt12']);
        $monthlyRevenue = $this->parseMoney((string) $input['monthly_revenue']);
        $growth = (float) ($input['monthly_growth'] ?? 0);
        $result = $this->calculate->execute($input)->toArray();
        $alerts = [];

        $brackets = $this->taxTable->bracketsFor($annex);
        $currentIndex = max(0, ((int) $result['bracket']) - 1);
        $current = $brackets[$currentIndex];
        $limit = $current->revenueUntil->minorAmount() / 100;
        $distance = $limit - $rbt12;

        if ((int) $result['bracket'] < 6 && $distance <= max(1, $limit * 0.10)) {
            $alerts[] = $this->alert('warning', 'Mudança de faixa próxima', 'Faltam '.$this->formatMoney(max(0, $distance)).' para alcançar a próxima faixa do '.$annex->label().'.');
        }

        if ($rbt12 >= 4320000) {
            $alerts[] = $this->alert($rbt12 > 4800000 ? 'danger' : 'warning', 'Limite do Simples Nacional', $rbt12 > 4800000 ? 'O RBT12 informado ultrapassa o limite de R$ 4.800.000,00.' : 'O RBT12 já atingiu 90% do limite de R$ 4.800.000,00.');
        }

        if (isset($input['payroll_12']) && (string) $input['payroll_12'] !== '') {
            $factor = $this->factorR->execute(['payroll_12' => (string) $input['payroll_12'], 'rbt12' => (string) $input['rbt12']])->toArray();
            $percentage = (float) str_replace(',', '.', str_replace('%', '', $factor['factor_r']));
            $gap = 28 - $percentage;
            if (abs($gap) <= 3) {
                $payrollNeeded = max(0, ($rbt12 * 0.28) - $this->parseMoney((string) $input['payroll_12']));
                $message = $gap > 0
                    ? 'Faltam aproximadamente '.$this->formatMoney($payrollNeeded).' de folha acumulada para atingir 28% e enquadrar no Anexo III.'
                    : 'O Fator R está próximo do limite de 28%; acompanhe folha e receita para evitar mudança ao Anexo V.';
                $alerts[] = $this->alert('info', 'Fator R sensível', $message);
            }
        }

        if ($growth > 0) {
            $projectedRevenue = $monthlyRevenue * (1 + ($growth / 100));
            $projectedRbt12 = min(4800000, $rbt12 + ($projectedRevenue - $monthlyRevenue));
            $projected = $this->calculate->execute([
                'annex' => $annex->value,
                'rbt12' => number_format($projectedRbt12, 2, '.', ''),
                'monthly_revenue' => number_format($projectedRevenue, 2, '.', ''),
            ])->toArray();
            if ($projected['bracket'] !== $result['bracket']) {
                $alerts[] = $this->alert('warning', 'Crescimento altera a faixa', 'Com crescimento de '.number_format($growth, 2, ',', '.').'%, a estimativa passa da faixa '.$result['bracket'].' para a faixa '.$projected['bracket'].'.');
            }
            $alerts[] = $this->alert('primary', 'Impacto estimado no DAS', 'No próximo mês, o DAS estimado seria '.$projected['estimated_das'].' para faturamento de '.$projected['monthly_revenue'].'.');
        }

        if ($alerts === []) {
            $alerts[] = $this->alert('success', 'Nenhum alerta crítico', 'Os dados informados não indicam mudança próxima de faixa, limite ou enquadramento.');
        }

        $counts = ['danger' => 0, 'warning' => 0, 'info' => 0, 'primary' => 0, 'success' => 0];
        foreach ($alerts as $alert) { $counts[$alert['level']]++; }

        return ['summary' => $counts, 'alerts' => $alerts];
    }

    private function alert(string $level, string $title, string $message): array { return compact('level', 'title', 'message'); }
    private function parseMoney(string $value): float { $v = str_replace(['R$', ' '], '', trim($value)); if (str_contains($v, ',')) { $v = str_replace('.', '', $v); $v = str_replace(',', '.', $v); } return (float) $v; }
    private function formatMoney(float $value): string { return 'R$ '.number_format($value, 2, ',', '.'); }
}
