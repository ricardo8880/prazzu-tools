# Calculadora de Depreciação de Ativos

## Descrição

Calcula a depreciação de ativos a partir do bem, valor e vida útil, apresentando depreciação mensal/anual, evolução da depreciação acumulada e valor contábil.

## Experiência Essencial

- um ativo por cálculo;
- nome do bem, valor e vida útil em anos;
- método linear;
- depreciação mensal e anual;
- valor contábil após o primeiro ano;
- projeção anual completa e memória de cálculo.

O Essencial resolve integralmente o caso individual sem ocultar fórmula ou resultado.

## Prazzu Plus

- vários ativos no mesmo cálculo, sem criar cadastro patrimonial persistente;
- métodos linear, saldos decrescentes (duplo) e soma dos dígitos dos anos;
- projeção patrimonial consolidada;
- exportação em PDF e XLSX pelo Core compartilhado.

Durante a fase gratuita de lançamento, os recursos Plus permanecem disponíveis conforme a política global do produto.

## Regras de domínio

A versão 1.0.0 considera valor residual igual a zero. Se houver valor residual material, o usuário deve informar como valor do bem a base efetivamente depreciável.

### Linear

`depreciação anual = base depreciável ÷ vida útil em anos`

`depreciação mensal = depreciação anual ÷ 12`

### Saldos decrescentes (duplo)

`depreciação do ano = valor contábil inicial do ano × (2 ÷ vida útil)`

O valor é limitado ao saldo contábil remanescente; o último ano absorve eventual diferença de arredondamento.

### Soma dos dígitos dos anos

`depreciação do ano = base depreciável × anos restantes ÷ soma dos dígitos da vida útil`

Todos os valores monetários são tratados em centavos, sem `float`. Ajustes residuais de centavos são absorvidos pela projeção para que o valor contábil nunca fique negativo e termine em zero.

## Limites

A ferramenta não define automaticamente vida útil contábil/fiscal, valor residual, enquadramento tributário, taxas normativas ou elegibilidade do ativo. Esses parâmetros dependem da política e do contexto aplicáveis.

## Dependências

- `Money` e `IntegerRounding` do Core;
- exportadores PDF/XLSX compartilhados;
- componentes Bootstrap compartilhados da plataforma.

## Integração entre ferramentas

- Contratos publicados: nenhum.
- Contratos aceitos: nenhum.

## Integração com a plataforma

- Slug: `calculadora-depreciacao-ativos`
- Rota principal: `tools.calculadora-depreciacao-ativos.index`
- Namespace de views: `tools-calculadora-depreciacao-ativos`
- Histórico: desabilitado
- Exportações: PDF e XLSX

## Histórico de versões

- `1.0.0` — implementação inicial em 12/08/2026.
