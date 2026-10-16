<?php

namespace App\Core\ToolIntegration\Services;

use App\Core\ToolIntegration\Contracts\ToolIntegrationCatalog;
use App\Core\ToolIntegration\Data\IntegrationContract;
use App\Core\ToolIntegration\Data\IntegrationField;

final readonly class StandardIntegrationContracts
{
    public function __construct(private ToolIntegrationCatalog $catalog) {}

    public function register(): void
    {
        foreach ($this->contracts() as $contract) {
            if ($this->catalog->find($contract->name, $contract->version) === null) {
                $this->catalog->register($contract);
            }
        }
    }

    /** @return array<int, IntegrationContract> */
    private function contracts(): array
    {
        return [
            new IntegrationContract('company-operating-profile', 2, 'Operating profile used to estimate U.S. bookkeeping and accounting fees.', [
                new IntegrationField('monthly_revenue', 'string', true),
                new IntegrationField('employees', 'integer', true),
                new IntegrationField('owners', 'integer', true),
                new IntegrationField('entity_type', 'string', true),
                new IntegrationField('business_segment', 'string', true),
            ]),
            new IntegrationContract('pricing-scenario', 2, 'Individual pricing and margin scenario.', [
                new IntegrationField('product_name', 'string'),
                new IntegrationField('sale_price', 'string', true),
                new IntegrationField('margin', 'string', true),
                new IntegrationField('markup', 'string', true),
            ]),
            new IntegrationContract('payroll-calculation-snapshot', 2, 'Summary of a completed U.S. payroll or employment calculation.', [
                new IntegrationField('calculation_type', 'string', true),
                new IntegrationField('gross_total', 'string', true),
                new IntegrationField('net_total', 'string', true),
            ]),
        ];
    }
}
