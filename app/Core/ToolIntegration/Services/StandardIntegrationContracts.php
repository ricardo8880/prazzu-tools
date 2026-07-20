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
            new IntegrationContract('company-tax-snapshot', 1, 'Resultado tributário mensal de uma empresa no Simples Nacional.', [
                new IntegrationField('monthly_revenue', 'string', true),
                new IntegrationField('rbt12', 'string', true),
                new IntegrationField('annex', 'string', true),
                new IntegrationField('effective_rate', 'string', true),
                new IntegrationField('estimated_das', 'string', true),
            ]),
            new IntegrationContract('company-operating-profile', 1, 'Perfil operacional informado para estimar honorários contábeis.', [
                new IntegrationField('monthly_revenue', 'string', true),
                new IntegrationField('employees', 'integer', true),
                new IntegrationField('partners', 'integer', true),
                new IntegrationField('tax_regime', 'string', true),
                new IntegrationField('business_segment', 'string', true),
            ]),
            new IntegrationContract('pricing-scenario', 1, 'Resultado individual de formação de preço.', [
                new IntegrationField('product_name', 'string'),
                new IntegrationField('sale_price', 'string', true),
                new IntegrationField('margin', 'string', true),
                new IntegrationField('markup', 'string', true),
            ]),
            new IntegrationContract('labor-calculation-snapshot', 1, 'Resumo de um cálculo trabalhista concluído.', [
                new IntegrationField('termination_type', 'string', true),
                new IntegrationField('gross_total', 'string', true),
                new IntegrationField('net_total', 'string', true),
            ]),
        ];
    }
}
