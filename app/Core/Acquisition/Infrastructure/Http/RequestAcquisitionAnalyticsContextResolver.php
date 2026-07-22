<?php

namespace App\Core\Acquisition\Infrastructure\Http;

use App\Core\Acquisition\Application\ResolveAcquisitionContext;
use App\Core\Acquisition\Contracts\AcquisitionAnalyticsContextResolver;
use App\Core\Acquisition\Domain\Data\AcquisitionAnalyticsSnapshot;
use Illuminate\Http\Request;

final readonly class RequestAcquisitionAnalyticsContextResolver implements AcquisitionAnalyticsContextResolver
{
    private const SESSION_KEY = 'acquisition.analytics_snapshot';

    public function __construct(
        private ResolveAcquisitionContext $contexts,
    ) {}

    public function resolve(Request $request): ?AcquisitionAnalyticsSnapshot
    {
        $queryContext = $request->query('context');
        $keyword = is_string($queryContext) ? trim($queryContext) : '';

        if ($keyword !== '') {
            $context = $this->contexts->execute($keyword);

            if ($context !== null) {
                $snapshot = AcquisitionAnalyticsSnapshot::fromContext($context);
                $this->persist($request, $snapshot);

                return $snapshot;
            }
        }

        if (! $request->hasSession()) {
            return null;
        }

        $stored = $request->session()->get(self::SESSION_KEY);

        return is_array($stored) ? AcquisitionAnalyticsSnapshot::fromArray($stored) : null;
    }

    private function persist(Request $request, AcquisitionAnalyticsSnapshot $snapshot): void
    {
        if ($request->hasSession()) {
            $request->session()->put(self::SESSION_KEY, $snapshot->toArray());
        }
    }
}
