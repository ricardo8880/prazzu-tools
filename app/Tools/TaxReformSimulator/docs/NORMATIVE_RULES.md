# Regras normativas — Reforma Tributária do Consumo

## Escopo

O simulador é **paramétrico e estimativo**. Ele modela a transição geral de 2026 a 2033 e não determina enquadramento, redução, regime específico, Imposto Seletivo, cashback, benefícios ou alíquotas futuras de referência do caso concreto.

## 2026 — ano-teste

- CBS: 0,9%;
- IBS: 0,1%;
- o montante eventualmente recolhido de CBS/IBS é compensado com PIS/Cofins e, na insuficiência prevista constitucionalmente, pode seguir a sistemática legal de compensação/ressarcimento;
- a legislação prevê hipótese de dispensa do recolhimento das alíquotas-teste para quem cumprir as obrigações acessórias aplicáveis.

Por isso, **CBS/IBS de 2026 não podem ser simplesmente somados à carga federal legada como custo adicional automático**. Desde a versão de cálculo `1.1.0`, o simulador representa a compensação até o limite da carga federal legada informada. A parcela de CBS/IBS continua visível para transparência da transição.

## 2027–2028

- PIS/Cofins deixam a transição geral;
- CBS usa a alíquota de referência informada reduzida em 0,1 ponto percentual;
- IBS permanece em 0,1%.

## 2029–2032

A transição de ICMS/ISS para IBS é representada pelos percentuais gerais:

| Ano | ICMS/ISS remanescente | IBS da alíquota de referência |
|---:|---:|---:|
| 2029 | 90% | 10% |
| 2030 | 80% | 20% |
| 2031 | 70% | 30% |
| 2032 | 60% | 40% |

## 2033

A simulação geral considera encerrada a parcela de ICMS/ISS e utiliza 100% da alíquota de referência do IBS informada pelo usuário.

## Fontes oficiais e rastreabilidade

- Emenda Constitucional nº 132/2023 — transição e compensação do ano-teste;
- Lei Complementar nº 214/2025 — IBS, CBS e regras de transição;
- Receita Federal — página “Entenda a Reforma Tributária do Consumo”, verificada em 19/08/2026.

A regra estruturada `tax_reform.consumption_transition` está na versão normativa `2026.08.3`, com vigência modelada de 01/01/2026 a 31/12/2033. Cada simulação gera um `NormativeRuleSnapshot` cuja data de referência é 1º de janeiro do ano escolhido. A LC 227/2026 e regulamentações posteriores continuam sendo monitoradas quando alterarem o comportamento efetivamente modelado; elas não são adicionadas ao snapshot apenas por existirem.

A revisão deste arquivo não transforma a ferramenta em apuração fiscal oficial. Mudanças normativas posteriores exigem nova revisão versionada e casos de regressão.
