# Prazzu Plus — Lote 5 — Ajustes parciais A

## Estado reconstruído

O lote foi iniciado reconstruindo o projeto na ordem obrigatória: ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4. Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os quatro relatórios anteriores e `config/product_tools.php`.

## Escopo entregue

Foram saneadas as 10 ferramentas parciais previstas para este lote, sem alterar slugs, fórmulas Essenciais ou o inventário oficial:

- **Depreciação de Ativos** — `portfolio_projection` passou a ter autorização individual; a projeção patrimonial consolidada só é entregue quando essa feature está autorizada.
- **Comparador Tributário** — `multiple_scenarios` ganhou comparação concreta de cenários adicionais; `annual_projection` passou a controlar a visão anual da comparação.
- **Distribuição de Lucros** — `partners` passou a aceitar múltiplos sócios, com validação determinística de soma das participações em 100% e gate Plus também nas exportações que reutilizam esses dados.
- **Conversor Fiscal XML** — `document_comparison` foi materializada no processamento em lote, comparando os dois primeiros documentos por total, emitente e quantidade de itens.
- **DARF/GPS** — `favorites` passou a proteger a alteração de favorito e a leitura filtrada por favoritos, reutilizando `ToolRunFavorites`.
- **Parcelamento Tributário** — `balance_evolution` controla a entrega da evolução do saldo/cronograma; o relatório PDF/XLSX passou a ser protegido explicitamente por `report`.
- **MEI → Microempresa** — `annual_projection`, `business_costs` e `migration_point` passaram a ser autorizadas individualmente conforme os parâmetros avançados enviados.
- **ISS** — `monthly_consolidation` passou a controlar a entrega da consolidação mensal.
- **Distribuição de Lucros — Balanço × sem Balanço** — parâmetros de escrituração e planejamento avançado passam por `planning`.
- **DAS Retroativo** — cronogramas com mais de um mês passam pelo gate `regularization`; o cronograma não é entregue a Free no modo monetizado.

## Governança

As 14 chaves corrigidas foram removidas de `config/plus_feature_governance.php::legacy_debt`. Cada uma possui evidência de implementação, gate pelo authorizer/middleware central e teste explícito Free × Plus. As demais features Plus desses módulos que não pertencem ao escopo deste lote continuam na dívida legada e serão tratadas nos lotes seguintes.

Nenhum novo serviço transversal foi criado. Histórico/favoritos continuam no Core compartilhado, exportações continuam nos exportadores compartilhados e os cálculos avançados reutilizam os próprios serviços de domínio das ferramentas. Não surgiu novo gatilho de promoção em `CORE_CANDIDATES.md`.

## Validação local

- `php -l` passou nos arquivos PHP alterados/criados.
- `php artisan route:list --json` confirmou as rotas canônicas e legadas e os middlewares relevantes, incluindo `favorites` em DARF/GPS e `report` em Parcelamento Tributário.
- `tools:check-architecture` continua retornando 48 violações preexistentes; não há violação `tools.plus.*` nas 14 features retiradas da dívida neste lote.
- Smoke test direto confirmou a distribuição multi-sócio 60%/40% sobre R$ 50.000,00 em R$ 30.000,00 e R$ 20.000,00.
- A suíte PHPUnit integral continua indisponível neste ambiente pelas extensões PHP `dom`, `mbstring` e `xmlwriter` ausentes.

## Continuidade

Antes do Lote 6, reconstruir novamente: ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5. Depois reler os documentos obrigatórios da raiz e todos os relatórios de monetização antes de qualquer alteração.
