<?php

namespace App\Core\Seo\Application;

use App\Core\Verticals\Application\VerticalContext;

final readonly class VerticalSeoContext
{
    public function __construct(private VerticalContext $verticalContext) {}

    /** @return array{title:string,description:string,keywords:list<string>,vertical:?string} */
    public function defaults(?string $page = null): array
    {
        $slug = $this->verticalContext->slug();
        $global = (array) config('seo.global', []);
        $vertical = $slug !== null ? (array) config("seo.verticals.{$slug}", []) : [];
        $globalPage = $page !== null ? (array) data_get($global, "pages.{$page}", []) : [];
        $verticalPage = $page !== null ? (array) data_get($vertical, "pages.{$page}", []) : [];

        return [
            'title' => (string) ($verticalPage['title'] ?? $vertical['title'] ?? $globalPage['title'] ?? $global['title'] ?? config('app.name', 'Prazzu Tools')),
            'description' => (string) ($verticalPage['description'] ?? $vertical['description'] ?? $globalPage['description'] ?? $global['description'] ?? ''),
            'keywords' => array_values((array) ($verticalPage['keywords'] ?? $vertical['keywords'] ?? $globalPage['keywords'] ?? $global['keywords'] ?? [])),
            'vertical' => $slug,
        ];
    }
}
