<?php

declare(strict_types=1);

namespace App\Core\Analytics\Application\Services;

final class StrategicAnalyticsInsightGenerator
{
    /** @param array<string,mixed> $report @return list<array<string,mixed>> */
    public function generate(array $report): array
    {
        return collect()
            ->merge($this->funnelInsights($report['funnel'] ?? []))
            ->merge($this->toolInsights($report['tool_performance'] ?? []))
            ->merge($this->acquisitionInsights($report['channel_breakdown'] ?? []))
            ->sortBy(fn (array $item): int => match ($item['priority']) {
                'high' => 1,
                'medium' => 2,
                default => 3,
            })
            ->values()
            ->all();
    }

    /** @param array<string,mixed> $funnel @return list<array<string,mixed>> */
    private function funnelInsights(array $funnel): array
    {
        $rates = $funnel['rates'] ?? [];
        $previous = $funnel['previous_rates'] ?? [];
        $counts = $funnel['current'] ?? [];
        $items = [];

        foreach ([
            'open_to_start' => ['label' => 'abertura para início', 'denominator' => 'opened', 'numerator' => 'started'],
            'start_to_complete' => ['label' => 'início para conclusão', 'denominator' => 'started', 'numerator' => 'completed'],
        ] as $metric => $definition) {
            $rate = (float) ($rates[$metric] ?? 0);
            $oldRate = (float) ($previous[$metric] ?? 0);
            $base = (int) ($counts[$definition['denominator']] ?? 0);
            $delta = round($rate - $oldRate, 1);

            if ($base < 20 || ($rate >= 60 && $delta > -10)) {
                continue;
            }

            $items[] = $this->insight(
                priority: $rate < 40 || $delta <= -15 ? 'high' : 'medium',
                type: 'funnel_bottleneck',
                title: 'Possível gargalo na etapa de '.$definition['label'],
                observation: sprintf('A taxa de %s foi de %.1f%% (%d de %d), com variação de %.1f pontos percentuais.', $definition['label'], $rate, (int) ($counts[$definition['numerator']] ?? 0), $base, $delta),
                evidence: ['metric' => $metric, 'rate' => $rate, 'previous_rate' => $oldRate, 'delta_percentage_points' => $delta, 'sample_size' => $base],
                hypotheses: ['Fricção ou falta de clareza na etapa.', 'Problema concentrado em uma ferramenta, dispositivo ou origem.', 'Mudança na qualidade do tráfego do período.'],
                actions: ['Segmentar a taxa por ferramenta e dispositivo.', 'Revisar erros, campos e mensagens da etapa.', 'Comparar origens de tráfego do período atual e anterior.'],
                confidence: $base >= 100 ? 'high' : 'medium',
            );
        }

        return $items;
    }

