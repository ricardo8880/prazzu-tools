<?php

namespace App\Core\Analytics\Contracts;

use Illuminate\Http\Request;

interface PlatformAnalytics
{
    /** @param array<string, mixed> $metadata */
    public function record(string $eventName, string $channel, Request $request, array $metadata = []): void;
}
