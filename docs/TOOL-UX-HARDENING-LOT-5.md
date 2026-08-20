# UX das ferramentas — Lote 5

## Objetivo

Fechar lacunas concretas de uso, acessibilidade e comportamento responsivo nas ferramentas sem reabrir frentes de UX já concluídas, sem alterar fórmulas e sem criar uma segunda infraestrutura visual.

## Continuidade

O estado foi reconstruído obrigatoriamente na ordem:

**ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5**

Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos Lotes 1–4, os relatórios `docs/UX-EXPERIENCE-LOT-*.md` e `config/product_tools.php`.

## Infraestrutura preservada

A auditoria confirmou que o projeto já possui e deve continuar reutilizando:

- `x-tools.page` com resumo compartilhado de validação;
- `x-tools.form-panel`, `x-tools.result-panel` e `x-tools.result-metric`;
- foco pós-resultado e reposicionamento dos tiers depois da entrega de valor;
- progressive disclosure compartilhado;
- próximos passos, continuidade, feedback de resolução e confiança normativa;
- Bootstrap como base responsiva existente.

Por isso, este lote não recria wrappers, sistema de mensagens, componentes de resultado nem camada de acessibilidade paralela.

## Validação acessível nos campos compartilhados

Os componentes `x-tools.form.input` e `x-tools.form.select` agora:

- expõem `aria-invalid="true"` quando o backend devolve erro para o campo;
- atribuem ID estável à mensagem de erro (`{campo}-error`);
- relacionam o campo à mensagem por `aria-describedby`;
- preservam simultaneamente o texto de ajuda no mesmo `aria-describedby` quando existe `help`.

O `x-tools.validation-summary` continua com `role="alert"` e passa a ser focável programaticamente com `tabindex="-1"`. As seis páginas legadas que ainda renderizam resumo próprio (`FederalPaymentGuideGenerator`, `MarginMarkupCalculator`, `ReceiptIssuer`, `AccountingFeesCalculator`, `VacationCalculator` e `LaborTerminationCalculator`) receberam o mesmo `data-testid`/`tabindex`, sem migração estrutural forçada para o wrapper moderno. Quando a página retorna com erros, o JavaScript compartilhado move o foco para esse resumo, sem alterar a validação Laravel ou o payload enviado.

## Feedback durante processamento

Formulários POST dentro de uma ferramenta agora recebem estado compartilhado de processamento:

- `aria-busy="true"` no formulário;
- região `role="status"`/`aria-live="polite"` criada somente durante a submissão;
- texto visível do submitter muda para `Processando...`;
- novo envio do mesmo formulário enquanto a primeira solicitação está pendente é bloqueado;
- exportações de arquivo continuam no fluxo específico já existente e não passam por esse estado.

O botão não é desabilitado nativamente antes da submissão, evitando remover acidentalmente `name/value` do submitter da requisição. O bloqueio de repetição é feito pelo estado do formulário.

## Tabelas em telas estreitas

A auditoria de todas as 50 views oficiais encontrou sete tabelas sem `table-responsive`:

- `PayslipGenerator`;
- `LateDasCalculator`;
- `LaborChargesCalculator`;
- `IncomeStatementGenerator`;
- `EmployerInssCalculator`;
- `ProLaboreSimulator`;
- `BreakEvenCalculator`.

Todas foram protegidas com o wrapper responsivo Bootstrap já utilizado pelas demais ferramentas. Conteúdo, valores, colunas e ordem dos resultados não mudaram.

## Gate de regressão

`ToolUxHardeningLot5Test` protege quatro invariantes:

1. campos compartilhados precisam comunicar erros por `aria-invalid` + `aria-describedby`;
2. o resumo de validação precisa permanecer focável;
3. formulários POST de ferramenta precisam manter feedback de processamento e proteção contra duplo envio;
4. qualquer `<table>` presente numa view oficial precisa ter cobertura equivalente de `table-responsive`.

## O que não mudou

- nenhuma fórmula, regra fiscal/trabalhista ou memória de cálculo;
- nenhum request/controller de domínio;
- nenhum slug, rota, ID, vertical ou `release_order`;
- nenhum status `active/beta`;
- nenhuma classificação Essencial/Plus;
- nenhuma persistência, evento de Analytics ou dado coletado;
- nenhuma nova dependência frontend/backend.

`config/product_tools.php` avança apenas a identificação técnica para schema `3.23.0` e `release_readiness = tool_ux_lot_5_hardened`.

## Continuidade para o Lote 6

Antes do próximo lote, reconstruir obrigatoriamente:

**ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5**

Reler novamente o README da raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos cinco lotes e `config/product_tools.php` antes de alterar qualquer arquivo.
