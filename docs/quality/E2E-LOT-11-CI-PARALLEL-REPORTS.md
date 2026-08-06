# Lote 11 — CI, paralelismo e relatórios executivos

## Continuidade analisada

Este lote foi implementado após reaplicar os arquivos dos Lotes 9 e 10 sobre o projeto-base. Permanecem obrigatórios os contratos acumulados de ambiente isolado, catálogo, cenários, observabilidade, downloads, acesso e cobertura das 32 ferramentas.

## Entregas

- Pipeline rápido de qualidade e smoke para commits.
- Pipeline E2E em quatro shards para pull requests, tags `v*` e execução manual de pré-release.
- Cache separado para Composer e browsers Playwright, sem armazenar `.env`, banco ou sessões.
- Publicação dos artefatos de cada shard, inclusive em falhas.
- Consolidação dos JSONs do Playwright em um resumo executivo.
- Comparação por fingerprint entre falhas atuais e baseline, classificando falhas novas, conhecidas e resolvidas.
- Bloqueio do pipeline quando uma regressão nova é encontrada.

## Continuidade para o Lote 12

O próximo lote deve partir do projeto-base com os Lotes 9, 10 e 11 aplicados. Deve preservar os perfis de CI determinísticos e manter exploração/fuzzing fora do gate bloqueante. Responsividade, Firefox e WebKit entram apenas na bateria completa, junto com métricas de cobertura, flakiness e duração.
