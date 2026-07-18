<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Tools\SimplesNacionalCalculator\Infrastructure\Repositories\SimplesNacionalCalculationRepository;

final readonly class DeleteSimplesNacionalCalculation
{
    public function __construct(private SimplesNacionalCalculationRepository $calculations) {}

    public function execute(int $calculationId, int $userId): void
    {
        $this->calculations->deleteOwned($calculationId, $userId);
    }
}
