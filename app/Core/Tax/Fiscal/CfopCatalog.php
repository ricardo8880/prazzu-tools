<?php

declare(strict_types=1);

namespace App\Core\Tax\Fiscal;

final class CfopCatalog
{
    private const CODES = [
            '1101' => ['description' => 'Compra para industrialização ou produção rural', 'scope' => 'Entrada dentro do estado'],
            '1102' => ['description' => 'Compra para comercialização', 'scope' => 'Entrada dentro do estado'],
            '1201' => ['description' => 'Devolução de venda de produção do estabelecimento', 'scope' => 'Entrada dentro do estado'],
            '1202' => ['description' => 'Devolução de venda de mercadoria adquirida ou recebida de terceiros', 'scope' => 'Entrada dentro do estado'],
            '1403' => ['description' => 'Compra para comercialização em operação com mercadoria sujeita à substituição tributária', 'scope' => 'Entrada dentro do estado'],
            '1551' => ['description' => 'Compra de bem para o ativo imobilizado', 'scope' => 'Entrada dentro do estado'],
            '1556' => ['description' => 'Compra de material para uso ou consumo', 'scope' => 'Entrada dentro do estado'],
            '2101' => ['description' => 'Compra para industrialização ou produção rural', 'scope' => 'Entrada de outro estado'],
            '2102' => ['description' => 'Compra para comercialização', 'scope' => 'Entrada de outro estado'],
            '2201' => ['description' => 'Devolução de venda de produção do estabelecimento', 'scope' => 'Entrada de outro estado'],
            '2202' => ['description' => 'Devolução de venda de mercadoria adquirida ou recebida de terceiros', 'scope' => 'Entrada de outro estado'],
            '2403' => ['description' => 'Compra para comercialização em operação com mercadoria sujeita à substituição tributária', 'scope' => 'Entrada de outro estado'],
            '2551' => ['description' => 'Compra de bem para o ativo imobilizado', 'scope' => 'Entrada de outro estado'],
            '2556' => ['description' => 'Compra de material para uso ou consumo', 'scope' => 'Entrada de outro estado'],
            '3101' => ['description' => 'Compra para industrialização ou produção rural', 'scope' => 'Entrada do exterior'],
            '3102' => ['description' => 'Compra para comercialização', 'scope' => 'Entrada do exterior'],
            '5101' => ['description' => 'Venda de produção do estabelecimento', 'scope' => 'Saída dentro do estado'],
            '5102' => ['description' => 'Venda de mercadoria adquirida ou recebida de terceiros', 'scope' => 'Saída dentro do estado'],
            '5201' => ['description' => 'Devolução de compra para industrialização ou produção rural', 'scope' => 'Saída dentro do estado'],
            '5202' => ['description' => 'Devolução de compra para comercialização', 'scope' => 'Saída dentro do estado'],
            '5405' => ['description' => 'Venda de mercadoria adquirida ou recebida de terceiros, sujeita à substituição tributária, na condição de contribuinte substituído', 'scope' => 'Saída dentro do estado'],
            '5551' => ['description' => 'Venda de bem do ativo imobilizado', 'scope' => 'Saída dentro do estado'],
            '5556' => ['description' => 'Devolução de compra de material de uso ou consumo', 'scope' => 'Saída dentro do estado'],
            '5905' => ['description' => 'Remessa para depósito fechado ou armazém geral', 'scope' => 'Saída dentro do estado'],
            '5910' => ['description' => 'Remessa em bonificação, doação ou brinde', 'scope' => 'Saída dentro do estado'],
            '5915' => ['description' => 'Remessa de mercadoria ou bem para conserto ou reparo', 'scope' => 'Saída dentro do estado'],
            '5916' => ['description' => 'Retorno de mercadoria ou bem recebido para conserto ou reparo', 'scope' => 'Saída dentro do estado'],
            '5949' => ['description' => 'Outra saída de mercadoria ou prestação de serviço não especificado', 'scope' => 'Saída dentro do estado'],
            '6101' => ['description' => 'Venda de produção do estabelecimento', 'scope' => 'Saída para outro estado'],
            '6102' => ['description' => 'Venda de mercadoria adquirida ou recebida de terceiros', 'scope' => 'Saída para outro estado'],
            '6201' => ['description' => 'Devolução de compra para industrialização ou produção rural', 'scope' => 'Saída para outro estado'],
            '6202' => ['description' => 'Devolução de compra para comercialização', 'scope' => 'Saída para outro estado'],
            '6404' => ['description' => 'Venda de mercadoria sujeita à substituição tributária, cujo imposto já tenha sido retido anteriormente', 'scope' => 'Saída para outro estado'],
            '6551' => ['description' => 'Venda de bem do ativo imobilizado', 'scope' => 'Saída para outro estado'],
            '6910' => ['description' => 'Remessa em bonificação, doação ou brinde', 'scope' => 'Saída para outro estado'],
            '6915' => ['description' => 'Remessa de mercadoria ou bem para conserto ou reparo', 'scope' => 'Saída para outro estado'],
            '6949' => ['description' => 'Outra saída de mercadoria ou prestação de serviço não especificado', 'scope' => 'Saída para outro estado'],
            '7101' => ['description' => 'Venda de produção do estabelecimento', 'scope' => 'Saída para o exterior'],
            '7102' => ['description' => 'Venda de mercadoria adquirida ou recebida de terceiros', 'scope' => 'Saída para o exterior'],
            '7949' => ['description' => 'Outra saída de mercadoria ou prestação de serviço não especificada', 'scope' => 'Saída para o exterior']
    ];

    /** @return array{description:string,scope:string}|null */
    public function find(string $code): ?array
    {
        $normalized = str_replace('.', '', trim($code));

        return self::CODES[$normalized] ?? null;
    }

    public function groupLabel(string $code): ?string
    {
        $first = str_replace('.', '', trim($code))[0] ?? '';
        return match ($first) {
            '1' => 'Entrada ou aquisição dentro do estado',
            '2' => 'Entrada ou aquisição de outro estado',
            '3' => 'Entrada ou aquisição do exterior',
            '5' => 'Saída ou prestação dentro do estado',
            '6' => 'Saída ou prestação para outro estado',
            '7' => 'Saída ou prestação para o exterior',
            default => null,
        };
    }

    public function isStructurallyValid(string $code): bool
    {
        return preg_match('/^[1-35-7]\d{3}$/', str_replace('.', '', trim($code))) === 1;
    }
}
