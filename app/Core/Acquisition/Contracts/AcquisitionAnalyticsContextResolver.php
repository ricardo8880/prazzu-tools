<?php

namespace App\Core\Acquisition\Contracts;

use App\Core\Acquisition\Domain\Data\AcquisitionAnalyticsSnapshot;
use Illuminate\Http\Request;

interface AcquisitionAnalyticsContextResolver
{
    public function resolve(Request $request): ?AcquisitionAnalyticsSnapshot;
}
