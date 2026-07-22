<?php

namespace App\Core\Analytics\Application\Queries;

use App\Core\Acquisition\Contracts\AcquisitionCampaignInvestmentProvider;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Support\Collection;

final class CampaignAnalyticsQuery
{
    public function __construct(
        private readonly AnalyticsEventNameResolver $eventNames,
        private readonly AcquisitionCampaignInvestmentProvider $investments,
    ) {}

    /** @return array<string, mixed> */
    public function execute(AnalyticsPeriod $period): array
    {
        $sessions = AnalyticsSession::query()
            ->whereBetween('started_at', [$period->start, $period->end])
            ->whereNotNull('acquisition_context_id')
            ->get();
        $events = PlatformAnalyticsEvent::query()
            ->whereBetween('occurred_at', [$period->start, $period->end])
            ->whereNotNull('acquisition_context_id')
            ->get();

        return [
            'period' => $period,
            'summary' => $this->summary($sessions, $events),
            'campaigns' => $this->rank($sessions, $events, 'acquisition_campaign_identifier', 'Sem campanha'),
            'origins' => $this->rank($sessions, $events, 'source', 'Direto / desconhecido'),
            'keywords' => $this->rank($sessions, $events, 'acquisition_keyword', 'Sem palavra-chave'),
            'contexts' => $this->rank($sessions, $events, 'acquisition_context_id', 'Sem contexto'),
            'tools' => $this->tools($events),
            'ctas' => $this->ctaPerformance($events),
            'journeys' => $this->toolJourneys($events),
            'funnels' => $this->funnels($sessions, $events),
            'roi' => $this->roi($period, $sessions, $events),
            'retention' => $this->retention($period, $sessions),
        ];
    }

    /** @param Collection<int, AnalyticsSession> $sessions @param Collection<int, PlatformAnalyticsEvent> $events */
    private function summary(Collection $sessions, Collection $events): array
    {
        $visitors = $sessions->pluck('visitor_id')->filter()->unique()->count();
        $conversions = $events->whereIn('event_name', $this->conversionEvents())->count();

        return [
            'sessions' => $sessions->count(),
            'visitors' => $visitors,
            'contexts' => $sessions->pluck('acquisition_context_id')->filter()->unique()->count(),
            'campaigns' => $sessions->pluck('acquisition_campaign_identifier')->filter()->unique()->count(),
            'keywords' => $sessions->pluck('acquisition_keyword')->filter()->unique()->count(),
            'conversions' => $conversions,
            'conversion_rate' => $sessions->isEmpty() ? 0.0 : round(($conversions / $sessions->count()) * 100, 1),
        ];
    }

    /** @param Collection<int, AnalyticsSession> $sessions @param Collection<int, PlatformAnalyticsEvent> $events */
    private function rank(Collection $sessions, Collection $events, string $field, string $fallback): Collection
    {
        return $sessions->groupBy(fn (AnalyticsSession $session): string => (string) ($session->{$field} ?: $fallback))
            ->map(function (Collection $group, string $label) use ($events, $field): object {
                $value = $group->first()?->{$field};
                $related = $events->filter(fn (PlatformAnalyticsEvent $event): bool => $event->{$field} == $value);
                $conversions = $related->whereIn('event_name', $this->conversionEvents())->count();

                return (object) [
                    'label' => $label,
                    'sessions' => $group->count(),
                    'visitors' => $group->pluck('visitor_id')->filter()->unique()->count(),
                    'tool_clicks' => $related->where('event_name', AnalyticsEventName::AcquisitionToolClicked->value)->count(),
                    'calculations_started' => $related->where('event_name', AnalyticsEventName::ToolCalculationStarted->value)->count(),
                    'calculations_completed' => $related->where('event_name', AnalyticsEventName::ToolCalculationCompleted->value)->count(),
                    'accounts' => $related->where('event_name', AnalyticsEventName::AccountCreated->value)->count(),
                    'subscriptions' => $related->whereIn('event_name', [AnalyticsEventName::SubscriptionStarted->value, AnalyticsEventName::SubscriptionCreated->value])->count(),
                    'conversions' => $conversions,
                    'conversion_rate' => $group->isEmpty() ? 0.0 : round(($conversions / $group->count()) * 100, 1),
                ];
            })->sortByDesc('conversions')->values()->take(50);
    }

    /** @param Collection<int, PlatformAnalyticsEvent> $events */
    private function tools(Collection $events): Collection
    {
        return $events->where('event_name', AnalyticsEventName::AcquisitionToolClicked->value)
            ->groupBy(fn (PlatformAnalyticsEvent $event): string => (string) (data_get($event->metadata, 'tool_slug') ?: $event->subject_slug ?: 'desconhecida'))
            ->map(function (Collection $clicks, string $tool) use ($events): object {
                $visitorIds = $clicks->pluck('visitor_id')->filter()->unique();
                $downstream = $events->whereIn('visitor_id', $visitorIds);

                return (object) [
                    'tool' => $tool,
                    'clicks' => $clicks->count(),
                    'visitors' => $visitorIds->count(),
                    'calculations_started' => $downstream->where('event_name', AnalyticsEventName::ToolCalculationStarted->value)->count(),
                    'calculations_completed' => $downstream->where('event_name', AnalyticsEventName::ToolCalculationCompleted->value)->count(),
                ];
            })->sortByDesc('clicks')->values()->take(30);
    }


