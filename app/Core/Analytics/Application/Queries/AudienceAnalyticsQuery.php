<?php

namespace App\Core\Analytics\Application\Queries;

use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsVisitor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class AudienceAnalyticsQuery
{
    /** @return array<string, mixed> */
    public function execute(AnalyticsPeriod $period): array
    {
        $sessions = $this->sessions($period);
        $visitorIds = (clone $sessions)->whereNotNull('visitor_id')->distinct()->pluck('visitor_id');
        $visitors = AnalyticsVisitor::query()->whereIn('id', $visitorIds);

        $totalVisitors = $visitorIds->count();
        $newVisitors = (clone $visitors)->whereBetween('first_seen_at', [$period->start, $period->end])->count();
        $returningVisitors = max(0, $totalVisitors - $newVisitors);

        $devices = $this->breakdown($period, 'device_type', 'unknown');
        $countries = $this->breakdown($period, 'country_code', 'unknown', 30);
        $regions = $this->breakdown($period, 'region', 'unknown', 30);
        $cities = $this->breakdown($period, 'city', 'unknown', 30);

        return [
            'period' => $period,
            'summary' => [
                'visitors' => $totalVisitors,
                'new_visitors' => $newVisitors,
                'returning_visitors' => $returningVisitors,
                'returning_rate' => $totalVisitors > 0 ? round($returningVisitors / $totalVisitors * 100, 1) : 0.0,
                'sessions' => (clone $sessions)->count(),
                'identified_users' => (clone $sessions)->whereNotNull('user_id')->distinct()->count('user_id'),
                'countries' => $countries->where('label', '!=', 'unknown')->count(),
                'regions' => $regions->where('label', '!=', 'unknown')->count(),
                'cities' => $cities->where('label', '!=', 'unknown')->count(),
            ],
            'devices' => $devices,
            'browsers' => $this->breakdown($period, 'browser', 'unknown'),
            'operating_systems' => $this->breakdown($period, 'operating_system', 'unknown'),
            'languages' => $this->breakdown($period, 'language', 'unknown'),
            'timezones' => $this->breakdown($period, 'timezone', 'unknown'),
            'resolutions' => $this->breakdown($period, 'screen_resolution', 'unknown'),
            'countries' => $countries,
            'regions' => $regions,
            'cities' => $cities,
            'brazil_regions' => $this->brazilRegions($regions),
            'daily' => $this->daily($period),
        ];
    }

    private function sessions(AnalyticsPeriod $period): Builder
    {
        return AnalyticsSession::query()->whereBetween('started_at', [$period->start, $period->end]);
    }

    private function breakdown(AnalyticsPeriod $period, string $column, string $fallback, int $limit = 15): Collection
    {
        $rows = $this->sessions($period)
            ->selectRaw("COALESCE(NULLIF({$column}, ''), ?) as label, COUNT(*) as total, COUNT(DISTINCT visitor_id) as visitors", [$fallback])
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
        $total = max(1, (int) $rows->sum('total'));

        return $rows->map(function (object $row) use ($total): object {
            $row->total = (int) $row->total;
            $row->visitors = (int) $row->visitors;
            $row->percentage = round($row->total / $total * 100, 1);
            return $row;
        });
    }

    private function daily(AnalyticsPeriod $period): Collection
    {
        $sessions = $this->sessions($period)
            ->selectRaw('DATE(started_at) as day, COUNT(*) as sessions, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('day')->orderBy('day')->get()->keyBy('day');

        $new = AnalyticsVisitor::query()
            ->whereBetween('first_seen_at', [$period->start, $period->end])
            ->selectRaw('DATE(first_seen_at) as day, COUNT(*) as new_visitors')
            ->groupBy('day')->get()->keyBy('day');

        return collect(range(0, $period->days() - 1))->map(function (int $offset) use ($period, $sessions, $new): object {
            $day = $period->start->addDays($offset)->format('Y-m-d');
            return (object) [
                'day' => $day,
                'sessions' => (int) ($sessions->get($day)?->sessions ?? 0),
                'visitors' => (int) ($sessions->get($day)?->visitors ?? 0),
                'new_visitors' => (int) ($new->get($day)?->new_visitors ?? 0),
            ];
        });
    }

    private function brazilRegions(Collection $states): Collection
    {
        $groups = [
            'Norte' => ['AC','AP','AM','PA','RO','RR','TO'],
            'Nordeste' => ['AL','BA','CE','MA','PB','PE','PI','RN','SE'],
            'Centro-Oeste' => ['DF','GO','MT','MS'],
            'Sudeste' => ['ES','MG','RJ','SP'],
            'Sul' => ['PR','RS','SC'],
        ];

        return collect($groups)->map(function (array $codes, string $name) use ($states): object {
            $total = (int) $states->filter(fn (object $row) => in_array(strtoupper((string) $row->label), $codes, true))->sum('total');
            return (object) ['label' => $name, 'total' => $total];
        })->sortByDesc('total')->values();
    }
}
