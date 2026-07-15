<?php

namespace App\Core\Analytics\Contracts;

use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use Illuminate\Http\Request;

interface PlatformAnalytics
{
    public function track(AnalyticsEvent $event, ?Request $request = null): void;

    /**
     * Compatibilidade com os módulos existentes. Novos módulos devem preferir track().
     *
     * @param array<string, mixed> $metadata
     */
    public function record(string $eventName, string $channel, Request $request, array $metadata = []): void;
}
