<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums\CompanyRegime;
use InvalidArgumentException;

final readonly class ProLaboreInput
{
    public function __construct(
        public Competence $competence,
        public CompanyRegime $companyRegime,
        public Money $grossAmount,
        public int $dependents = 0,
        public ?Money $otherOfficialSocialSecurity = null,
    ) {
        if ($grossAmount->minorAmount() < 0) {
            throw new InvalidArgumentException('O pró-labore bruto não pode ser negativo.');
        }

        if ($dependents < 0 || $dependents > 99) {
            throw new InvalidArgumentException('A quantidade de dependentes deve estar entre 0 e 99.');
        }

        if ($otherOfficialSocialSecurity?->minorAmount() < 0) {
            throw new InvalidArgumentException('A contribuição previdenciária de outros vínculos não pode ser negativa.');
        }
    }
}
