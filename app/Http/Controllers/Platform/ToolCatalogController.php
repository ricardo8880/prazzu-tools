<?php

namespace App\Http\Controllers\Platform;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Tools\Discovery\Application\ProblemJourneyCatalog;
use App\Core\Tools\Favorites\Services\UserToolFavorites;
use App\Core\Tools\History\Application\Queries\UserToolContinuityQuery;
use App\Core\Tools\ToolCatalog;
use App\Core\Verticals\Application\VerticalContext;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ToolCatalogController extends Controller
{
    public function __construct(private readonly ToolCatalog $catalog) {}

    public function index(
        Request $request,
        PlatformAnalytics $analytics,
        UserToolFavorites $toolFavorites,
        UserToolContinuityQuery $continuity,
        ProblemJourneyCatalog $problemJourneys,
        VerticalContext $verticalContext,
        ?string $category = null,
    ): View {
        $query = trim((string) $request->query('q', ''));
        $query = mb_substr($query, 0, 255);
        $categories = $this->catalog->categories(false);
        $activeCategory = $category === null ? null : $categories->firstWhere('slug', $category);

        if ($category !== null && $activeCategory === null) {
            $categoryTools = $this->catalog->byCategory($category);
            $metadata = config("tools.categories.{$category}");

            if ($categoryTools->isNotEmpty() && is_array($metadata)) {
                $activeCategory = [
                    'slug' => $category,
                    'name' => $metadata['name'],
                    'icon' => $metadata['icon'],
                    'count' => $categoryTools->count(),
                    'url' => route('tools.category', ['category' => $category]),
                ];
            }
        }

        abort_if($category !== null && $activeCategory === null, 404);

        $tools = $this->catalog->search($query, $category);

        if (
            $query !== ''
            && $category === null
            && $request->query('source') === 'home_search'
        ) {
            $analytics->record(
                AnalyticsEventName::HomeSearchSubmitted->value,
                'platform',
                $request,
                [
                    'query' => $query,
                    'result_count' => $tools->count(),
                    'has_results' => $tools->isNotEmpty(),
                    'origin' => 'home',
                ],
            );
        }

        $searchToolCatalog = $this->catalog->all()->map(static fn (array $tool): array => [
            'slug' => $tool['slug'],
            'name' => $tool['name'],
            'description' => $tool['description'],
            'icon' => $tool['icon'],
            'category' => $tool['category'],
            'category_name' => $tool['category_name'],
            'keywords' => $tool['keywords'],
            'url' => route($tool['route_name']),
        ])->values();

        $favoriteToolSlugs = $request->user() === null
            ? collect()
            : $toolFavorites->forUser((int) $request->user()->getAuthIdentifier())
                ->pluck('slug')
                ->intersect($searchToolCatalog->pluck('slug'))
                ->values();

        $recentToolSlugs = $request->user() === null
            ? collect()
            : $continuity->recentTools(
                (int) $request->user()->getAuthIdentifier(),
                $verticalContext->slug(),
                6,
            )->pluck('tool_slug')->values();

        $featuredToolSlugs = $this->catalog->featured()->pluck('slug')->values();
        $personalizedToolSlugs = $favoriteToolSlugs
            ->concat($recentToolSlugs)
            ->unique()
            ->take(6)
            ->values();
        $personalizedTools = $personalizedToolSlugs
            ->map(static fn (string $slug): ?array => $searchToolCatalog->firstWhere('slug', $slug))
            ->filter()
            ->values();

        return view('pages.tools.index', [
            'tools' => $tools,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'query' => $query,
            'totalTools' => $this->catalog->all()->count(),
            'searchToolCatalog' => $searchToolCatalog,
            'favoriteToolSlugs' => $favoriteToolSlugs,
            'recentToolSlugs' => $recentToolSlugs,
            'featuredToolSlugs' => $featuredToolSlugs,
            'personalizedTools' => $query === '' && $activeCategory === null ? $personalizedTools : collect(),
            'problemJourneys' => $query === '' && $activeCategory === null
                ? $problemJourneys->forActiveVertical()
                : collect(),
        ]);
    }
}
