# Exportação oficial por bibliotecas — Lote 5A

## Escopo

Migração incremental das ferramentas oficiais 25–28:

25. Calculadora de Honorários Contábeis;
26. Validador Inteligente de CNPJ, CPF e IE;
27. Gerador de Contratos;
28. Gerador Inteligente de DARF/GPS.

## Implementação

- PDF passa pelos contratos `PdfExporter` e pela implementação real baseada em Dompdf.
- Excel `.xlsx` passa por `SpreadsheetExporter` e PhpSpreadsheet.
- Dados de entrada e resultado são entregues às factories compartilhadas do Core.
- CSV, JSON e DOCX preexistentes foram preservados onde representam formatos adicionais úteis.
- O fluxo de impressão do navegador foi removido dos controllers alterados.

## Continuidade

O Lote 5B deve migrar as ferramentas 29–32: Conversor Fiscal de XML, Emissor de Recibos, Calculadora de Simples Nacional e Calculadora de Férias. O lote final permanece responsável pela busca global e remoção de qualquer legado restante.
