<?php

namespace App\Http\Controllers\Platform;

use App\Core\Tools\ToolCatalog;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __invoke(ToolCatalog $catalog): View
    {
        return view('welcome', [
            'home' => config('home'),
            'categories' => $catalog->categories(),
            'featuredTools' => $catalog->latest(8),
        ]);
    }
}
