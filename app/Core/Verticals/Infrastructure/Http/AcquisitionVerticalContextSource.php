<?php

namespace App\Core\Verticals\Infrastructure\Http;

use App\Core\Acquisition\Domain\Data\AcquisitionContext;
use App\Core\Verticals\Contracts\VerticalContextSource;
use App\Core\Verticals\Contracts\VerticalRegistry;
use App\Core\Verticals\Domain\Data\Vertical;
use Illuminate\Http\Request;

final readonly class AcquisitionVerticalContextSource implements VerticalContextSource
{
    public function __construct(
        private VerticalRegistry $verticals,
    ) {}

    public function resolve(Request $request): ?Vertical
    {
        $context = $request->attributes->get('acquisition.context');

        if (! $context instanceof AcquisitionContext) {
            return null;
        }

        $slug = $this->mappedSlug('keywords', $context->keyword)
            ?? $this->mappedSlug('campaigns', $context->campaignIdentifier);

        return $slug !== null ? $this->verticals->find($slug) : null;
    }

    private function mappedSlug(string $signal, ?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $mapping = config("verticals.acquisition.{$signal}", []);

        if (! is_array($mapping)) {
            return null;
        }

        $slug = $mapping[$value] ?? null;

        return is_string($slug) && trim($slug) !== '' ? trim($slug) : null;
    }
}
