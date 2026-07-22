<?php

namespace App\Core\Acquisition\Infrastructure\Persistence;

use App\Core\Acquisition\Contracts\AcquisitionContextRepository;
use App\Core\Acquisition\Domain\Data\AcquisitionCallToAction;
use App\Core\Acquisition\Domain\Data\AcquisitionContext;
use App\Core\Acquisition\Domain\Data\AcquisitionHero;
use App\Core\Acquisition\Domain\Enums\AcquisitionContextStatus;
use App\Core\Acquisition\Domain\Enums\AcquisitionContextToolPlacement;
use App\Core\Acquisition\Infrastructure\Cache\AcquisitionContextCache;

final readonly class EloquentAcquisitionContextRepository implements AcquisitionContextRepository
{
    public function __construct(private AcquisitionContextCache $cache) {}

    public function activeByKeyword(string $keyword): ?AcquisitionContext
    {
        return $this->cache->remember($keyword, fn (): ?AcquisitionContext => $this->resolveActive($keyword));
    }

    private function resolveActive(string $keyword): ?AcquisitionContext
    {
        $record = AcquisitionContextRecord::query()
            ->with(['tools', 'articles'])
            ->where('keyword', $keyword)
            ->where('status', AcquisitionContextStatus::Active->value)
            ->first();

        if ($record === null) {
            return null;
        }

        $featuredToolSlugs = [];
        $recommendedToolSlugs = [];

        foreach ($record->tools as $tool) {
            if ($tool->placement === AcquisitionContextToolPlacement::Featured) {
                $featuredToolSlugs[] = $tool->tool_slug;
            }

            if ($tool->placement === AcquisitionContextToolPlacement::Recommended) {
                $recommendedToolSlugs[] = $tool->tool_slug;
            }
        }

        return new AcquisitionContext(
            id: (int) $record->getKey(),
            name: $record->name,
            keyword: $record->keyword,
            status: $record->status,
            campaignIdentifier: $record->campaign_identifier,
            hero: new AcquisitionHero(
                titleBefore: $record->hero_title_before,
                titleLine: $record->hero_title_line,
                titleHighlight: $record->hero_title_highlight,
                description: $record->hero_description,
                searchPlaceholder: $record->hero_search_placeholder,
            ),
            callToAction: new AcquisitionCallToAction(
                title: $record->cta_title,
                description: $record->cta_description,
                label: $record->cta_label,
                url: $record->cta_url,
                toolSlug: $record->cta_tool_slug,
            ),
            contextualMessage: $record->contextual_message,
            contextualContinueLabel: $record->contextual_continue_label,
            contextualContinueUrl: $record->contextual_continue_url,
            contextualContinueToolSlug: $record->contextual_continue_tool_slug,
            toolsSectionTitle: $record->tools_section_title,
            primaryToolSlug: $record->primary_tool_slug,
            featuredToolSlugs: $featuredToolSlugs,
            recommendedToolSlugs: $recommendedToolSlugs,
            articleSlugs: $record->articles->pluck('article_slug')->all(),
        );
    }
}
