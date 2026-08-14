<?php

declare(strict_types=1);

namespace App\Tools\DigitalCertificateAnalyzer\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;
use DateTimeImmutable;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $pkcs12,
        public string $password,
        public string $originalName,
        public int $size,
        public DateTimeImmutable $referenceDate,
        public bool $includeTechnicalDetails = true,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'original_name' => $this->originalName,
            'size_bytes' => $this->size,
            'reference_date' => $this->referenceDate->format(DATE_ATOM),
            'technical_details' => $this->includeTechnicalDetails,
        ];
    }
}
