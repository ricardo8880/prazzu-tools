# Simulador CLT × PJ × Autônomo

## Descrição

Compara líquido e custo empresarial nos três modelos.

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

Múltiplos cenários, gráficos, histórico e exportações. Esses recursos representam produtividade e continuidade; não alteram a
correção do resultado Essencial.

## Regras

Entradas principais: Valores, descontos, tributos e encargos explicitamente informados. O resultado é orientativo e casos normativos,
contratuais ou cadastrais fora das premissas exibidas exigem revisão
profissional.

## Integração entre ferramentas

Não publica nem aceita contratos. O módulo funciona isoladamente e não importa
classes internas de outras ferramentas.

## Dependências

Objetos financeiros, contratos de cálculo, histórico, exportação e componentes
visuais compartilhados do Core técnico.

## Histórico de versões

- `1.0.0`: motor ou gerador funcional, validação, interface, memória,
  documentação e testes.

## Qualidade

O módulo é publicado como `beta`: permanece visível e executável no catálogo,
na busca e nas superfícies da plataforma, com cenários de regressão registrados.
Regras normativas continuam sujeitas à revisão profissional e atualização periódica.


## Alinhamento do Lote 4

O resultado agora explicita premissas e limitações. As alíquotas continuam entradas do usuário porque CLT, PJ e autônomo dependem de atividade, regime, retenções, benefícios e contratação concreta. A ferramenta não importa regras internas das calculadoras trabalhistas ou societárias.

## Prazzu Plus — saneamento de monetização

Exportação em planilha (`spreadsheet_export`) é Plus; comparação mensal e PDF permanecem Essenciais.
A autorização usa exclusivamente o gate central `tool.feature` no modo monetizado.
