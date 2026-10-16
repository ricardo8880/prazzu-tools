<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Enums;

enum ContractType: string
{
    case ServiceAgreement = 'service-agreement';
    case GoodsSaleAgreement = 'goods-sale-agreement';

    public function label(): string
    {
        return match ($this) {
            self::ServiceAgreement => 'Service agreement',
            self::GoodsSaleAgreement => 'Goods sale agreement',
        };
    }

    public function documentTitle(): string
    {
        return match ($this) {
            self::ServiceAgreement => 'Service Agreement',
            self::GoodsSaleAgreement => 'Goods Sale Agreement',
        };
    }

    public function firstPartyLabel(): string
    {
        return match ($this) {
            self::ServiceAgreement => 'Client',
            self::GoodsSaleAgreement => 'Seller',
        };
    }

    public function secondPartyLabel(): string
    {
        return match ($this) {
            self::ServiceAgreement => 'Service Provider',
            self::GoodsSaleAgreement => 'Buyer',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $type) {
            $options[$type->value] = $type->label();
        }
        return $options;
    }
}