    /** @param Collection<int, PlatformAnalyticsEvent> $events */
    private function ctaPerformance(Collection $events): Collection
    {
        $views = $events->where('event_name', AnalyticsEventName::AcquisitionCtaViewed->value);
        $clicks = $events->where('event_name', AnalyticsEventName::AcquisitionCtaClicked->value);

        return $views->groupBy(fn (PlatformAnalyticsEvent $event): string => (string) (data_get($event->metadata, 'cta_identifier') ?: data_get($event->metadata, 'cta_label') ?: 'CTA contextual'))
            ->map(function (Collection $group, string $label) use ($clicks): object {
                $contextIds = $group->pluck('acquisition_context_id')->filter()->unique();
                $relatedClicks = $clicks->whereIn('acquisition_context_id', $contextIds)->count();

                return (object) [
                    'label' => $label,
                    'views' => $group->count(),
                    'clicks' => $relatedClicks,
                    'ctr' => $group->isEmpty() ? 0.0 : round(($relatedClicks / $group->count()) * 100, 1),
                ];
            })->sortByDesc('clicks')->values()->take(30);
    }

    /** @param Collection<int, PlatformAnalyticsEvent> $events */
    private function toolJourneys(Collection $events): Collection
    {
        $clicks = $events->where('event_name', AnalyticsEventName::AcquisitionToolClicked->value)
            ->sortBy('occurred_at')->groupBy(fn (PlatformAnalyticsEvent $event): string => (string) ($event->analytics_session_id ?: 'visitor-'.$event->visitor_id));
        $pairs = [];
        foreach ($clicks as $sessionClicks) {
            $tools = $sessionClicks->map(fn (PlatformAnalyticsEvent $event): string => (string) (data_get($event->metadata, 'tool_slug') ?: $event->subject_slug ?: 'desconhecida'))->values();
            for ($index = 1; $index < $tools->count(); $index++) {
                $key = $tools[$index - 1].' → '.$tools[$index];
                $pairs[$key] = ($pairs[$key] ?? 0) + 1;
            }
        }

        return collect($pairs)->map(fn (int $count, string $journey): object => (object) ['journey' => $journey, 'transitions' => $count])
            ->sortByDesc('transitions')->values()->take(30);
    }

    /** @param Collection<int, AnalyticsSession> $sessions @param Collection<int, PlatformAnalyticsEvent> $events */
    private function funnels(Collection $sessions, Collection $events): Collection
    {
        $steps = [
            ['label' => 'Sessões', 'event' => null],
            ['label' => 'Clique em ferramenta', 'event' => AnalyticsEventName::AcquisitionToolClicked->value],
            ['label' => 'Cálculo iniciado', 'event' => AnalyticsEventName::ToolCalculationStarted->value],
            ['label' => 'Cálculo concluído', 'event' => AnalyticsEventName::ToolCalculationCompleted->value],
            ['label' => 'Conta criada', 'event' => AnalyticsEventName::AccountCreated->value],
            ['label' => 'Assinatura', 'event' => [AnalyticsEventName::SubscriptionStarted->value, AnalyticsEventName::SubscriptionCreated->value]],
        ];

        return $sessions->groupBy(fn (AnalyticsSession $session): string => (string) ($session->acquisition_campaign_identifier ?: 'Sem campanha'))
            ->map(function (Collection $group, string $campaign) use ($events, $steps): array {
                $visitorIds = $group->pluck('visitor_id')->filter()->unique();
                $related = $events->whereIn('visitor_id', $visitorIds);
                $previous = null;
                $result = [];
                foreach ($steps as $step) {
                    $count = $step['event'] === null
                        ? $visitorIds->count()
                        : $related->whereIn('event_name', (array) $step['event'])->pluck('visitor_id')->filter()->unique()->count();
                    $result[] = [
                        'label' => $step['label'],
                        'visitors' => $count,
                        'step_rate' => $previous === null ? 100.0 : ($previous === 0 ? 0.0 : round(($count / $previous) * 100, 1)),
                    ];
                    $previous = $count;
                }

                return ['campaign' => $campaign, 'steps' => $result];
            })->sortByDesc(fn (array $funnel): int => $funnel['steps'][0]['visitors'])->values()->take(20);
    }


