# Lote Cirúrgico 4 — Substituição, limpeza final e regressão

## Objetivo

Concluir o saneamento sem reduzir o catálogo para 31 ferramentas, sem inventar uma ferramenta fiscal sem base técnica e sem manter um terceiro formulário com propósito idêntico aos módulos especializados.

## Decisão


## Compatibilidade preservada

- slug público;
- nomes de rota;
- histórico persistido;
- exports;
- API e endpoints existentes;
- schema técnico anterior legível.

## Resultado

- 32 módulos em `app/Tools`;
- 32 ferramentas no inventário oficial;
- 32 estados `implemented`;
- nenhuma ferramenta escondida;
- nenhuma ponte `deprecated` no catálogo;
- Home com exatamente 8 ferramentas recentes;
- sobreposição funcional encerrada com distinção documentada.

## Critérios de regressão

- IDs, slugs, módulos e `release_order` continuam únicos;
- a ferramenta reposicionada aceita cálculo e cenários;
- a página explica a diferença para os calculadores especializados;
- o catálogo não contém estado `compatibility_bridge`;
- nenhuma rota histórica foi removida.
