<?php

namespace App\Http\Controllers\Platform;

use App\Core\Acquisition\Application\Home\BuildContextualHome;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __invoke(Request $request, BuildContextualHome $home): View
    {
        return view('welcome', $home->execute(
            keyword: $request->query('context'),
            defaultHome: config('home'),
        ));
    }
}
