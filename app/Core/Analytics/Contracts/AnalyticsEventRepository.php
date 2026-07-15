<?php

namespace App\Core\Analytics\Contracts;

use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsContext;

interface AnalyticsEventRepository
{
    public function store(AnalyticsEvent $event, AnalyticsContext $context): void;
}
