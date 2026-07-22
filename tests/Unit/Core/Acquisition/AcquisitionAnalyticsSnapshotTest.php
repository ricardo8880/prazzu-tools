<?php

namespace Tests\Unit\Core\Acquisition;

use App\Core\Acquisition\Domain\Data\AcquisitionAnalyticsSnapshot;
use Tests\TestCase;

final class AcquisitionAnalyticsSnapshotTest extends TestCase
{
    public function test_it_round_trips_a_valid_snapshot(): void
    {
        $snapshot = AcquisitionAnalyticsSnapshot::fromArray([
            'context_id' => 12,
            'context_name' => 'Rescisão Instagram',
            'keyword' => 'rescisao-video-01',
            'campaign_identifier' => 'instagram-rescisao',
            'primary_tool_slug' => 'calculadora-rescisao',
        ]);

        self::assertNotNull($snapshot);
        self::assertSame(12, $snapshot->contextId);
        self::assertSame('rescisao-video-01', $snapshot->keyword);
        self::assertSame('instagram-rescisao', $snapshot->campaignIdentifier);
        self::assertSame('calculadora-rescisao', $snapshot->primaryToolSlug);
        self::assertSame(12, $snapshot->toArray()['context_id']);
    }

    public function test_it_rejects_incomplete_stored_data(): void
    {
        self::assertNull(AcquisitionAnalyticsSnapshot::fromArray([]));
        self::assertNull(AcquisitionAnalyticsSnapshot::fromArray([
            'context_id' => 1,
            'context_name' => '',
            'keyword' => 'campanha',
        ]));
    }
}
