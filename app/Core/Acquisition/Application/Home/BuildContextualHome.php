<?php

namespace App\Core\Acquisition\Application\Home;

use App\Core\Acquisition\Application\ResolveAcquisitionContext;
use App\Core\Acquisition\Domain\Data\AcquisitionContext;
use App\Core\Tools\ToolCatalog;
use Illuminate\Support\Collection;

final readonly class BuildContextualHome
{
    public function __construct(
        private ResolveAcquisitionContext $resolveContext,
        private ToolCatalog $tools,
    ) {}

    /**
     * @param array<string, mixed> $defaultHome
     * @return array{home: array<string, mixed>, categories: Collection<int, array<string, mixed>>, featuredTools: Collection<int, array<string, mixed>>, acquisitionContext: ?AcquisitionContext}
     */
    public function execute(mixed $keyword, array $defaultHome): array
    {
        $context = $this->resolveContext->execute(is_string($keyword) ? $keyword : null);

        if ($context === null) {
            return $this->defaultPayload($defaultHome);
        }

        return [
            'home' => $this->contextualHome($defaultHome, $context),
            'categories' => $this->tools->categories(),
            'featuredTools' => $this->contextualTools($context),
            'acquisitionContext' => $context,
        ];
    }

    /** @param array<string, mixed> $defaultHome */
    private function defaultPayload(array $defaultHome): array
    {
        return [
            'home' => $defaultHome,
            'categories' => $this->tools->categories(),
            'featuredTools' => $this->tools->latest(8),
            'acquisitionContext' => null,
        ];
    }

    /**
     * @param array<string, mixed> $defaultHome
     * @return array<string, mixed>
     */
    private function contextualHome(array $defaultHome, AcquisitionContext $context): array
    {
        $home = $defaultHome;
        $hero = is_array($home['hero'] ?? null) ? $home['hero'] : [];
        $cta = is_array($home['cta'] ?? null) ? $home['cta'] : [];

        $home['hero'] = array_replace($hero, array_filter([
            'title_before' => $context->hero->titleBefore,
            'title_line' => $context->hero->titleLine,
            'title_highlight' => $context->hero->titleHighlight,
            'description' => $context->hero->description,
            'search_placeholder' => $context->hero->searchPlaceholder,
        ], $this->hasContent(...)));

        if ($this->hasContent($context->toolsSectionTitle)) {
            $home['tools_section_title'] = $context->toolsSectionTitle;
        }

        $home['cta'] = array_replace($cta, array_filter([
            'title' => $context->callToAction->title,
            'description' => $context->callToAction->description,
            'label' => $context->callToAction->label,
            'url' => $this->callToActionUrl($context),
        ], $this->hasContent(...)));

        return $home;
    }

    /** @return Collection<int, array<string, mixed>> */
    private function contextualTools(AcquisitionContext $context): Collection
    {
        $slugs = collect([$context->primaryToolSlug])
            ->merge($context->featuredToolSlugs)
            ->filter($this->hasContent(...))
            ->unique()
            ->values();

        if ($slugs->isEmpty()) {
            return $this->tools->latest(8);
        }

        $resolved = $slugs
            ->map(fn (string $slug): ?array => $this->tools->find($slug))
            ->filter(static fn (?array $tool): bool => $tool !== null)
            ->values();

        return $resolved->isEmpty() ? $this->tools->latest(8) : $resolved;
    }

    private function callToActionUrl(AcquisitionContext $context): ?string
    {
        $toolSlug = $context->callToAction->toolSlug;

        if ($this->hasContent($toolSlug)) {
            $tool = $this->tools->find($toolSlug);

            if ($tool !== null && is_string($tool['route_name'] ?? null)) {
                return route($tool['route_name']);
            }
        }

        return $context->callToAction->url;
    }

    private function hasContent(mixed $value): bool
    {
        return is_string($value) && trim($value) !== '';
    }
}
