<?php

namespace App\Core\Seo\Application;

use App\Core\Verticals\Application\VerticalContext;

final readonly class VerticalSeoContext
{
    public function __construct(
        private VerticalContext $verticalContext,
        private ToolSeoContext $toolSeoContext,
    ) {}

    /** @return array{title:string,description:string,keywords:list<string>,vertical:?string,canonical:?string} */
    public function defaults(?string $page = null): array
    {
        $slug = $this->verticalContext->slug();
        $global = (array) config('seo.global', []);
        $tool = $page === null ? $this->toolSeoContext->current() : null;
        $vertical = $slug !== null ? (array) config("seo.verticals.{$slug}", []) : [];
        $globalPage = $page !== null ? (array) data_get($global, "pages.{$page}", []) : [];
        $verticalPage = $page !== null ? (array) data_get($vertical, "pages.{$page}", []) : [];

        $defaultKeywords = array_values((array) ($verticalPage['keywords'] ?? $vertical['keywords'] ?? $globalPage['keywords'] ?? $global['keywords'] ?? []));

        return [
            'title' => (string) ($tool['title'] ?? $verticalPage['title'] ?? $vertical['title'] ?? $globalPage['title'] ?? $global['title'] ?? config('app.name', 'Prazzu Tools')),
            'description' => (string) ($tool['description'] ?? $verticalPage['description'] ?? $vertical['description'] ?? $globalPage['description'] ?? $global['description'] ?? ''),
            'keywords' => array_values(array_unique(array_merge((array) ($tool['keywords'] ?? []), $defaultKeywords))),
            'vertical' => (string) ($tool['vertical'] ?? ($slug ?? '')),
            'canonical' => $tool['canonical'] ?? null,
        ];
    }
}
