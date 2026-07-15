<?php

namespace App\Core\Analytics\Contracts;

use App\Core\Analytics\Domain\ValueObjects\AnalyticsContext;
use Illuminate\Http\Request;

interface AnalyticsContextResolver
{
    public function resolve(?Request $request = null): AnalyticsContext;
}
