<?php

namespace App\Core\Analytics\Application\Services;

use Illuminate\Support\Collection;

final class CampaignAnalyticsInsightGenerator
{
    /** @param array<string, mixed> $current @param array<string, mixed> $previous */
    public function generate(array $current, array $previous): Collection
    {
        $insights = collect();
        $currentSummary = $current['summary'];
        $previousSummary = $previous['summary'];

        $sessionChange = $this->change($currentSummary['sessions'], $previousSummary['sessions']);
        if ($previousSummary['sessions'] >= 5 && abs($sessionChange) >= 20) {
            $insights->push((object) [
                'severity' => $sessionChange >= 0 ? 'success' : 'danger',
                'title' => $sessionChange >= 0 ? 'Campanhas ganharam tráfego' : 'Campanhas perderam tráfego',
                'message' => sprintf('As sessões atribuídas %s %.1f%% em relação ao período anterior.', $sessionChange >= 0 ? 'cresceram' : 'caíram', abs($sessionChange)),
                'recommendation' => $sessionChange >= 0
                    ? 'Amplifique as campanhas e palavras-chave responsáveis pelo crescimento.'
                    : 'Revise distribuição, criativos e origens das campanhas com maior queda.',
            ]);
        }

        $rateDelta = round($currentSummary['conversion_rate'] - $previousSummary['conversion_rate'], 1);
        if ($previousSummary['sessions'] >= 5 && abs($rateDelta) >= 5) {
            $insights->push((object) [
                'severity' => $rateDelta >= 0 ? 'success' : 'warning',
                'title' => $rateDelta >= 0 ? 'Conversão de campanhas melhorou' : 'Conversão de campanhas recuou',
                'message' => sprintf('A taxa de conversão variou %.1f pontos percentuais.', $rateDelta),
                'recommendation' => $rateDelta >= 0
                    ? 'Preserve a combinação de contexto, CTA e ferramenta das campanhas líderes.'
                    : 'Compare o funil das campanhas e priorize a primeira etapa com maior perda.',
            ]);
        }

        $bestKeyword = collect($current['keywords'] ?? [])->sortByDesc('conversion_rate')->first(fn (object $row): bool => $row->sessions >= 3);
        if ($bestKeyword) {
            $insights->push((object) [
                'severity' => 'info',
                'title' => 'Palavra-chave com melhor eficiência',
                'message' => sprintf('%s converteu %.1f%% em %d sessões.', $bestKeyword->label, $bestKeyword->conversion_rate, $bestKeyword->sessions),
                'recommendation' => 'Use esse contexto como referência para novos conteúdos, CTAs e campanhas relacionadas.',
            ]);
        }

        $bestRoi = collect($current['roi'] ?? [])->first(fn (object $row): bool => $row->roi !== null && $row->subscriptions > 0);
        if ($bestRoi) {
            $insights->push((object) [
                'severity' => $bestRoi->roi >= 0 ? 'success' : 'warning',
                'title' => $bestRoi->roi >= 0 ? 'Campanha com retorno positivo' : 'Campanha ainda não recuperou o investimento',
                'message' => sprintf('%s apresenta ROI estimado de %.1f%%.', $bestRoi->campaign, $bestRoi->roi),
                'recommendation' => $bestRoi->roi >= 0
                    ? 'Considere ampliar o investimento gradualmente, preservando o custo por assinatura.'
                    : 'Revise custo, segmentação e etapas do funil antes de ampliar o orçamento.',
            ]);
        }

        $bestRetention = collect($current['retention'] ?? [])->first(fn (object $row): bool => $row->retention_7d !== null && $row->eligible_7d >= 3);
        if ($bestRetention) {
            $insights->push((object) [
                'severity' => 'info',
                'title' => 'Contexto com melhor retenção inicial',
                'message' => sprintf('%s reteve %.1f%% da coorte elegível em até 7 dias.', $bestRetention->label, $bestRetention->retention_7d),
                'recommendation' => 'Reaproveite a promessa, a ferramenta inicial e a sequência de descoberta desse contexto.',
            ]);
        }

        $weakCampaign = collect($current['campaigns'] ?? [])->first(fn (object $row): bool => $row->sessions >= 5 && $row->tool_clicks === 0);
        if ($weakCampaign) {
            $insights->push((object) [
                'severity' => 'warning',
                'title' => 'Campanha com tráfego sem descoberta de ferramenta',
                'message' => sprintf('%s recebeu %d sessões, mas não registrou cliques em ferramentas.', $weakCampaign->label, $weakCampaign->sessions),
                'recommendation' => 'Revise a correspondência entre promessa, hero, CTA e ferramenta recomendada.',
            ]);
        }

        return $insights->take(8)->values();
    }

    private function change(float|int $current, float|int $previous): float
    {
        if ((float) $previous === 0.0) {
            return (float) $current === 0.0 ? 0.0 : 100.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
