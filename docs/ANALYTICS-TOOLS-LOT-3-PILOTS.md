# Analytics das Ferramentas — Lote 3 — Pilotos

## Estado reconstruído

Este lote foi refeito a partir do ZIP original com aplicação sequencial dos Lotes 1 e 2. Nenhum ficheiro do primeiro processamento incompleto do Lote 3 foi usado como estado de continuidade.

## Pilotos ativados

- `capital-de-giro`: formulário financeiro simples e resultado na mesma jornada.
- `comparador-tributario`: formulário extenso, etapas semânticas e exportação.
- `declaracao-rendimentos`: dados pessoais sensíveis, resultado documental e impressão/PDF.
- `gerador-de-contratos`: questionário guiado, editor posterior, resultado em duas fases e PDF/DOCX.
- `conversor-fiscal-xml`: upload individual, upload múltiplo em lote e exportações CSV/XLSX/JSON.

Cada módulo implementa apenas `HasAnalyticsJourney` e declara chaves semânticas, etapas, seletores e ações. O mecanismo de captura permanece integralmente no Core.

## Privacidade e comportamento

Nenhum valor, documento, conteúdo XML, nome de ficheiro, texto contratual, CPF/CNPJ ou resultado é enviado. Campos sensíveis são representados apenas pela chave declarada e pelo estado agregado de conclusão/validação.

O piloto revelou uma diferença concreta entre envio de cálculo e botão de exportação com `formaction`. O capturador foi corrigido para não publicar `tool.calculation.started` quando o `submitter` representa exportação ou partilha.

## Gate para expansão

O Lote 4 só deve começar depois de confirmar em ambiente navegável:

1. uma emissão de `tool.started` por jornada;
2. ausência de duplicidade de campo, etapa, erro e exportação;
3. correlação correta entre envio e resultado;
4. abandono apenas antes de envio/resultado;
5. ausência de valores ou dados pessoais nos payloads;
6. upload individual e múltiplo contabilizados sem serializar ficheiros.

## Continuidade

O próximo lote deve reconstruir o projeto com: ZIP original + Lote 1 + Lote 2 + este Lote 3. Depois deve reler README, `CORE_CANDIDATES.md`, inventário, relatórios e documentação do capturador antes de expandir a instrumentação.
