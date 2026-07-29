<?php

declare(strict_types=1);

namespace App\Core\Tools\Analytics\Services;

use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
use App\Core\Tools\ToolRegistry;
use LogicException;

final readonly class ToolAnalyticsJourneyRegistry
{
    public function __construct(private ToolRegistry $tools) {}

    public function find(string $toolSlug): ?ToolAnalyticsJourney
    {
        $module = $this->tools->findModule($toolSlug);

        if (! $module instanceof HasAnalyticsJourney) {
            return null;
        }

        $journey = $module->analyticsJourney();

        if ($journey->toolSlug !== $toolSlug) {
            throw new LogicException("A jornada [{$journey->toolSlug}] não corresponde ao módulo [{$toolSlug}].");
        }

        return $journey;
    }

    /** @return array<string, ToolAnalyticsJourney> */
    public function all(): array
    {
        $journeys = [];

        foreach (array_keys($this->tools->modules()) as $toolSlug) {
            $journey = $this->find($toolSlug);
            if ($journey !== null) {
                $journeys[$toolSlug] = $journey;
            }
        }

        return $journeys;
    }
}
