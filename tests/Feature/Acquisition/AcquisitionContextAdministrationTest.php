<?php

namespace Tests\Feature\Acquisition;

use App\Core\Acquisition\Contracts\AcquisitionContextAdministration;
use App\Core\Acquisition\Infrastructure\Persistence\AcquisitionContextRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AcquisitionContextAdministrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_duplicates_a_context_as_inactive_with_unique_keyword_and_creative_metadata(): void
    {
        $context = AcquisitionContextRecord::query()->create([
            'name' => 'Campanha original',
            'keyword' => 'campanha-original',
            'campaign_identifier' => 'campanha-julho',
            'campaign_source' => 'instagram',
            'campaign_medium' => 'social-video',
            'content_identifier' => 'conteudo-01',
            'video_identifier' => 'video-01',
            'banner_identifier' => 'banner-01',
            'cta_identifier' => 'cta-01',
            'status' => 'active',
        ]);

        $copyId = app(AcquisitionContextAdministration::class)->duplicate((int) $context->getKey());
        $copy = AcquisitionContextRecord::query()->findOrFail($copyId);

        self::assertSame('inactive', $copy->status->value);
        self::assertSame('campanha-original-copy', $copy->keyword);
        self::assertSame('instagram', $copy->campaign_source);
        self::assertSame('video-01', $copy->video_identifier);
        self::assertSame('banner-01', $copy->banner_identifier);
        self::assertSame('cta-01', $copy->cta_identifier);
    }
}
