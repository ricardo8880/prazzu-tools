# Calculadora de Parcelamento Tributário

Ferramenta paramétrica para estimar o parcelamento de uma dívida tributária a partir de valores informados pelo usuário.

## Descrição

Simula um parcelamento pelo método SAC com dívida, prazo e encargos definidos pelo usuário, sem representar programa fiscal oficial.

## Funcionalidades

- parcela média, primeira e última parcela, encargos e custo final;
- comparação de cenários e evolução do saldo no Plus;
- relatório em PDF e XLSX no Plus.

## Experiência Essencial

- dívida;
- quantidade de parcelas;
- encargos mensais informados;
- parcela média aproximada;
- primeira e última parcela;
- encargos estimados e custo final;
- memória de cálculo.

## Prazzu Plus

- entrada no cenário principal;
- comparação de múltiplos cenários de entrada, prazo e encargos;
- evolução mensal do saldo devedor;
- cronograma de amortização, encargos e parcelas;
- relatório/exportação em PDF e XLSX.

## Regras de cálculo

A versão 1.0.0 usa amortização constante (SAC). O saldo após a entrada é dividido pelo número de parcelas; em cada mês, os encargos são calculados sobre o saldo devedor inicial usando a taxa mensal informada. As parcelas diminuem ao longo do prazo, e o resumo exibe também a parcela média aproximada.

Todos os valores monetários são processados em centavos e os percentuais usam `Percentage`, sem ponto flutuante. Diferenças de arredondamento do principal são absorvidas ao longo do cronograma para que o saldo finalize em zero.

## Limites de escopo

A ferramenta não implementa regras normativas de um programa específico e não consulta Receita Federal, PGFN, Simples Nacional, estados ou municípios. Não infere entrada mínima, parcela mínima, descontos, SELIC, multa, juros, atualização monetária, honorários ou critérios de adesão. O usuário deve informar a taxa de encargos aplicável ao caso e validar as condições oficiais antes de decidir.

A comparação Plus existe somente dentro da simulação atual. Não há cadastro persistente de débitos, cobrança, negociação ou gestão fiscal.

## Dependências

- `Money`, `Percentage` e arredondamento determinístico do Core;
- memória de cálculo e exportadores compartilhados;
- regras de acesso Plus da plataforma.

## Histórico de versões

- `1.0.0` — implementação inicial em 12/08/2026.
