<?php

namespace App\Http\Controllers\Platform;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Tools\ToolCatalog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ToolCatalogController extends Controller
{
    public function __construct(private readonly ToolCatalog $catalog) {}

    public function index(
        Request $request,
        PlatformAnalytics $analytics,
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

        return view('pages.tools.index', [
            'tools' => $tools,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'query' => $query,
            'totalTools' => $this->catalog->all()->count(),
        ]);
    }
}
