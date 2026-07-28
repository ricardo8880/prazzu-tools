# Lote Cirúrgico 3 — Ponte de compatibilidade de Pró-Labore e Lucros

## Objetivo

Eliminar a duplicação da experiência pública sem apagar prematuramente URLs, histórico, métricas ou integrações da antiga ferramenta combinada.

## Alterações realizadas

- A página pública da ferramenta combinada deixou de apresentar um terceiro formulário duplicado.
- A URL antiga agora funciona como ponte para `ProLaboreSimulator` e `ProfitDistributionCalculator`.
- O manifesto foi marcado como `Deprecated` e passou a declarar apenas destinos e consulta de histórico antigo.
- O inventário mudou o estado de `migration_pending` para `compatibility_bridge`.
- As rotas históricas e endpoints antigos foram preservados temporariamente para compatibilidade.
- Nenhum slug foi removido e o catálogo continua com 32 entradas.

## Limite deste lote

O módulo físico ainda existe porque a sua remoção imediata deixaria 31 ferramentas e poderia quebrar consumidores antigos. O próximo lote deve substituir esta entrada por uma ferramenta realmente distinta e só então remover código, rotas e dados de compatibilidade após migração verificada.

## Critérios de aceite

- A URL pública antiga não executa nem exibe um cálculo duplicado.
- As duas ferramentas independentes permanecem acessíveis por slugs próprios.
- O histórico antigo continua consultável por utilizadores autenticados.
- O inventário mantém 32 módulos e identifica a ponte de compatibilidade de forma explícita.
