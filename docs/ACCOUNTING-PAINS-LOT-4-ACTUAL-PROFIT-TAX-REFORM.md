# Cobertura das dores contábeis — Lote 4 — Lucro Real + Reforma Tributária

Reconstruído na ordem obrigatória: ZIP original → Lote 1 → Lote 2 → Lote 3, com releitura do README da raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, relatórios anteriores e inventários executáveis.

## Lucro Real


## Reforma Tributária
Publicado `TaxReformSimulator`, slug `sales-tax-calculator`, ID/release 49. A regra 2026–2033 é versionada: 2026 usa as alíquotas-teste de CBS 0,9% e IBS 0,1%; 2027–2028 extingue PIS/Cofins e usa CBS de referência reduzida em 0,1 p.p. + IBS 0,1%; 2029–2032 aplica os percentuais de transição 90/80/70/60% para ICMS/ISS e 10/20/30/40% para IBS; 2033 extingue ICMS/ISS. Alíquotas futuras de referência são parâmetros informados e não são inventadas.

Fontes verificadas: páginas oficiais de IRPJ e CSLL da Receita Federal; Receita Federal, “Entenda a Reforma Tributária do Consumo”, atualizada em 03/07/2026; LC 214/2025.

## Governança
Catálogo 47 → **49**. Contratos Plus 141 → **143**, com `tax_base_diagnostics` e `transition_diagnostics`, ambos com gate central e teste marcado. Nenhuma dependência externa adicionada.

## Continuidade
O Lote 5 deve reconstruir: **ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4**, reler todos os documentos e então tratar ECAD + saneamento fiscal dirigido.
