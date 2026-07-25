<?php

namespace App\Http\Controllers\Seo;

use App\Core\Tools\ToolCatalog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

final class ToolSitemapController extends Controller
{
    public function __invoke(ToolCatalog $catalog): Response
    {
        return response()
            ->view('seo.tools-sitemap', [
                'categories' => $catalog->categories(false),
                'tools' => $catalog->all(),
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
