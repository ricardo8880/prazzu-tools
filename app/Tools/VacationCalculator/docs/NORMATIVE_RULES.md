# Regras normativas — Calculadora de Férias

## Versão 1.0.0

### Fontes primárias

- Constituição Federal, art. 7º, XVII: férias anuais remuneradas com pelo menos um terço adicional.
- CLT, arts. 129 e 130: direito anual e faixas de dias conforme faltas injustificadas.
- CLT, art. 134: concessão nos 12 meses subsequentes à aquisição do direito.
- CLT, art. 142: remuneração vigente na concessão e integração de médias/adicionais.
- CLT, art. 143: conversão de um terço do período em abono pecuniário.
- CLT, art. 145: pagamento até dois dias antes do início das férias.

Texto consolidado oficial: `https://www.planalto.gov.br/ccivil_03/decreto-lei/del5452compilado.htm`.
Constituição compilada: `https://www.planalto.gov.br/ccivil_03/constituicao/constituicaocompilado.htm`.

### Política monetária

Todos os valores usam `App\Core\Money\Money`, em centavos, sem `float`. Divisões usam o arredondamento padrão `HalfUp` do Core.

### Limites do Lote 1

- O cálculo cobre férias individuais de período completo.
- Férias coletivas, fracionamento em vários períodos e perda/interrupção do período aquisitivo exigem evolução posterior.
- INSS e IRRF não são apurados automaticamente neste lote.
- Consequências financeiras de concessão fora do prazo são sinalizadas para revisão, mas não calculadas automaticamente.
