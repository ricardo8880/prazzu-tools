<?php

namespace App\Http\Requests\Admin\Analytics;

use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;

final class AcquisitionAnalyticsRequest extends AnalyticsDashboardRequest
{
    public function period(): AnalyticsPeriod
    {
        return parent::period();
    }
}
