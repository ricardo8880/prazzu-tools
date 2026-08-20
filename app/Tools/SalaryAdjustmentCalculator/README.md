# Calculadora de Reajuste Salarial

## Descrição

Calcula novo salário, diferença mensal, reajuste efetivo, retroativo e impacto anual da remuneração.

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

Lotes, histórico por funcionário, relatórios e exportações. Esses recursos representam produtividade e continuidade; não alteram a
correção do resultado Essencial.

## Regras

Entradas principais: salário, percentual, aumento fixo e meses retroativos. O reajuste efetivo considera conjuntamente a parcela percentual e o aumento fixo. O resultado é orientativo e casos normativos,
contratuais ou cadastrais fora das premissas exibidas exigem revisão
profissional.

## Integração entre ferramentas

Não publica nem aceita contratos. O módulo funciona isoladamente e não importa
classes internas de outras ferramentas.

## Dependências

Objetos financeiros, contratos de cálculo, histórico, exportação e componentes
visuais compartilhados do Core técnico.

## Histórico de versões

- `1.1.0`: adiciona reajuste efetivo e fixa a memória do impacto anual em 12 salários + 13º + 1/3 de férias; encargos patronais continuam fora do escopo.
- `1.0.0`: motor ou gerador funcional, validação, interface, memória,
  documentação e testes.

## Qualidade

O módulo é publicado como `beta`: permanece visível e executável no catálogo,
na busca e nas superfícies da plataforma, com cenários de regressão registrados.
Regras normativas continuam sujeitas à revisão profissional e atualização periódica.


## Lote 5 — memória e transparência

O módulo foi revisado no Lote 5 para expor memória de cálculo estruturada, premissas e arredondamento sem depender do domínio de outras ferramentas. A responsabilidade permanece limitada ao escopo descrito neste documento.

## Prazzu Plus — saneamento de monetização

Exportação em planilha (`spreadsheet_export`) é Plus; cálculo individual e PDF permanecem Essenciais.
A autorização usa exclusivamente o gate central `tool.feature` no modo monetizado.

## Base normativa limitada do impacto anual

O fator anual exibido considera apenas a diferença sobre 12 salários, um 13º salário e o adicional constitucional de 1/3 de férias. A existência do 13º decorre da Lei 4.090/1962 e o adicional mínimo de férias está no art. 7º, XVII, da Constituição Federal. O cálculo não inclui FGTS, INSS patronal, reflexos específicos, pisos ou cláusulas coletivas.
