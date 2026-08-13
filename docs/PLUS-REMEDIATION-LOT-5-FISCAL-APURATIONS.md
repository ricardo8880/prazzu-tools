# Remediação Prazzu Plus — Lote 5 — Apurações fiscais

## Reconstrução e escopo

O lote foi produzido após reconstruir o ZIP original e reaplicar, em ordem, os ajustes direcionados e os quatro lotes anteriores. O escopo ficou restrito a três módulos fiscais com benefícios reais já implementados, mas ainda sem certificação funcional completa.

## Contratos certificados

- `calculadora-irpj-csll-lucro-presumido`: `periodicity`, `multiple_activities`, `scenario_comparison`, `carry_forward_limit`, `credits` e `export`.
- `calculadora-pis-cofins`: `aggregate_credits`, `multiple_operations`, `credit_breakdown`, `comparison` e `export`.
- `calculadora-icms-st`: `adjusted_mva`, `fcp`, `interstate`, `multiple_items` e `export`.

Cada contrato possui execução observável no fluxo da ferramenta, autorização individual pelo Core, cobertura Free × Plus em modo monetizado e teste comportamental marcado com `CoversPlusFeature`.

## Garantias preservadas

- Nenhum slug, fórmula Essencial, rota pública ou ferramenta foi removido.
- Nenhum recurso meramente descritivo foi promovido: os testes exercitam entradas avançadas e verificam resultados, comparações, itens ou ações de exportação renderizadas.
- Exportações continuam reutilizando `ToolResultExportFactory`, `PdfExporter` e `SpreadsheetExporter`.
- Histórico, dinheiro, datas e memória de cálculo continuam no Core existente; nenhuma persistência ou infraestrutura fiscal paralela foi criada.

## Estado acumulado

- catálogo Plus declarado: 137 contratos;
- contratos funcionalmente certificados: 43 → 59;
- dívida funcional: 94 → 78;
- contratos estritos: 104 → 120;
- dívida legada: 33 → 17;
- checksum da dívida legada: `d9c7d8d732933d7000fc33e9db850a802deac7dd141203a94e021bd95ebea2e8`.

## Continuidade

Antes do Lote 6, reconstruir novamente o ZIP original, reaplicar todos os ajustes e Lotes 1–5 em ordem e reler os documentos obrigatórios. O próximo lote deve partir somente dos 17 contratos restantes em `legacy_debt`.
