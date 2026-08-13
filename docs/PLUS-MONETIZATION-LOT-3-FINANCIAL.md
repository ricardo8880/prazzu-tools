# Prazzu Plus — Lote 3 — Financeiro e retirada de sócios

## Estado reconstruído

O trabalho partiu do ZIP original com reaplicação, em ordem, dos Lotes 1 e 2. Foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios de monetização anteriores e `config/product_tools.php`.

## Escopo entregue

- Capital de Giro: projeção de cenário real, protegida por `projections`.
- Fluxo de Caixa: `advanced_productivity` substituída por `cash_flow_scenarios`, comparando base, conservador e otimista.
- Ponto de Equilíbrio: `advanced_productivity` substituída por `scenario_comparison`, com alteração parametrizada de custos e preço.
- Comissão de Vendedores: `advanced_productivity` substituída por `batch_sellers`, processando de 2 a 50 vendedores sob uma regra comum.
- Pró-Labore Ideal: `scenarios` materializada por comparação anual de 2 a 4 valores mensais nas 12 competências de 2026.
- Planejador de Retirada de Sócios: cenários existentes ligados a `scenario_planning`; histórico, gravação e exportações ligados a `history_exports`.

Todos os cálculos Essenciais permanecem livres de gate Plus. A autorização usa exclusivamente `tool.feature`/`ToolFeatureRequestAuthorizer` e a persistência compartilhada já existente.

## Governança

As sete features corrigidas saíram da dívida legada do Lote 1 e agora ficam submetidas ao `PlusFeatureReadinessInspector`. Cada módulo possui teste explícito com `SubscriptionPlan::Free` e `SubscriptionPlan::Plus`.

## Validação local

Os arquivos PHP alterados passaram por `php -l`. `tools:check-architecture` continua retornando as 48 violações preexistentes já observadas fora deste lote; nenhuma violação Plus nova foi reportada para os seis módulos deste lote. A suíte PHPUnit integral continua indisponível neste ambiente pela ausência das extensões PHP `dom`, `mbstring` e `xmlwriter`.

## Continuidade

Antes do Lote 4, reconstruir novamente o estado a partir do ZIP original e reaplicar Lotes 1, 2 e 3, em ordem. Não recriar a fundação de autorização e não reintroduzir `advanced_productivity` nestes módulos.
