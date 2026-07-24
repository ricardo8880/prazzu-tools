<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Enums;

enum ContractType: string
{
    case ServiceProvision = 'prestacao-servicos';
    case MovableAssetSale = 'compra-venda-bem-movel';

    public function label(): string
    {
        return match ($this) {
            self::ServiceProvision => 'Prestação de serviços',
            self::MovableAssetSale => 'Compra e venda de bem móvel',
        };
    }

    public function documentTitle(): string
    {
        return match ($this) {
            self::ServiceProvision => 'Contrato Particular de Prestação de Serviços',
            self::MovableAssetSale => 'Contrato Particular de Compra e Venda de Bem Móvel',
        };
    }

    public function firstPartyLabel(): string
    {
        return match ($this) {
            self::ServiceProvision => 'Contratante',
            self::MovableAssetSale => 'Vendedor',
        };
    }

    public function secondPartyLabel(): string
    {
        return match ($this) {
            self::ServiceProvision => 'Contratado',
            self::MovableAssetSale => 'Comprador',
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