    /** @param Collection<int, AnalyticsSession> $sessions @param Collection<int, PlatformAnalyticsEvent> $events */
    private function roi(AnalyticsPeriod $period, Collection $sessions, Collection $events): Collection
    {
        $contextIds = $sessions->pluck('acquisition_context_id')->filter()->map(static fn ($id): int => (int) $id)->unique()->values()->all();
        $investments = $this->investments->forContextIds($contextIds);
        $revenueKeys = array_values(array_filter((array) config('analytics.dashboard.revenue_metadata_keys', []), 'is_string'));

        return $sessions->groupBy(fn (AnalyticsSession $session): string => (string) ($session->acquisition_campaign_identifier ?: 'Sem campanha'))
            ->map(function (Collection $group, string $campaign) use ($events, $investments, $period, $revenueKeys): object {
                $contextIds = $group->pluck('acquisition_context_id')->filter()->map(static fn ($id): int => (int) $id)->unique();
                $related = $events->whereIn('acquisition_context_id', $contextIds);
                $subscriptions = $related->whereIn('event_name', [AnalyticsEventName::SubscriptionStarted->value, AnalyticsEventName::SubscriptionCreated->value]);
                $monthlyCost = $contextIds->sum(static fn (int $id): int => (int) data_get($investments, $id.'.monthly_investment_cents', 0));
                $cost = (int) round($monthlyCost * ($period->days() / 30));
                $revenue = $subscriptions->sum(function (PlatformAnalyticsEvent $event) use ($revenueKeys): int {
                    foreach ($revenueKeys as $key) {
                        $value = data_get($event->metadata, $key);
                        if (is_numeric($value)) {
                            return max(0, (int) $value);
                        }
                    }
                    return 0;
                });
                $accounts = $related->where('event_name', AnalyticsEventName::AccountCreated->value)->count();
                $subscriptionCount = $subscriptions->count();

                return (object) [
                    'campaign' => $campaign,
                    'currency' => 'BRL',
                    'cost_cents' => $cost,
                    'revenue_cents' => $revenue,
                    'accounts' => $accounts,
                    'subscriptions' => $subscriptionCount,
                    'cost_per_account_cents' => $accounts > 0 && $cost > 0 ? (int) round($cost / $accounts) : null,
                    'cost_per_subscription_cents' => $subscriptionCount > 0 && $cost > 0 ? (int) round($cost / $subscriptionCount) : null,
                    'roas' => $cost > 0 ? round($revenue / $cost, 2) : null,
                    'roi' => $cost > 0 ? round((($revenue - $cost) / $cost) * 100, 1) : null,
                ];
            })->sortByDesc(fn (object $row): float => (float) ($row->roi ?? -INF))->values();
    }

    /** @param Collection<int, AnalyticsSession> $cohortSessions */
    private function retention(AnalyticsPeriod $period, Collection $cohortSessions): Collection
    {
        if ($cohortSessions->isEmpty()) {
            return collect();
        }

        $now = now();
        $futureSessions = AnalyticsSession::query()
            ->whereIn('visitor_id', $cohortSessions->pluck('visitor_id')->filter()->unique())
            ->whereIn('acquisition_context_id', $cohortSessions->pluck('acquisition_context_id')->filter()->unique())
            ->where('started_at', '>', $period->start)
            ->where('started_at', '<=', $period->end->addDays(30)->min($now))
            ->get();

        return $cohortSessions->groupBy(fn (AnalyticsSession $session): string => (string) ($session->acquisition_keyword ?: 'Sem palavra-chave'))
            ->map(function (Collection $group, string $label) use ($futureSessions, $now): object {
                $cohorts = $group->sortBy('started_at')->unique(fn (AnalyticsSession $session): string => $session->visitor_id.'-'.$session->acquisition_context_id);
                $mature7 = $cohorts->filter(fn (AnalyticsSession $session): bool => $session->started_at->lte($now->copy()->subDays(7)));
                $mature30 = $cohorts->filter(fn (AnalyticsSession $session): bool => $session->started_at->lte($now->copy()->subDays(30)));
                $retained = function (AnalyticsSession $initial, int $days) use ($futureSessions): bool {
                    return $futureSessions->contains(fn (AnalyticsSession $later): bool =>
                        $later->visitor_id === $initial->visitor_id
                        && $later->acquisition_context_id === $initial->acquisition_context_id
                        && $later->started_at->gt($initial->started_at)
                        && $later->started_at->lte($initial->started_at->copy()->addDays($days))
                    );
                };

                $retained7 = $mature7->filter(fn (AnalyticsSession $session): bool => $retained($session, 7))->count();
                $retained30 = $mature30->filter(fn (AnalyticsSession $session): bool => $retained($session, 30))->count();

                return (object) [
                    'label' => $label,
                    'cohort' => $cohorts->count(),
                    'eligible_7d' => $mature7->count(),
                    'retained_7d' => $retained7,
                    'retention_7d' => $mature7->isEmpty() ? null : round(($retained7 / $mature7->count()) * 100, 1),
                    'eligible_30d' => $mature30->count(),
                    'retained_30d' => $retained30,
                    'retention_30d' => $mature30->isEmpty() ? null : round(($retained30 / $mature30->count()) * 100, 1),
                ];
            })->sortByDesc(fn (object $row): float => (float) ($row->retention_30d ?? $row->retention_7d ?? -1))->values();
    }

    /** @return list<string> */
    private function conversionEvents(): array
    {
        return $this->eventNames->expand(array_values(array_filter((array) config('analytics.dashboard.conversion_events', []), 'is_string')));
    }
}
