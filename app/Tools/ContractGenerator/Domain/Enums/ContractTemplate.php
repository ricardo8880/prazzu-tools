<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Enums;

enum ContractTemplate: string
{
    case ServiceStandard = 'servicos-padrao';
    case SaleStandard = 'venda-padrao';
    case ServiceConfidentiality = 'servicos-confidencialidade';
    case ServiceDataProtection = 'servicos-protecao-dados';
    case ServiceIntellectualProperty = 'servicos-propriedade-intelectual';
    case SaleWarranty = 'venda-garantia-entrega';

    public function label(): string
    {
        return match ($this) {
            self::ServiceStandard => 'Prestação de serviços — padrão',
            self::SaleStandard => 'Compra e venda de bem móvel — padrão',
            self::ServiceConfidentiality => 'Serviços com confidencialidade',
            self::ServiceDataProtection => 'Serviços com proteção de dados',
            self::ServiceIntellectualProperty => 'Serviços com propriedade intelectual',
            self::SaleWarranty => 'Compra e venda com garantia e entrega',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ServiceStandard => 'Modelo essencial para contratação e execução de serviços.',
            self::SaleStandard => 'Modelo essencial para venda e entrega de bem móvel.',
            self::ServiceConfidentiality => 'Modelo profissional com cláusula de confidencialidade pré-selecionada.',
            self::ServiceDataProtection => 'Modelo profissional com cláusula de proteção de dados pré-selecionada.',
            self::ServiceIntellectualProperty => 'Modelo profissional com cláusula de propriedade intelectual pré-selecionada.',
            self::SaleWarranty => 'Modelo profissional com cláusula reforçada de garantia e entrega pré-selecionada.',
        };
    }

    public function contractType(): ContractType
    {
        return match ($this) {
            self::SaleStandard, self::SaleWarranty => ContractType::MovableAssetSale,
            default => ContractType::ServiceProvision,
        };
    }

    public function isPlus(): bool
    {
        return ! in_array($this, [self::ServiceStandard, self::SaleStandard], true);
    }

    /** @return list<SmartClause> */
    public function presetClauses(): array
    {
        return match ($this) {
            self::ServiceConfidentiality => [SmartClause::Confidentiality],
            self::ServiceDataProtection => [SmartClause::DataProtection],
            self::ServiceIntellectualProperty => [SmartClause::IntellectualProperty],
            self::SaleWarranty => [SmartClause::WarrantyAndDelivery],
            default => [],
        };
    }

    public static function essentialFor(ContractType $type): self
    {
        return $type === ContractType::MovableAssetSale ? self::SaleStandard : self::ServiceStandard;
    }
}
