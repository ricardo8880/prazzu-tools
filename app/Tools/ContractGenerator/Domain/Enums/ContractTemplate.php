<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Enums;

enum ContractTemplate: string
{
    case ServiceStandard = 'service-standard';
    case SaleStandard = 'sale-standard';
    case ServiceConfidentiality = 'service-confidentiality';
    case ServicePrivacy = 'service-privacy';
    case ServiceIntellectualProperty = 'service-intellectual-property';
    case SaleWarranty = 'sale-warranty-delivery';

    public function label(): string
    {
        return match ($this) {
            self::ServiceStandard => 'Service agreement — standard',
            self::SaleStandard => 'Goods sale — standard',
            self::ServiceConfidentiality => 'Service agreement with confidentiality',
            self::ServicePrivacy => 'Service agreement with privacy clause',
            self::ServiceIntellectualProperty => 'Service agreement with IP clause',
            self::SaleWarranty => 'Goods sale with warranty and delivery',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ServiceStandard => 'General starting template for a U.S. service relationship.',
            self::SaleStandard => 'General starting template for the sale and delivery of goods.',
            self::ServiceConfidentiality => 'Service template with a confidentiality clause preselected.',
            self::ServicePrivacy => 'Service template with a privacy/data-handling clause preselected.',
            self::ServiceIntellectualProperty => 'Service template with an intellectual-property clause preselected.',
            self::SaleWarranty => 'Goods-sale template with a warranty and delivery clause preselected.',
        };
    }

    public function contractType(): ContractType
    {
        return match ($this) {
            self::SaleStandard, self::SaleWarranty => ContractType::GoodsSaleAgreement,
            default => ContractType::ServiceAgreement,
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
            self::ServicePrivacy => [SmartClause::DataProtection],
            self::ServiceIntellectualProperty => [SmartClause::IntellectualProperty],
            self::SaleWarranty => [SmartClause::WarrantyAndDelivery],
            default => [],
        };
    }

    public static function essentialFor(ContractType $type): self
    {
        return $type === ContractType::GoodsSaleAgreement ? self::SaleStandard : self::ServiceStandard;
    }
}
