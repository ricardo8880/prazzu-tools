# Lote 14 — Integração das três ferramentas da expansão

## Objetivo

Consolidar no produto as ferramentas implementadas nos Lotes 11, 12 e 13, sem alterar contratos públicos das 29 ferramentas preexistentes e sem criar dependência entre módulos.

## Estado de entrada

O lote parte obrigatoriamente da reconstrução:

1. ZIP original;
2. Lote 11 — Salário Líquido;
3. Lote 12 — Hora Extra, Adicional Noturno e DSR;
4. Lote 13 — DIFAL / ICMS / FCP.

Nesse estado existem 32 módulos em `app/Tools`: 20 ferramentas oficiais do ciclo original, 9 módulos adicionais preservados e 3 módulos de expansão aguardando promoção formal.

## Integração realizada

- `NetSalaryCalculator` foi promovido ao catálogo oficial como ID 21, chave `net-salary` e slug público preservado `calculadora-salario-liquido`.
- `OvertimeCalculator` foi promovido ao catálogo oficial como ID 22, chave `overtime` e slug público preservado `calculadora-hora-extra`.
- `DifalIcmsCalculator` foi promovido ao catálogo oficial como ID 23, chave `difal-icms` e slug público preservado `calculadora-difal-icms`.
- Os três registros temporários `expansion_lot_11`, `expansion_lot_12` e `expansion_lot_13` foram removidos de `additional_modules`.
- O inventário executável agora representa 23 ferramentas oficiais e 9 módulos adicionais, totalizando 32 módulos classificados exatamente uma vez.
- Busca, categorias, páginas relacionadas e sitemap continuam usando `ToolCatalog`/`ToolRegistry`; nenhuma lógica paralela foi criada. Como os três módulos já estavam registrados em `config/tools/modules.php` e visíveis em estado `beta`, a promoção de produto não exigiu duplicação de registro.
- Os testes arquiteturais foram atualizados para proteger IDs 1–23, unicidade de slug/chave/módulo e a promoção explícita das três ferramentas.

## Compatibilidade preservada

- Nenhum slug das 29 ferramentas preexistentes foi alterado.
- Nenhum módulo preexistente foi removido.
- `ProLaboreProfitDistributionCalculator` continua classificado como compatibilidade legada até auditoria específica.
- Os schemas de resultado `1.0.0` das três ferramentas da expansão permanecem inalterados.
- O lote não move regras de domínio para o Core e não cria integração direta entre ferramentas.

## Core Candidates

A integração não ativou novo gatilho de extração. Catálogo, busca, sitemap, histórico, exportação e descoberta já pertencem ao Core e são reutilizados pelas três ferramentas.

## Prontidão

`config/product_tools.php` passa a usar `release_readiness = expansion_lot_14_integrated`. Isso registra consolidação de produto, não substitui a auditoria final da expansão. A prontidão definitiva continua reservada ao próximo lote de auditoria/regressão e ao `composer release:check` no CI oficial.

## Continuidade para o Lote 15

Antes de iniciar a auditoria final:

1. reler o ZIP original;
2. reaplicar os Lotes 11, 12, 13 e 14 em ordem;
3. reler `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md` e `config/product_tools.php`;
4. confirmar 32 módulos classificados, 23 oficiais e 9 adicionais;
5. executar regressão global e checks de release sem alterar contratos apenas para satisfazer testes.
