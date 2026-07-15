<?php

namespace App\Core\Analytics\Application\Queries;

use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\AnalyticsInsight;

final class InsightsAnalyticsQuery
{
    public function execute(AnalyticsPeriod $period, ?string $type = null, ?string $severity = null): array
    {
        $base = AnalyticsInsight::query()->whereDate('period_start', $period->start->toDateString())->whereDate('period_end', $period->end->toDateString());
        $items = (clone $base)->when($type, fn($q)=>$q->where('type',$type))->when($severity, fn($q)=>$q->where('severity',$severity))->orderByRaw("CASE severity WHEN 'danger' THEN 1 WHEN 'warning' THEN 2 WHEN 'info' THEN 3 ELSE 4 END")->latest('generated_at')->get();
        return [
            'period'=>$period,'insights'=>$items,
            'summary'=>['total'=>(clone $base)->count(),'alerts'=>(clone $base)->where('type','alert')->count(),'opportunities'=>(clone $base)->where('type','opportunity')->count(),'recommendations'=>(clone $base)->where('type','recommendation')->count(),'open'=>(clone $base)->where('status','open')->count()],
            'selected_type'=>$type,'selected_severity'=>$severity,
        ];
    }
}
