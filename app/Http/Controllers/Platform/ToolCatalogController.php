<?php

namespace App\Http\Controllers\Platform;

use App\Core\Tools\ToolCatalog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ToolCatalogController extends Controller
{
    public function __construct(private readonly ToolCatalog $catalog) {}

    public function index(Request $request, ?string $category = null): View
    {
        $query = trim((string) $request->query('q', ''));
        $categories = $this->catalog->categories(false);
        $activeCategory = $category === null ? null : $categories->firstWhere('slug', $category);

        abort_if($category !== null && $activeCategory === null, 404);

        return view('pages.tools.index', [
            'tools' => $this->catalog->search($query, $category),
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'query' => $query,
            'totalTools' => $this->catalog->all()->count(),
        ]);
    }
}
