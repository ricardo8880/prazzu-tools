# Lote 6 multi-nicho — RH como prova arquitetural

## Objetivo

Provar que a arquitetura multi-vertical construída nos Lotes 1 a 5 aceita uma segunda vertical real sem duplicar infraestrutura compartilhada.

## Reconstrução e continuidade

O estado foi reconstruído do ZIP original e recebeu, em ordem, os deltas dos Lotes 3, 4 e 5 disponíveis na conversa. Antes da implementação foram relidos README, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `docs/ARCHITECTURE.md` e os relatórios multi-vertical existentes.

## Segunda vertical

`config/verticals.php` registra `rh` como dado de negócio com o nome `Recursos Humanos`. Nenhum enum, `if` por nicho ou namespace paralelo foi adicionado ao Core.

## Ferramenta de prova

Foi criada `TurnoverCalculator` com slug `calculadora-turnover`, vertical `rh` e categoria trabalhista. O módulo usa os mesmos contratos de `ToolModule`, `ToolManifest`, catálogo, rotas, Analytics e qualidade das demais ferramentas. A fórmula é deliberadamente operacional e sem dependência normativa: `((admissões + desligamentos) / 2) / quadro médio * 100`.

A ferramenta não importa domínio de outro módulo, não persiste dados pessoais e não cria serviços compartilhados específicos de RH.

## Home e SEO

RH possui conteúdo mínimo em `config/home.php` e `config/seo.php`. A mesma Home e o mesmo serviço `VerticalSeoContext` selecionam esses dados por `VerticalContext`. Não existem `HomeRH`, `SEORH` ou controllers duplicados.

## Blog

Uma migration idempotente cria a categoria `Gestão de Pessoas`, dois artigos iniciais de RH, incluindo um sobre turnover e a associação com `calculadora-turnover`. São utilizadas as tabelas e a engine de Blog já existentes, com `vertical_slug = rh`.

## Analytics e observabilidade

A nova ferramenta declara `HasAnalyticsJourney`. Como o Analytics já recebe `vertical_slug` do contexto transversal, eventos da experiência RH continuam no mesmo sistema global e podem ser filtrados pela dimensão `rh`.

## E2E

O manifesto declarativo já descobre ferramentas a partir de `config/product_tools.php`. O lote atualiza a cobertura mínima para 33 ferramentas; a nova ferramenta recebe cenários válido e inválido pelo mesmo mecanismo de geração automática, sem runner específico de RH.

## Inventário e compatibilidade

O inventário oficial passa de 32 para 33 ferramentas por este lote explícito de expansão. Os 32 módulos históricos e seus slugs permanecem intactos e associados a `contabilidade`; a nova entrada ID 33 pertence a `rh`.

## Validação

- `php artisan tools:check-architecture` — aprovado;
- `php artisan analytics:check` — aprovado;
- rotas de `calculadora-turnover` — registradas;
- lint PHP — executado nos arquivos alterados/adicionados;
- PHPUnit integral continua sujeito às extensões ausentes do ambiente (`dom`, `mbstring`, `xmlwriter`).

## Resultado arquitetural

A prova é bem-sucedida porque uma segunda vertical entrou por registro, configuração, conteúdo e ferramenta, reutilizando Core, Home, Blog, Analytics, SEO, Admin, observabilidade e E2E. Nenhuma infraestrutura paralela foi necessária.

## Continuidade

Antes do próximo lote ou expansão, reconstruir novamente o ZIP original e reaplicar todos os deltas multi-vertical entregues, relendo README e relatórios para preservar o ponto exato de continuidade.
