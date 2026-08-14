# Experiência do Usuário — Lote 2 — Continuidade e próximos passos

## Objetivo

Evoluir duas superfícies já existentes sem recriar Growth/Retention:

1. tornar `Continue de onde parou` orientado ao último trabalho salvo, e não apenas ao nome/descrição da ferramenta;
2. substituir a apresentação genérica de `Ferramentas relacionadas` por `Próximos passos` derivados exclusivamente da jornada editorial já existente.

## Estado de origem

A base foi reconstruída na ordem obrigatória:

`ZIP original → UX Lote 1`

Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `config/product_tools.php`, `docs/UX-EXPERIENCE-LOT-1.md` e os relatórios de Growth/Retention relevantes.

## Continuidade

A consulta compartilhada `UserToolContinuityQuery` continua sem expor `input_payload` ou `result_payload` fora da ferramenta responsável. O resumo agora também informa qual ação GET segura representa melhor a retomada:

- detalhe do último resultado, quando existe rota `history.show`;
- histórico salvo, quando existe apenas `history.index`;
- ferramenta, quando não existe superfície de histórico adequada.

Na Home autenticada, os cards deixaram de repetir a descrição comercial da ferramenta e passaram a mostrar:

- identificação de `Último trabalho`;
- data/hora do resultado salvo;
- data de referência quando disponível;
- ação concreta para retomar aquele trabalho.

O `Meu Prazzu` reutiliza o mesmo contrato e prioriza a abertura do trabalho salvo; `Refazer cálculo` continua disponível apenas quando a rota real de repetição existe.

Nenhum valor financeiro, documento, entrada digitada ou resultado sensível passou a ser renderizado na Home ou no hub.

## Próximos passos

`ToolCatalog` ganhou `nextSteps()`, que usa somente `config/tools/journeys.php`. Diferentemente de `related()`, essa superfície não completa vagas com heurística de categoria/palavras-chave, porque `Próximo passo` deve representar uma decisão editorial intencional.

Nas páginas padronizadas de ferramenta:

- `Ferramentas relacionadas` virou `Próximos passos`;
- o primeiro item editorial recebe destaque como `Próximo passo recomendado`;
- os demais aparecem como alternativas complementares;
- a origem de Analytics continua usando `source=related_tools`, preservando o evento e os funis já consolidados em Growth/Retention;
- a vertical de RH continua sem recomendações artificiais enquanto possuir apenas uma ferramenta oficial.

`related()` foi preservado para superfícies que realmente precisam de relacionamento genérico, como Blog e integrações existentes.

## Limites preservados

- README da raiz não foi alterado.
- Nenhuma fórmula, controller de cálculo, request, migration, slug, vertical ou `release_order` foi alterado.
- Nenhuma persistência anônima nova foi criada.
- Nenhum payload de cálculo foi exposto na Home ou no Meu Prazzu.
- Eventos e parâmetros existentes de Analytics foram preservados.
- Nenhum novo candidato ao Core foi necessário: o lote reutiliza `ToolCatalog` e `UserToolContinuityQuery`, já compartilhados.

## Continuidade obrigatória

O próximo lote desta rodada deve reconstruir a base na ordem:

`ZIP original → UX Lote 1 → UX Lote 2`

Depois deve reler novamente os documentos obrigatórios da raiz, os relatórios desta rodada, os lotes anteriores relevantes e `config/product_tools.php`.

O próximo escopo planejado é a Home orientada ao problema, sem reconstruir busca, favoritos, recentes ou personalização que já existem.
