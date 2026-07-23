<?php

declare(strict_types=1);

namespace App\Core\Analytics\Application\Services;

use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Carbon\CarbonImmutable;

final class HistoricalAnalyticsDeduplicator
{
    /**
     * Finds only duplicates that match the same logical identity, subject and
     * event-specific metadata inside the configured collection window.
     *
     * @return array{scanned:int,duplicates:int,deleted:int,duplicate_ids:list<int>}
     */
    public function run(bool $apply = false, ?CarbonImmutable $from = null, ?CarbonImmutable $to = null, int $chunkSize = 1000): array
    {
        $query = PlatformAnalyticsEvent::query()
            ->orderBy('occurred_at')
            ->orderBy('id');

        if ($from !== null) {
            $query->where('occurred_at', '>=', $from);
        }

        if ($to !== null) {
            $query->where('occurred_at', '<=', $to);
        }

        $scanned = 0;
        $duplicateIds = [];
        $lastAcceptedAt = [];

        foreach ($query->cursor() as $event) {
                ++$scanned;

                $window = $this->windowFor((string) $event->event_name);
                if ($window <= 0) {
                    continue;
                }

                $fingerprint = $this->fingerprint($event);
                $occurredAt = CarbonImmutable::instance($event->occurred_at);
                $previousAt = $lastAcceptedAt[$fingerprint] ?? null;

                if ($previousAt instanceof CarbonImmutable && $previousAt->diffInSeconds($occurredAt) <= $window) {
                    $duplicateIds[] = (int) $event->id;
                    continue;
                }

                $lastAcceptedAt[$fingerprint] = $occurredAt;
        }

        $deleted = 0;
        if ($apply && $duplicateIds !== []) {
            foreach (array_chunk($duplicateIds, max(100, min(5000, $chunkSize))) as $ids) {
                $deleted += PlatformAnalyticsEvent::query()->whereKey($ids)->delete();
            }
        }

        return [
            'scanned' => $scanned,
            'duplicates' => count($duplicateIds),
            'deleted' => $deleted,
            'duplicate_ids' => $duplicateIds,
        ];
    }

    private function windowFor(string $eventName): int
    {
        // Event names contain dots (for example, page.viewed). Reading them
        // through config("....$eventName") makes Laravel interpret each dot
        // as a nested key, so the configured window is never found.
        $historyWindows = (array) config('analytics.history_repair.event_windows', []);
        if (array_key_exists($eventName, $historyWindows)) {
            return (int) $historyWindows[$eventName];
        }

        $collectionWindows = (array) config('analytics.deduplication.event_windows', []);

        return (int) ($collectionWindows[$eventName] ?? 0);
    }

    private function fingerprint(PlatformAnalyticsEvent $event): string
    {
        $metadata = is_array($event->metadata) ? $event->metadata : [];
        $metadataKeys = (array) config('analytics.history_repair.identity_metadata_keys', []);
        $relevantMetadata = [];

        foreach ($metadataKeys as $key) {
            if (array_key_exists($key, $metadata)) {
                $relevantMetadata[$key] = $metadata[$key];
            }
        }

        ksort($relevantMetadata);

        return hash('sha256', json_encode([
            'event' => $event->event_name,
            'channel' => $event->channel,
            'identity' => $event->analytics_session_id
                ?: $event->visitor_id
                ?: ($event->user_id !== null
                    ? 'user:'.$event->user_id
                    : ($event->session_id !== null ? 'session:'.$event->session_id : 'ip:'.$event->ip_hash)),
            'subject_type' => $event->subject_type,
            'subject_id' => $event->subject_id,
            'subject_slug' => $event->subject_slug,
            'path' => $event->subject_type === null ? $event->path : null,
            'metadata' => $relevantMetadata,
        ], JSON_THROW_ON_ERROR));
    }
}
