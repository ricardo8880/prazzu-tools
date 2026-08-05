# Exportação universal — Lote Final de Encerramento

## Base aplicada

Projeto original com os lotes incrementais 4A, 4B, 5A e 5B reaplicados em ordem, preservando os lotes 1–3 já presentes no ZIP original.

## Limpeza concluída

- removidos `BrowserPrintExporter`, `PrintableDocument`, a view `exports/browser-print.blade.php` e `resources/js/print-page.js`;
- removida a ação global `window.print()` de `resources/js/app.js`;
- históricos de Rescisão, Margem/Markup, Custo de Funcionário e o exportador genérico de histórico passaram a usar `PdfExporter`/Dompdf;
- exportações XLS/XLSX artesanais foram removidas de `TabularExportService`, que permanece responsável apenas por CSV;
- Custo de Funcionário, Validador de Documentos e Analytics passaram a produzir `.xlsx` real com PhpSpreadsheet;
- documentação dos módulos afetados foi atualizada.

## Compatibilidade

As rotas públicas existentes foram preservadas. Rotas anteriormente chamadas de “imprimir” continuam aceitas, mas agora retornam download de PDF real no backend.

## Arquivos removidos ao aplicar este ZIP

Consultar `docs/lots/EXPORT-LOT-FINAL-DELETIONS.txt`. Esses caminhos devem ser excluídos do projeto consolidado.

## Validações

- sintaxe PHP dos arquivos alterados: sem erros;
- busca em código de produção por `BrowserPrintExporter`, `PrintableDocument`, `window.print()` e `print-page.js`: sem ocorrências;
- busca em código de produção por chamadas `TabularExportService::excel/xlsx`, `->excel()` e `->xlsx()`: sem ocorrências;
- as extensões PHP `dom`, `mbstring` e `xmlwriter` não estão disponíveis no ambiente, portanto a suíte funcional completa não pôde ser executada.
