<?php

namespace App\Http\Controllers\Platform;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Tools\ToolCatalog;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class HomeSearchController extends Controller
{
    public function __invoke(Request $request, ToolCatalog $catalog, PlatformAnalytics $analytics): RedirectResponse
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return redirect()->route('tools.index');
        }

        $query = mb_substr($query, 0, 255);
        $results = $catalog->search($query);

        $analytics->record(
            AnalyticsEventName::HomeSearchSubmitted->value,
            'platform',
            $request,
            [
                'query' => $query,
                'result_count' => $results->count(),
                'has_results' => $results->isNotEmpty(),
                'origin' => 'home',
            ],
        );

        return redirect()->route('tools.index', ['q' => $query]);
    }
}
