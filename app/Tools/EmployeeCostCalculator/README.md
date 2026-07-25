# Calculadora de Custo de Funcionário CLT

## Descrição

Calcula remuneração, benefícios, encargos, provisões e custo mensal e anual.

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

Cadastro em lote, comparações de contratação, histórico e relatórios. Esses recursos representam produtividade e continuidade; não alteram a
correção do resultado Essencial.

Na importação unificada, CSV e XLSX mantêm o mesmo fluxo de prévia e
processamento, mas cada formato é autorizado de forma independente pelos
recursos `csv_import` e `xlsx_import`.

## Regras

Entradas principais: Salário, média variável, benefícios, regime, RAT ajustado e terceiros. O resultado é orientativo e casos normativos,
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
