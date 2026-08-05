# Exportação universal — Lote 2

## Ferramentas migradas

1. EmployeeCostCalculator
2. FactorRSimulator
3. LateDasCalculator
4. LaborChargesCalculator
5. EmploymentModelComparator
6. EmployerInssCalculator
7. WorkingCapitalCalculator
8. CashFlowCalculator

## Resultado

- Cada ferramenta passou a oferecer download real em PDF e Excel.
- PDF é gerado por `PdfExporter`/Dompdf, sem `window.print()`.
- Excel é gerado por `SpreadsheetExporter`/PhpSpreadsheet, sem OOXML artesanal.
- Tela, PDF e Excel executam a mesma action de cálculo e recebem a mesma entrada validada.
- O PDF compartilhado contém somente título e conteúdo do resultado; não renderiza layout da plataforma.
- O legado de impressão/exportação da Calculadora de Custo de Funcionário foi preservado temporariamente para compatibilidade e será removido no lote final.

## Continuidade

O próximo lote deve reconstruir o estado com o ZIP original, Lote 1 e Lote 2, nessa ordem, e migrar as ferramentas oficiais 9 a 16.
