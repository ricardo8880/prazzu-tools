<?php

declare(strict_types=1);
namespace App\Tools\InvoiceWithholdingCalculator\Application\Data;
use App\Core\Tools\Contracts\ToolCalculationInput;
final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $competence,
        public string $paymentReference,
        public string $paymentDescription,
        public string $grossValue,
        public bool $applyBackupWithholding=true,
        public string $backupWithholdingRate='24',
        public string $stateWithholding='0',
        public string $otherWithholding='0',
    ) {}
    public function toArray(): array { return get_object_vars($this); }
}
