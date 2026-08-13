# Calculadora de Ponto de Equilíbrio

## Descrição

Calcula quantidade e faturamento mínimos para cobrir custos.

## Funcionalidades

- entrada validada por `FormRequest`;
- domínio independente e valores financeiros sem `float`;
- resultado completo com memória reproduzível;
- página responsiva com componentes compartilhados;
- impressão/PDF quando o resultado for documental.

## Experiência Essencial

O visitante resolve integralmente um caso individual sem autenticação. Fórmulas,
premissas, valores intermediários e limitações permanecem visíveis.

## Prazzu Plus

Comparação de cenário alternativo com variações de custos fixos, preço de venda e custo variável, mantendo o cálculo Essencial completo e gratuito.

## Regras

Custos fixos, preço e custo variável unitário do mesmo período. A quantidade é arredondada para cima até a primeira unidade inteira; tributos, comissões e perdas variáveis devem integrar o custo variável quando aplicáveis.

## Integração entre ferramentas

Não publica nem aceita contratos. O módulo funciona isoladamente e não importa
classes internas de outras ferramentas.

## Dependências

Objetos financeiros, contratos de cálculo, histórico, exportação e componentes
visuais compartilhados do Core técnico.

## Histórico de versões

- `1.1.0`: memória financeira estruturada, proteção de intervalo monetário, arredondamento para unidade inteira e premissas temporais explícitas.
- `1.0.0`: motor ou gerador funcional, validação, interface, memória,
  documentação e testes.

## Qualidade

O módulo é publicado como `beta`: permanece visível e executável no catálogo,
na busca e nas superfícies da plataforma, com cenários de regressão registrados.
Regras normativas continuam sujeitas à revisão profissional e atualização periódica.
