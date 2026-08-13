# Remediação Prazzu Plus — Lote 4 — Custo de Funcionário CLT

## Base e escopo

O lote foi iniciado sobre a reconstrução integral do ZIP original com todas as entregas anteriores reaplicadas em ordem. Os 11 contratos ainda legados da Calculadora de Custo de Funcionário CLT foram tratados como uma jornada única.

## Contratos certificados

- cálculo em lote e relatório consolidado;
- perfis reutilizáveis de empresa e funcionário;
- importação CSV e XLSX com prévia e processamento;
- exportação CSV e XLSX;
- cenários de custo;
- comparação numérica CLT × PJ × Autônomo;
- histórico autenticado;
- relatório profissional.

Cada contrato possui implementação concreta, gate individual, cobertura Free × Plus e método comportamental marcado com `CoversPlusFeature`. Perfis são persistidos e consultados de fato; histórico é gerado por um cálculo autenticado; importações processam datasets CSV e XLSX; cenários e modalidades executam as calculadoras do módulo.

## Governança acumulada

- catálogo Plus: 137;
- dívida legada: 44 → 33;
- contratos estritos: 93 → 104;
- contratos funcionais: 32 → 43;
- dívida funcional: 105 → 94;
- checksum do legado atualizado para o conjunto exato restante.

## Arquitetura e compatibilidade

O módulo reutiliza `ToolProfiles`, histórico, leitor tabular e exportadores do Core. Não foram alterados slug, fórmula Essencial, rotas, interface ou dependências. Não surgiu nova abstração compartilhada.

## Continuidade

Antes do Lote 5, reconstruir o estado desde o ZIP original, aplicar correções e Lotes 1–4 em ordem e reler os documentos obrigatórios. O lote seguinte deve considerar somente as 33 entradas restantes em `legacy_debt`.
