{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('tools.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
@foreach ($categories as $category)
    <url>
        <loc>{{ $category['url'] }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
@endforeach
@foreach ($tools as $tool)
    <url>
        <loc>{{ route($tool['route_name']) }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
@endforeach
</urlset>
