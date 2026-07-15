<?php

namespace App\Console\Commands;

use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsVisitor;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Console\Command;

final class PruneAnalyticsDataCommand extends Command
{
    protected $signature = 'analytics:prune {--days= : Sobrescreve ANALYTICS_RETENTION_DAYS} {--chunk=1000 : Registros removidos por lote}';

    protected $description = 'Remove eventos antigos e registros analíticos órfãos conforme a política de retenção.';

    public function handle(): int
    {
        $days = max(1, (int) ($this->option('days') ?: config('analytics.retention_days', 730)));
        $chunk = max(100, min(10000, (int) $this->option('chunk')));
        $cutoff = now()->subDays($days);
        $deletedEvents = $this->deleteInChunks(PlatformAnalyticsEvent::query()->where('occurred_at', '<', $cutoff), $chunk);

        $orphanSessionCutoff = now()->subDays($days)->subDay();
        $deletedSessions = $this->deleteInChunks(
            AnalyticsSession::query()
                ->where('last_activity_at', '<', $orphanSessionCutoff)
                ->whereDoesntHave('events'),
            $chunk,
        );

        $deletedVisitors = $this->deleteInChunks(
            AnalyticsVisitor::query()
                ->where('last_seen_at', '<', $orphanSessionCutoff)
                ->whereDoesntHave('events')
                ->whereDoesntHave('sessions'),
            $chunk,
        );

        $this->components->info(sprintf(
            'Retenção aplicada: %d evento(s), %d sessão(ões) e %d visitante(s) removidos.',
            $deletedEvents,
            $deletedSessions,
            $deletedVisitors,
        ));

        return self::SUCCESS;
    }

    private function deleteInChunks($query, int $chunk): int
    {
        $deleted = 0;

        do {
            $ids = (clone $query)->limit($chunk)->pluck('id');
            $count = $ids->count();

            if ($count > 0) {
                $deleted += $query->getModel()::query()->whereKey($ids)->delete();
            }
        } while ($count === $chunk);

        return $deleted;
    }
}
