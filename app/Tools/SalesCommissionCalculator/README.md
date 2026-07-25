# Calculadora de Comissão de Vendedores

## Descrição

Calcula base comissionável, comissão-base, bônus por meta, comissão total e atingimento da meta.

## Funcionalidades

- cálculo da base comissionável líquida de estornos;
- comissão-base, bônus por meta e comissão total;
- memória estruturada das bases e percentuais.

## Experiência Essencial

O cálculo individual completo permanece disponível sem autenticação e exibe a base, os percentuais e o total calculado.

## Prazzu Plus

Cenários, lotes, histórico e exportações representam produtividade e continuidade, sem alterar a regra Essencial.

## Regras

- `base comissionável = faturamento bruto - estornos e devoluções informados`;
- `comissão-base = base comissionável × percentual`;
- a meta é avaliada sobre a mesma base líquida de estornos;
- `bônus = base comissionável × percentual de bônus`, somente quando a meta é atingida;
- `comissão total = comissão-base + bônus`;
- estornos não podem ser negativos nem superar o faturamento bruto;
- valores monetários usam `Money` e o arredondamento monetário do Core;
- regras contratuais de competência, pagamento, teto, devolução futura ou cancelamento não informadas não são inferidas;
- o resultado é estimativo e deve ser confrontado com o contrato ou política comercial aplicável.

## Memória de cálculo

O resultado usa `CalculationMemory`, registra cada base e percentual utilizado e foi atualizado para o schema `1.1.0` no Lote 8.

## Integração entre ferramentas

O módulo permanece isolado e não publica nem consome contratos de outras ferramentas.

## Dependências

Usa `Money`, `Percentage`, arredondamento monetário e `CalculationMemory` do Core técnico.


## Histórico de versões

- `1.1.0`: base líquida de estornos, meta consistente e memória estruturada.
