<?php

namespace App\Http\Controllers\Seo;

use App\Blog\Models\BlogPost;
use App\Core\Verticals\Application\VerticalContext;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

final class BlogSitemapController extends Controller
{
    public function __invoke(VerticalContext $verticalContext): Response
    {
        $posts = BlogPost::query()
            ->forVertical($verticalContext->slug())
            ->publiclyAvailable()
            ->where('should_index', true)
            ->get(['slug', 'updated_at', 'content_updated_at', 'published_at']);

        return response()
            ->view('seo.blog-sitemap', compact('posts'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
