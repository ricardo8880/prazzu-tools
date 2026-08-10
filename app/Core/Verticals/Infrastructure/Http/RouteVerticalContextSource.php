<?php

namespace App\Core\Verticals\Infrastructure\Http;

use App\Core\Verticals\Contracts\VerticalContextSource;
use App\Core\Verticals\Contracts\VerticalRegistry;
use App\Core\Verticals\Domain\Data\Vertical;
use Illuminate\Http\Request;

final readonly class RouteVerticalContextSource implements VerticalContextSource
{
    public function __construct(private VerticalRegistry $verticals) {}

    public function resolve(Request $request): ?Vertical
    {
        $slug = $request->route('vertical');

        if (! is_string($slug) || trim($slug) === '') {
            return null;
        }

        return $this->verticals->findByPublicSlug(trim($slug));
    }
}
