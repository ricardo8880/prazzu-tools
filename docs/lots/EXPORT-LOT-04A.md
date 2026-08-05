# Lote 4A — Exportação das ferramentas 17 a 19

Estado-base: projeto consolidado após os Lotes 1, 2 e 3.

## Ferramentas migradas

17. Calculadora de Distribuição de Lucros
18. Gerador de Declaração de Rendimentos
19. Gerador de Declaração de Trabalho/Renda

## Resultado

- PDF real gerado pelo `PdfExporter`/Dompdf.
- Excel `.xlsx` real gerado pelo `SpreadsheetExporter`/PhpSpreadsheet.
- Tela, PDF e Excel usam a mesma requisição validada e a mesma action/calculadora.
- Os botões baseados em impressão do navegador foram removidos das duas declarações.
- O pacote é incremental e contém somente arquivos alterados.

## Continuidade

O Lote 4B deve migrar as ferramentas 20 a 24, preservando históricos e formatos estruturados já existentes.
