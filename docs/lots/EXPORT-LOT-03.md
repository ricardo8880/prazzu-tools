# Lote 3 — Exportação das ferramentas 9 a 16

Estado-base reconstruído obrigatoriamente a partir do ZIP original, seguido pelos Lotes 1 e 2.

## Ferramentas migradas

9. Ponto de Equilíbrio
10. Precificação / Margem e Markup
11. Pró-Labore Ideal
12. Comissão de Vendedores
13. Holerite
14. Admissão
15. Demissão / Rescisão
16. Reajuste Salarial

Todas passam a oferecer PDF real e Excel XLSX real, gerados no backend pelos contratos do Core. Tela e exportações reutilizam a mesma entrada validada e a mesma regra de cálculo. O botão de impressão foi removido dos fluxos atuais de Holerite e Admissão.

`StructuredResultExportFactory` foi adicionado ao Core porque Margem/Markup e Rescisão possuem resultados estruturados próprios, distintos de `ToolCalculationResult`.

As exportações históricas legadas por impressão permanecem temporariamente para compatibilidade e serão removidas no lote final de saneamento.