    /** @param list<array<string,mixed>> $tools @return list<array<string,mixed>> */
    private function toolInsights(array $tools): array
    {
        $items = [];
        foreach ($tools as $tool) {
            $starts = (int) ($tool['started'] ?? 0);
            $opens = (int) ($tool['opened'] ?? 0);
            $completionRate = (float) ($tool['completion_rate'] ?? 0);
            $startRate = (float) ($tool['start_rate'] ?? 0);
            $completionDelta = $tool['completion_rate_delta_pp'] ?? null;

            if ($opens >= 20 && $startRate < 35) {
                $items[] = $this->insight(
                    priority: $startRate < 20 ? 'high' : 'medium',
                    type: 'tool_activation',
                    title: 'Baixo início de uso em '.$tool['name'],
                    observation: sprintf('A ferramenta teve %d aberturas e %d inícios, taxa de %.1f%%.', $opens, $starts, $startRate),
                    evidence: ['tool' => $tool['name'], 'opened' => $opens, 'started' => $starts, 'start_rate' => $startRate],
                    hypotheses: ['A proposta ou o primeiro passo pode não estar claro.', 'A página pode atrair visitantes com intenção diferente.', 'O formulário inicial pode parecer trabalhoso.'],
                    actions: ['Revisar título, descrição e chamada principal.', 'Comparar a taxa por dispositivo e origem.', 'Inspecionar abandonos antes do primeiro envio.'],
                    confidence: $opens >= 100 ? 'high' : 'medium',
                );
            }

            if ($starts >= 20 && ($completionRate < 60 || ($completionDelta !== null && $completionDelta <= -10))) {
                $items[] = $this->insight(
                    priority: $completionRate < 40 || ($completionDelta !== null && $completionDelta <= -15) ? 'high' : 'medium',
                    type: 'tool_completion',
                    title: 'Conclusão exige atenção em '.$tool['name'],
                    observation: sprintf('Foram %d inícios e %d conclusões, taxa de %.1f%% e variação de %s p.p.', $starts, (int) $tool['completed'], $completionRate, $completionDelta === null ? 'sem base' : number_format((float) $completionDelta, 1, ',', '.')),
                    evidence: ['tool' => $tool['name'], 'started' => $starts, 'completed' => (int) $tool['completed'], 'completion_rate' => $completionRate, 'delta_percentage_points' => $completionDelta],
                    hypotheses: ['Erros ou validações podem impedir a conclusão.', 'Algum campo pode exigir informação difícil de obter.', 'A experiência pode variar por dispositivo.'],
                    actions: ['Testar o fluxo completo em desktop e mobile.', 'Revisar logs e eventos de erro relacionados.', 'Comparar campos e mudanças recentes da ferramenta.'],
                    confidence: $starts >= 100 ? 'high' : 'medium',
                );
            }
        }

        return array_slice($items, 0, 12);
    }

    /** @param list<array<string,mixed>> $channels @return list<array<string,mixed>> */
    private function acquisitionInsights(array $channels): array
    {
        $qualified = collect($channels)->filter(fn (array $row): bool => (int) ($row['visitors'] ?? 0) >= 20)
            ->map(function (array $row): array {
                $row['conversion_rate'] = (int) $row['visitors'] === 0 ? 0.0 : round(((int) $row['conversions'] / (int) $row['visitors']) * 100, 1);

                return $row;
            });

        if ($qualified->count() < 2) {
            return [];
        }

        $best = $qualified->sortByDesc('conversion_rate')->first();
        $worst = $qualified->sortBy('conversion_rate')->first();
        if (! $best || ! $worst || $best['name'] === $worst['name'] || ($best['conversion_rate'] - $worst['conversion_rate']) < 10) {
            return [];
        }

        return [$this->insight(
            priority: 'medium',
            type: 'channel_quality',
            title: 'Qualidade de tráfego varia entre canais',
            observation: sprintf('O canal %s converteu %.1f%% dos visitantes, enquanto %s converteu %.1f%%.', $best['name'] ?: 'não informado', $best['conversion_rate'], $worst['name'] ?: 'não informado', $worst['conversion_rate']),
            evidence: ['best_channel' => $best, 'lowest_channel' => $worst, 'rate_gap_percentage_points' => round($best['conversion_rate'] - $worst['conversion_rate'], 1)],
            hypotheses: ['Os canais podem trazer intenções de uso diferentes.', 'Campanhas ou conteúdos podem estar desalinhados com a ferramenta de destino.'],
            actions: ['Investigar quais ferramentas convertem em cada canal.', 'Revisar páginas de destino do canal de menor taxa.', 'Evitar realocar investimento antes de validar volume, custo e atribuição.'],
            confidence: min((int) $best['visitors'], (int) $worst['visitors']) >= 100 ? 'high' : 'medium',
        )];
    }

    /** @param array<string,mixed> $evidence @param list<string> $hypotheses @param list<string> $actions */
    private function insight(string $priority, string $type, string $title, string $observation, array $evidence, array $hypotheses, array $actions, string $confidence): array
    {
        return compact('priority', 'type', 'title', 'observation', 'evidence', 'hypotheses', 'actions', 'confidence');
    }
}
