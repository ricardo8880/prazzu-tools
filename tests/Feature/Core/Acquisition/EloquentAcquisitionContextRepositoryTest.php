<?php

namespace Tests\Feature\Core\Acquisition;

use App\Core\Acquisition\Contracts\AcquisitionContextRepository;
use App\Core\Acquisition\Domain\Enums\AcquisitionContextStatus;
use App\Core\Acquisition\Domain\Enums\AcquisitionContextToolPlacement;
use App\Core\Acquisition\Infrastructure\Persistence\AcquisitionContextArticleRecord;
use App\Core\Acquisition\Infrastructure\Persistence\AcquisitionContextRecord;
use App\Core\Acquisition\Infrastructure\Persistence\AcquisitionContextToolRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EloquentAcquisitionContextRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_only_active_contexts_with_ordered_relations(): void
    {
        $record = AcquisitionContextRecord::query()->create([
            'name' => 'Rescisão Instagram',
            'keyword' => 'rescisao-video-01',
            'campaign_identifier' => 'instagram-rescisao',
            'status' => AcquisitionContextStatus::Active,
            'hero_title_before' => 'Calcule corretamente',
            'hero_title_line' => 'a sua',
            'hero_title_highlight' => 'rescisão',
            'hero_description' => 'Faça uma simulação completa.',
            'hero_search_placeholder' => 'Buscar ferramentas...',
            'cta_title' => 'Comece agora',
            'cta_description' => 'Use a calculadora principal.',
            'cta_label' => 'Calcular rescisão',
            'cta_tool_slug' => 'calculadora-rescisao',
            'primary_tool_slug' => 'calculadora-rescisao',
        ]);

        AcquisitionContextToolRecord::query()->create([
            'acquisition_context_id' => $record->getKey(),
            'tool_slug' => 'calculadora-ferias',
            'placement' => AcquisitionContextToolPlacement::Featured,
            'position' => 2,
        ]);
        AcquisitionContextToolRecord::query()->create([
            'acquisition_context_id' => $record->getKey(),
            'tool_slug' => 'calculadora-rescisao',
            'placement' => AcquisitionContextToolPlacement::Featured,
            'position' => 1,
        ]);
        AcquisitionContextToolRecord::query()->create([
            'acquisition_context_id' => $record->getKey(),
            'tool_slug' => 'gerador-darf-gps',
            'placement' => AcquisitionContextToolPlacement::Recommended,
            'position' => 1,
        ]);

        AcquisitionContextArticleRecord::query()->create([
            'acquisition_context_id' => $record->getKey(),
            'article_slug' => 'direitos-na-demissao',
            'position' => 2,
        ]);
        AcquisitionContextArticleRecord::query()->create([
            'acquisition_context_id' => $record->getKey(),
            'article_slug' => 'como-calcular-rescisao',
            'position' => 1,
        ]);

        $context = $this->app->make(AcquisitionContextRepository::class)
            ->activeByKeyword('rescisao-video-01');

        self::assertNotNull($context);
        self::assertSame('instagram-rescisao', $context->campaignIdentifier);
        self::assertSame('rescisão', $context->hero->titleHighlight);
        self::assertSame('calculadora-rescisao', $context->callToAction->toolSlug);
        self::assertSame(
            ['calculadora-rescisao', 'calculadora-ferias'],
            $context->featuredToolSlugs,
        );
        self::assertSame(['gerador-darf-gps'], $context->recommendedToolSlugs);
        self::assertSame(
            ['como-calcular-rescisao', 'direitos-na-demissao'],
            $context->articleSlugs,
        );
    }

    public function test_it_returns_null_for_inactive_or_unknown_contexts(): void
    {
        AcquisitionContextRecord::query()->create([
            'name' => 'Campanha pausada',
            'keyword' => 'campanha-pausada',
            'status' => AcquisitionContextStatus::Inactive,
        ]);

        $repository = $this->app->make(AcquisitionContextRepository::class);

        self::assertNull($repository->activeByKeyword('campanha-pausada'));
        self::assertNull($repository->activeByKeyword('nao-existe'));
    }
}
