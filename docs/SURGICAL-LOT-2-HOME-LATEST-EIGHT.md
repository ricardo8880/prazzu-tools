# Lote Cirúrgico 2 — Home com exatamente 8 ferramentas mais recentes

## Objetivo

Garantir que a Home apresente sempre exatamente oito ferramentas e que a seleção represente a ordem real de publicação documentada, sem reutilizar `featured` ou `position` como substitutos de recência.

## Estado anterior

- A Home concatenava `featured()` com `latest(8)` e aplicava apenas unicidade por slug.
- O resultado final podia ultrapassar oito ferramentas.
- `ToolCatalog::latest()` ordenava por `position`, campo editorial utilizado também para ordenar o catálogo.
- Contextos de aquisição podiam substituir a lista da Home por qualquer quantidade de ferramentas.

## Alterações realizadas

- Foi criado `release_order` para as 32 entradas de `config/product_tools.php`.
- A sequência foi reconstruída a partir dos lotes de implementação já documentados.
- `ToolCatalog::latest()` passou a ordenar exclusivamente por `release_order`.
- A Home deixou de concatenar destaques com ferramentas recentes.
- A Home padrão e a Home contextual passam a receber exatamente `latest(8)`.
- Contextos de aquisição continuam podendo alterar hero, CTA e título da secção, mas não alteram o conjunto das oito ferramentas mais recentes.
- Foram adicionados gates para garantir sequência completa, exclusiva e estável de 1 a 32.

## Oito ferramentas atuais na Home

1. Calculadora DIFAL / ICMS Interestadual + FCP
2. Calculadora de Hora Extra, Adicional Noturno e DSR
3. Calculadora de Salário Líquido
4. Gerador de Declaração de Trabalho/Renda
5. Gerador de Declaração de Rendimentos
6. Calculadora de Comissão de Vendedores
7. Calculadora de Precificação de Produtos
8. Calculadora de Ponto de Equilíbrio

## Regras de continuidade

- `position` continua sendo apenas ordenação editorial do catálogo.
- `featured` não controla mais a lista principal da Home.
- Toda nova ferramenta deve receber o próximo `release_order` em lote explícito.
- Substituições não devem reutilizar silenciosamente a ordem de outra ferramenta; a decisão deve ser documentada.
- A página Ferramentas continua exibindo as 32 ferramentas oficiais.
- Nenhum slug, rota ou módulo foi removido neste lote.

## Validação

- Sintaxe PHP dos ficheiros alterados: validada.
- Inventário: 32 valores únicos de `release_order`, cobrindo 1–32.
- Limite da Home: fixado em 8 no serviço compartilhado.
- PHPUnit completo permanece condicionado às extensões PHP documentadas pelo projeto.
