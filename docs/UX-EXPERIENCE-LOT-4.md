# Experiência do Usuário — Lote 4 — Analytics e retorno

## Objetivo

Concluir a rodada de UX com os itens 7 e 8:

1. transformar os eventos de jornada já existentes em um diagnóstico claro de abandono;
2. estabelecer problemas resolvidos e retorno para um novo resultado em outro dia como métricas centrais de produto.

## Estado de origem

A base foi reconstruída na ordem obrigatória:

`ZIP original → UX Lote 1 → UX Lote 2 → UX Lote 3`

Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `config/product_tools.php`, os três relatórios desta rodada e a documentação de Analytics.

A auditoria confirmou que as 50 ferramentas oficiais já implementam `HasAnalyticsJourney`. Portanto, este lote não criou listeners, eventos ou contratos paralelos e não alterou módulos individualmente.

## Mudanças

- `ToolAnalyticsQuery` passou a expor um funil de audiência única: `abriu → começou → viu resultado`.
- O painel diferencia `taxa de início` de `resultado após início`, permitindo saber se o problema está antes da interação ou durante o preenchimento/cálculo.
- Abandono continua baseado em `tool.abandoned` e permanece complementado por campos e etapas problemáticas já existentes.
- Alertas automáticos agora distinguem `muitas aberturas e poucos inícios` de `muitos inícios e poucos resultados`.
- O painel principal ganhou as métricas: `Problemas resolvidos`, `Pessoas que resolveram`, `Voltaram e resolveram` e `Taxa de retorno`.
- Retorno exige um novo resultado válido em outro dia dentro do período. Repetições no mesmo dia não contam.
- Para afirmar retorno são aceitas somente identidades persistentes de usuário ou visitante; sessão isolada não é tratada como retenção.
- O detalhe de cada ferramenta recebeu o mesmo funil e a mesma leitura de retorno.
- A view de detalhe deixou de usar propriedades antigas que já não pertenciam ao contrato atual de `ToolAnalyticsQuery`.

## Privacidade

Nenhuma métrica lê `input_payload`, `result_payload`, valores de formulário, documentos, CPF, CNPJ, nomes ou e-mails. O cálculo usa somente eventos, identidade técnica já existente e a data de ocorrência.

## Limites preservados

- Nenhuma fórmula, ferramenta, controller de cálculo, rota, slug, vertical, `release_order` ou inventário foi alterado.
- Nenhum evento novo foi criado; o lote reutiliza a taxonomia oficial do Analytics 2.0.
- Nenhuma coleta adicional foi adicionada ao navegador.
- Funis de aquisição, continuidade e próximos passos dos lotes anteriores foram preservados.
- Nenhum candidato de `CORE_CANDIDATES.md` foi ativado: a capacidade já pertence ao Core de Analytics.

## Continuidade obrigatória

Qualquer lote posterior deve reconstruir a base na ordem:

`ZIP original → UX Lote 1 → UX Lote 2 → UX Lote 3 → UX Lote 4`

Depois deve reler novamente README, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `config/product_tools.php` e os relatórios desta rodada antes de alterar o projeto.

## Validação executada

- `php artisan tools:check-architecture`: aprovado, sem violações;
- `php artisan analytics:check`: aprovado;
- `node --check resources/js/app.js`: aprovado;
- `php -l` na query e no teste novo: aprovado;
- compilação direta das duas views Blade alteradas: aprovada;
- comparação contra `ZIP original → Lote 1 → Lote 2 → Lote 3` confirmou que somente os arquivos deste lote foram alterados;
- PHPUnit não iniciou porque o PHP do ambiente não possui `dom`, `mbstring` e `xmlwriter`;
- execução de integração contra SQLite também não pôde ser feita porque o runtime não possui o driver PDO SQLite.

As limitações são ambientais e não foram contornadas com alterações em dependências ou configuração do projeto.
