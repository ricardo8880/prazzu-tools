# Calculadora de Capital de Giro

## Descrição

Calcula NCG, CCL, capital necessário, necessidade adicional e folga de capital circulante.

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

Projeção de cenário sobre a posição calculada, com variações independentes para ativos, passivos operacionais e passivos financeiros. O cálculo Essencial permanece integralmente gratuito.

## Regras

Saldos circulantes pertencentes à mesma data-base. A NCG e o CCL são calculados em centavos; a necessidade adicional é uma fotografia estimativa que depende da classificação informada e não incorpora sazonalidade automaticamente.

## Integração entre ferramentas

Não publica nem aceita contratos. O módulo funciona isoladamente e não importa
classes internas de outras ferramentas.

## Dependências

Objetos financeiros, contratos de cálculo, histórico, exportação e componentes
visuais compartilhados do Core técnico.

## Histórico de versões

- `1.2.0`: passa a distinguir explicitamente déficit e folga de financiamento, com alerta contextual no resultado.
- `1.1.0`: memória financeira estruturada, data-base comum, política de centavos e premissas de classificação e sazonalidade explícitas.
- `1.0.0`: motor ou gerador funcional, validação, interface, memória,
  documentação e testes.

## Qualidade

O módulo é publicado como `beta`: permanece visível e executável no catálogo,
na busca e nas superfícies da plataforma, com cenários de regressão registrados.
Regras normativas continuam sujeitas à revisão profissional e atualização periódica.
