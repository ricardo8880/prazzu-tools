# Calculadora de Fluxo de Caixa

## Descrição

Calcula entradas, saídas, geração operacional e saldo previsto.

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

Comparação automática entre cenário base, conservador e otimista, variando entradas e saídas sem alterar o cálculo Essencial de um período.

## Regras

Saldo inicial e movimentações de um único período, todos sob regime de caixa. O saldo final é uma projeção baseada apenas nos recebimentos e pagamentos informados.

## Integração entre ferramentas

Não publica nem aceita contratos. O módulo funciona isoladamente e não importa
classes internas de outras ferramentas.

## Dependências

Objetos financeiros, contratos de cálculo, histórico, exportação e componentes
visuais compartilhados do Core técnico.

## Histórico de versões

- `1.1.0`: memória financeira estruturada, período e regime de caixa explícitos, política de centavos e natureza estimativa do saldo final.
- `1.0.0`: motor ou gerador funcional, validação, interface, memória,
  documentação e testes.

## Qualidade

O módulo é publicado como `beta`: permanece visível e executável no catálogo,
na busca e nas superfícies da plataforma, com cenários de regressão registrados.
Regras normativas continuam sujeitas à revisão profissional e atualização periódica.
