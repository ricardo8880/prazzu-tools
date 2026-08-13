# Prazzu Plus — Lote 4 — Críticas restantes

## Estado reconstruído

O lote partiu do ZIP original com reaplicação, em ordem, dos Lotes 1, 2 e 3. Foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios anteriores e `config/product_tools.php` antes das alterações.

## Escopo entregue

As 11 ferramentas críticas restantes deixaram de usar `advanced_productivity`/`advanced_analysis` como promessa comercial genérica.

Dez módulos passaram a declarar um benefício concreto já materializado pela infraestrutura compartilhada de exportação:

- Simulador de Fator R — `spreadsheet_export`;
- DAS em Atraso — `spreadsheet_export`;
- Encargos Trabalhistas — `spreadsheet_export`;
- CLT × PJ × Autônomo — `spreadsheet_export`;
- INSS Patronal — `spreadsheet_export`;
- Admissão — `spreadsheet_export`;
- Reajuste Salarial — `spreadsheet_export`;
- Holerite — `spreadsheet_export`;
- Declaração de Rendimentos — `spreadsheet_export`;
- Declaração Trabalho/Renda — `spreadsheet_export`.

Em todos eles o cálculo/geração principal continua Essencial e a exportação PDF continua disponível sem gate Plus. Somente a exportação avançada em planilha é protegida por `tool.feature:<slug>,spreadsheet_export`. O `ToolExportPolicy` passou a declarar `xlsx`, refletindo o formato que o `SpreadsheetExporter` já entrega.

Durante a validação do Turnover também foi corrigido um defeito preexistente na memória do cálculo Essencial: a movimentação média era enviada como `float` para `CalculationMemoryStep`, cujo contrato aceita `int|string`. A média agora é representada deterministicamente como inteiro ou texto terminado em `,5`, sem aritmética financeira em ponto flutuante.

Turnover recebeu `segmented_analysis`: o usuário Plus pode comparar de 2 a 12 períodos ou segmentos, informando nome, admissões, desligamentos e quadro médio. Cada linha é executada pelo mesmo `CalculateTool` do cálculo Essencial, evitando fórmula paralela.

## Governança

As 11 entradas genéricas anteriores foram removidas de `plus_feature_governance.legacy_debt`. As novas features ficam sob o contrato estrito do Lote 1: implementação identificável, middleware central e teste explícito Free × Plus.

Nenhum novo serviço transversal foi criado. As planilhas reutilizam `SpreadsheetExporter` e `ToolResultExportFactory`; a análise de Turnover reutiliza a calculadora do próprio módulo. Não houve gatilho novo para promoção em `CORE_CANDIDATES.md`.

## Compatibilidade

- slugs e URLs públicas existentes foram preservados;
- cálculos Essenciais não foram alterados;
- PDF permanece Essencial nas dez ferramentas com planilha Plus;
- o modo `launch_free` continua obedecendo à política central e pode manter Plus aberto durante lançamento;
- em modo monetizado, Free recebe `feature.plus_required` e Plus recebe acesso normal.

## Continuidade

Antes do Lote 5, reconstruir o estado novamente a partir do ZIP original e reaplicar, em ordem, os Lotes 1, 2, 3 e 4. Depois reler os documentos obrigatórios da raiz e todos os relatórios de monetização. O próximo escopo é o conjunto de ajustes parciais, sem reabrir as decisões dos lotes críticos salvo regressão comprovada.

## Validação local

- Todos os arquivos PHP alterados neste lote passaram por `php -l`.
- O smoke test direto confirmou as 11 decisões de acesso em modo monetizado: Free bloqueado com `feature.plus_required` e Plus permitido com `feature.plus_plan`.
- A análise segmentada de Turnover foi executada diretamente com dois segmentos e retornou `7,50%` e `6,32%`, comprovando o caminho funcional após a correção da memória de cálculo.
- `php artisan route:list --json` confirmou os middlewares `tool.feature` nas rotas canônicas e legadas das 11 features.
- `tools:check-architecture` continua com 48 violações preexistentes fora das 11 ferramentas deste lote; nenhuma violação do Lote 4 permanece.
- PHPUnit não inicia neste ambiente porque faltam as extensões PHP `dom`, `mbstring` e `xmlwriter`.
- `php artisan view:cache` também não conclui porque `DOMDocument` não está disponível neste ambiente.
