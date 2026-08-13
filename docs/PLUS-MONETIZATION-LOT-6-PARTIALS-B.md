# Prazzu Plus — Lote 6 — Ajustes parciais B

## Base reconstruída

Este lote foi executado sobre o estado acumulado reconstruído na ordem obrigatória:

1. ZIP original;
2. Prazzu Plus Lote 1;
3. Prazzu Plus Lote 2;
4. Prazzu Plus Lote 3;
5. Prazzu Plus Lote 4;
6. Prazzu Plus Lote 5.

Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos lotes anteriores e `config/product_tools.php`.

## Escopo

Foram saneadas as 10 ferramentas parciais restantes do relatório de monetização:

- DIFAL / ICMS;
- IRPJ/CSLL — Lucro Presumido;
- PIS/COFINS;
- ICMS-ST;
- Retenções na Nota Fiscal;
- Férias;
- Custo de Funcionário CLT;
- Salário Líquido;
- Hora Extra;
- Emissor de Recibos.

## Decisões

- DIFAL protege assistência interestadual, base dupla, FCP e exportação de forma independente.
- IRPJ/CSLL, PIS/COFINS, ICMS-ST, Retenções e Salário Líquido passam a expor histórico autenticado real, usando `ToolRunHistory` compartilhado e gate `history`.
- PIS/COFINS mantém o resultado essencial e bloqueia apenas a memória detalhada de fórmulas em `memory` quando monetizado para Free.
- Retenções mantém o resumo essencial e protege a conferência detalhada (`report`) e a memória de cálculo (`memory`). Gates já existentes de regras customizadas, múltiplas notas e exportação foram preservados.
- Férias mantém cálculo individual Essencial; o planejamento com múltiplos funcionários exige `multiple_employees` além de `vacation_planning`.
- Custo CLT mantém relatório individual Essencial; identidade do escritório exige `branded_report`, e a projeção de 12 meses do lote exige `projections`.
- Salário Líquido mantém salário base, INSS, IRRF e dependentes Essenciais; ganhos variáveis, descontos personalizados, histórico e exportação são gates Plus individuais.
- Hora Extra mantém hora normal e hora extra básica Essenciais; adicional noturno, DSR, reflexos e exportação são gates Plus individuais.
- Emissor de Recibos materializa `custom_branding` com nome do escritório, CNPJ/registro e rodapé opcional no preview e PDF; usar qualquer campo de identidade exige o gate Plus.

## Governança

As 23 features efetivamente saneadas neste lote foram removidas de `config/plus_feature_governance.php::legacy_debt`. Cada uma possui evidência de implementação, autorização central e teste Free × Plus.

Features Plus fora deste escopo permanecem congeladas na dívida legada para lotes posteriores, sem ampliar silenciosamente o escopo.

## Continuidade

Antes do próximo lote, reconstruir obrigatoriamente:

**ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5 → Lote 6**.

Não substituir o projeto por uma pasta parcial; cada ZIP de lote contém somente arquivos novos ou modificados daquele lote e deve ser aplicado sobre o estado acumulado anterior.
