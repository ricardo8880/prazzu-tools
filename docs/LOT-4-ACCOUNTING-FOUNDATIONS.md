# Lote 4 — Fundamentos contábeis compartilhados

## Objetivo

Fornecer value objects e contratos estáveis para conceitos repetidos em muitas
ferramentas, sem implementar regras específicas, persistência, histórico ou
interface.

## Dinheiro

`App\Core\Money\Money` armazena valores em unidades mínimas inteiras. Para BRL,
`100` representa R$ 1,00. `float` não deve ser utilizado em cálculos monetários.

```php
$base = Money::fromDecimal('1.234,56');
$taxa = Percentage::fromString('10');
$valor = $base->percentage($taxa, RoundingMode::HalfUp);
```

Regras:

- a moeda faz parte do valor;
- moedas diferentes não podem ser somadas;
- toda divisão que pode produzir fração informa o arredondamento;
- valores de formulários entram como `string`, nunca como `float`;
- persistência futura deve gravar o valor em centavos e o código da moeda.

## Percentuais

`Percentage` preserva até seis casas decimais do percentual. Isso evita
representações binárias imprecisas e mantém uma base única para taxas, alíquotas
e índices.

O objeto não pressupõe que um percentual possa ou não ser negativo ou superior a
100%. Essa restrição pertence à regra de cada ferramenta.

## Arredondamento

Modos disponíveis:

- `Down`;
- `Up`;
- `HalfUp`;
- `HalfDown`;
- `HalfEven`.

A ferramenta deve escolher o modo exigido pela regra aplicável. Não existe um
arredondamento contábil universal.

## Datas

Conceitos disponíveis:

- `ReferenceDate`: dia civil explícito usado pelo cálculo;
- `Competence`: ano e mês de competência;
- `DatePeriod`: período fechado com início e fim;
- `EffectivePeriod`: vigência com fim opcional;
- `EffectiveRuleResolver`: seleciona exatamente uma regra para uma data.

Regras de domínio não devem chamar `now()` internamente. A data de referência deve
ser recebida na entrada do caso de uso para permitir reprodução, simulação e
testes históricos.

## Vigência

Regras versionadas que implementam `EffectiveDated` informam seu período. O
resolvedor:

- falha quando nenhuma regra atende à data;
- falha quando mais de uma regra atende à mesma data;
- permite validar sobreposição antecipadamente.

A versão formal da ferramenta, os snapshots e a auditoria serão adicionados no
Lote 5.

## CPF e CNPJ

`Cpf` e `Cnpj`:

- removem pontuação na entrada;
- validam dígitos verificadores;
- rejeitam sequências repetidas;
- fornecem forma somente com dígitos;
- formatam para exibição;
- fornecem máscara para logs e telas não autorizadas.

A existência matemática de um CNPJ válido não confirma situação cadastral. Uma
consulta oficial ou integração externa será uma responsabilidade separada.

## Referências normativas

`NormativeReference` registra de forma tipada:

- tipo da fonte;
- identificador;
- título;
- publicação;
- vigência opcional;
- URL oficial opcional;
- artigo ou trecho de referência opcional.

O objeto não baixa legislação nem garante atualização automática. Cada ferramenta
continua responsável por seus casos de referência e pela revisão das fontes.

## Limites

Este lote deliberadamente não inclui:

- fórmulas tributárias ou trabalhistas;
- calendário de dias úteis e feriados;
- atualização automática de índices;
- banco de dados;
- snapshots de entrada e resultado;
- auditoria;
- autorização;
- integrações externas.

Esses itens serão adicionados somente nos lotes correspondentes.
