<?php

namespace App\Core\Seo\Application;

use App\Core\Tools\ToolCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final readonly class ToolSeoContext
{
    public function __construct(
        private ToolCatalog $catalog,
        private Request $request,
    ) {}

    /** @return array{title:string,description:string,keywords:list<string>,vertical:string,canonical:string,slug:string}|null */
    public function current(): ?array
    {
        $routeName = $this->request->route()?->getName();

        if (! is_string($routeName) || ! str_starts_with($routeName, 'tools.')) {
            return null;
        }

        foreach ($this->catalog->forVertical(null, false) as $tool) {
            $indexRoute = (string) ($tool['route_name'] ?? '');
            if ($indexRoute === '') {
                continue;
            }

            $prefix = Str::endsWith($indexRoute, '.index')
                ? Str::beforeLast($indexRoute, '.index')
                : $indexRoute;

            if ($routeName !== $indexRoute && ! str_starts_with($routeName, $prefix.'.')) {
                continue;
            }

            return [
                'title' => trim((string) $tool['name']).' — Prazzu Tools',
                'description' => trim((string) $tool['description']),
                'keywords' => array_values(array_unique(array_filter(
                    (array) ($tool['keywords'] ?? []),
                    static fn (mixed $keyword): bool => is_string($keyword) && trim($keyword) !== '',
                ))),
                'vertical' => (string) $tool['vertical'],
                'canonical' => route($indexRoute),
                'slug' => (string) $tool['slug'],
            ];
        }

        return null;
    }
}
