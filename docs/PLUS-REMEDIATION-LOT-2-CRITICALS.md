# Remediação Prazzu Plus — Lote 2 — Ferramentas críticas

## Base e escopo

O lote partiu do ZIP original e reaplicou os ajustes anteriores na ordem de continuidade. Foram confrontados manifesto, implementação, rota protegida e teste comportamental de 19 benefícios nas cinco ferramentas cuja dívida era integral.

## Contratos certificados

- Simples Nacional: comparação de cenários, comparação de anexos, projeção anual, alertas e histórico mensal.
- Honorários Contábeis: proposta comercial, contrato de serviços, histórico e exportação do histórico.
- Margem e Markup: exportação, lote/importação, cenários e histórico.
- Rescisão: histórico, repetição de cálculo salvo e relatório histórico.
- Validador de CNPJ: processamento em lote, exportação do lote e histórico.

Nenhuma promessa vazia foi apenas reclassificada. Cada item já possui caminho funcional concreto, middleware `tool.feature` e teste que executa o comportamento. Os testes receberam `CoversPlusFeature`; o atributo tornou-se repetível para permitir que um fluxo integrado comprove mais de um contrato sem duplicar preparação cara.

## Governança

- catálogo declarado preservado: 137;
- dívida legada: 76 → 57;
- contratos estritos: 61 → 80;
- contratos funcionalmente certificados: 0 → 19;
- dívida funcional: 137 → 118;
- checksum da dívida legada atualizado para o conjunto exato restante.

## Arquitetura

Os recursos usam o histórico, exportadores, armazenamento temporário e autorização compartilhados. Regras de cenários, documentos e cálculos permanecem nos módulos. A auditoria não encontrou gatilho para uma nova abstração transversal.

## Continuidade

Antes do Lote 3, reconstruir novamente o estado na ordem: ZIP original, ajustes direcionados anteriores, Remediação Lote 1 e Remediação Lote 2. Reler os documentos obrigatórios e selecionar apenas contratos ainda presentes em `legacy_debt`; uma entrada só pode sair quando implementação, gate e teste comportamental forem comprovados.
