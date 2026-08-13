# Remediação Prazzu Plus — Lote 3 — Produtividade documental

## Base e seleção

O estado foi reconstruído do ZIP original com todas as correções e lotes anteriores reaplicados em ordem. O escopo selecionou 13 contratos legados ligados a documentos, exportação e continuidade.

## Contratos certificados

- Comparador Tributário: exportação estruturada, histórico e relatório profissional.
- Conversor Fiscal XML: processamento em lote, histórico e exportação profissional.
- Emissor de Recibos: geração em lote, histórico e perfis reutilizáveis.
- DARF/GPS: histórico e exportação profissional.
- Distribuição de Lucros com Balanço: relatório PDF/XLSX.
- MEI → Microempresa: relatório PDF/XLSX.

Cada contrato possui implementação concreta, gate `tool.feature`, matriz Free × Plus e teste comportamental marcado com `CoversPlusFeature`. Testes antigos que aceitavam múltiplos status sem provar o fluxo foram fortalecidos para gravar e recuperar dados reais.

## Governança acumulada

- catálogo Plus: 137;
- dívida legada: 57 → 44;
- contratos estritos: 80 → 93;
- contratos funcionais: 19 → 32;
- dívida funcional: 118 → 105;
- checksum da dívida legada atualizado para o conjunto exato restante.

## Arquitetura

Histórico, payload temporário e exportações continuam no Core técnico. As regras de montagem de recibos, documentos fiscais, guias e relatórios permanecem nos módulos. Não houve criação de dependência, CSS, JavaScript ou abstração nova.

## Continuidade

Antes do Lote 4, reconstruir o estado na ordem obrigatória e aplicar este lote por último. Somente contratos ainda presentes em `legacy_debt` podem compor o lote seguinte, e nenhum deve ser certificado sem execução comportamental comprovada.
