# Calculadora de Hora Extra, Adicional Noturno e DSR

## Descrição

Módulo independente para cálculo de verbas variáveis de jornada de empregado urbano CLT comum.

## Funcionalidades

- valor da hora normal;
- horas extras de 50%, 100% e percentual informado;
- adicional noturno urbano com hora reduzida;
- horas extras noturnas;
- DSR parametrizado por calendário informado;
- projeções de reflexos;
- memória, histórico autenticado e exportações compartilhadas.

## Experiência Essencial

Resolve integralmente valor da hora normal e das horas extras sem autenticação. Recursos Plus acrescentam profundidade, cenários e conveniência, nunca correção da fórmula básica.

## Prazzu Plus

Adiciona adicional noturno, DSR, reflexos projetados, histórico e exportações, mantendo o cálculo essencial totalmente resolvido.

## Regras

Percentuais, divisor e calendário devem ser compatíveis com o caso concreto. A ferramenta usa mínimos gerais da CLT e referências normativas registradas em `docs/NORMATIVE_RULES.md`, mas não decide convenção coletiva, banco de horas, 12x36 ou enquadramentos especiais.

## Dependências

Somente capacidades transversais do Core: Money, Percentage, arredondamento inteiro, memória de cálculo, regras normativas, histórico e exportação. Não importa classes internas de outras ferramentas.

## Histórico de versões

- 1.0.0 — Expansão Lote 12: implementação inicial completa.
