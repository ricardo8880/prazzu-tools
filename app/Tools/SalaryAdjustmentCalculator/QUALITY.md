# Qualidade — Calculadora de Reajuste Salarial

## Evidência revisada no Beta Activation — Lote 2

- [x] Novo salário, diferença mensal, reajuste efetivo e retroativo são calculados sem `float`.
- [x] O reajuste efetivo inclui percentual e aumento fixo, evitando interpretação errada quando existe parcela fixa.
- [x] O impacto anual é explicitamente limitado a 12 salários + 13º + adicional de 1/3 de férias.
- [x] Encargos patronais, FGTS, pisos, tetos, compensações e cláusulas coletivas ficam explicitamente fora do cálculo.
- [x] Salário deve ser positivo; percentual, aumento fixo e meses retroativos possuem limites de entrada.
- [x] Golden cases concretos cobrem cenário típico, ajuste zero, entrada inválida, arredondamento, não aplicação, transição normativa e regressão.
- [x] Testes unitários protegem o cálculo principal e o percentual efetivo com aumento fixo.
- [x] Lei 4.090/1962 e Constituição Federal art. 7º, XVII, estão registradas no README como base limitada das parcelas anuais consideradas.
- [x] O Essencial entrega cálculo e memória completos; planilha/histórico/lotes permanecem produtividade Plus.

A ferramenta permanece `beta` até a auditoria global dos lotes 10/11, que deve revalidar a base normativa antes da promoção.
