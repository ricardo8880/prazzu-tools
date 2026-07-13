<?php

namespace App\Core\Usage\Services;

use App\Core\Usage\Contracts\UsageMetrics;
use App\Core\Usage\Models\ToolUsageEvent;
use Illuminate\Support\Facades\Log;
use Throwable;

final class DatabaseUsageMetrics implements UsageMetrics
{
    public function record(string $toolSlug, string $event, ?int $userId = null, ?int $organizationId = null, ?int $durationMs = null): void
    {
        try {
            ToolUsageEvent::query()->create([
                'tool_slug' => $toolSlug,
                'user_id' => $userId,
                'organization_id' => $organizationId,
                'event' => $event,
                'duration_ms' => $durationMs,
                'occurred_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Não foi possível registrar métrica de ferramenta.', [
                'tool_slug' => $toolSlug,
                'event' => $event,
                'exception' => $exception::class,
            ]);
        }
    }
}
