<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Domain\Services;

use App\Tools\FederalPaymentGuideGenerator\Domain\Data\RevenueCode;
use App\Tools\FederalPaymentGuideGenerator\Domain\Enums\GuideType;

final class RevenueCodeCatalog
{
    /** @return list<RevenueCode> */
    public function all(): array
    {
        return [
            new RevenueCode(GuideType::Gps, '1007', 'Contribuinte individual — recolhimento mensal', 'monthly', 'Receita Federal — Códigos de Receita de Contribuição Previdenciária'),
            new RevenueCode(GuideType::Gps, '1104', 'Contribuinte individual — recolhimento trimestral', 'quarterly', 'Receita Federal — Códigos de Receita de Contribuição Previdenciária'),
            new RevenueCode(GuideType::Gps, '1163', 'Contribuinte individual — plano simplificado mensal', 'monthly', 'Receita Federal — Códigos de Receita de Contribuição Previdenciária'),
            new RevenueCode(GuideType::Gps, '1406', 'Segurado facultativo — recolhimento mensal', 'monthly', 'Receita Federal — Códigos de Receita de Contribuição Previdenciária'),
            new RevenueCode(GuideType::Darf, '0561', 'IRRF — rendimentos do trabalho assalariado', 'monthly', 'Receita Federal — Tabela de Código de Retenção de IR'),
            new RevenueCode(GuideType::Darf, '0588', 'IRRF — trabalho sem vínculo empregatício', 'monthly', 'Receita Federal — Tabela de Código de Retenção de IR'),
            new RevenueCode(GuideType::Darf, '1708', 'IRRF — serviços profissionais prestados por pessoa jurídica', 'monthly', 'Receita Federal — Tabela de Código de Retenção de IR'),
        ];
    }

    public function find(GuideType $type, string $code): ?RevenueCode
    {
        foreach ($this->all() as $entry) {
            if ($entry->guideType === $type && $entry->code === $code) {
                return $entry;
            }
        }

        return null;
    }
}
