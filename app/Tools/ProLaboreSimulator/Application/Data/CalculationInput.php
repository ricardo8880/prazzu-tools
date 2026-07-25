<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator\Application\Data;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Tools\ProLaboreSimulator\Domain\Data\ProLaboreInput;
use App\Tools\ProLaboreSimulator\Domain\Enums\CompanyRegime;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $competence,
        public string $companyRegime,
        public string $grossProLabore,
        public int $dependents = 0,
        public string $otherOfficialSocialSecurity = '0',
    ) {}

    public function toDomain(): ProLaboreInput
    {
        return new ProLaboreInput(
            competence: Competence::fromString($this->competence),
            companyRegime: CompanyRegime::fromInput($this->companyRegime),
            grossAmount: Money::fromDecimal($this->grossProLabore),
            dependents: $this->dependents,
            otherOfficialSocialSecurity: Money::fromDecimal($this->otherOfficialSocialSecurity),
        );
    }

    public function toArray(): array
    {
        return [
            'competence' => $this->competence,
            'company_regime' => $this->companyRegime,
            'gross_pro_labore' => $this->grossProLabore,
            'dependents' => $this->dependents,
            'other_official_social_security' => $this->otherOfficialSocialSecurity,
        ];
    }
}
