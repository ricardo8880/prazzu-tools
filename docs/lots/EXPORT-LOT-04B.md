# Lote 4B — Exportação das ferramentas 20 a 24

Estado-base reconstruído a partir do projeto original e do Lote 4A, preservando os Lotes 1, 2 e 3 já consolidados.

## Ferramentas migradas

20. Simulador Tributário (Simples × Lucro Presumido × Lucro Real)
21. Calculadora de Salário Líquido
22. Calculadora de Hora Extra, Adicional Noturno e DSR
23. Calculadora DIFAL / ICMS Interestadual + FCP
24. Planejador de Retirada de Sócios

## Resultado

- Todas oferecem PDF real via `PdfExporter`/Dompdf.
- Todas oferecem Excel `.xlsx` real via `SpreadsheetExporter`/PhpSpreadsheet.
- Tela, PDF e Excel executam a mesma calculadora/action com a mesma entrada validada.
- Os fluxos de impressão do navegador foram removidos das ferramentas 20 a 24.
- CSV e JSON preexistentes foram preservados nas ferramentas 20 e 24 por compatibilidade.
- Históricos e rotas públicas existentes foram preservados.

## Continuidade

O próximo lote deve migrar as ferramentas 25 a 32. O saneamento global de `BrowserPrintExporter`, `window.print()` e exportações manuais deve ocorrer somente após confirmar a migração integral das 32 ferramentas.
