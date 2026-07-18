<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Tools\SimplesNacionalCalculator\Infrastructure\Models\SimplesNacionalCalculation;
use App\Tools\SimplesNacionalCalculator\Infrastructure\Repositories\SimplesNacionalCalculationRepository;
use Illuminate\Support\Collection;

final readonly class ListSimplesNacionalCalculations
{
    public function __construct(private SimplesNacionalCalculationRepository $calculations) {}

    /** @return Collection<int, SimplesNacionalCalculation> */
    public function recent(?int $userId, int $limit = 24): Collection
    {
        if ($userId === null) {
            return collect();
        }

        return $this->calculations->recentForUser($userId, $limit);
    }
}
