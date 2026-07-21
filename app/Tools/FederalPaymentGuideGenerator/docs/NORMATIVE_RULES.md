# Governança normativa — Gerador DARF/GPS

## Regra registrada

- identificador: `federal_payment_guide.late_payment_charges`;
- versão: `2026.1.0`;
- vigência suportada: desde 28/05/2009;
- data de referência: vencimento original do débito;
- responsável pela última verificação: Prazzu Tools;
- última verificação: 21/07/2026.

A regra é implementada no domínio e resolvida pelo contrato central `App\Core\Normative\NormativeRuleResolver`. O Controller não escolhe versão, vigência ou fórmula.

## Fontes oficiais

- Lei nº 11.941/2009, art. 26, no Portal Planalto;
- Receita Federal — Como calcular multa de mora;
- Receita Federal — Como calcular juros de mora.

A multa diária é de 0,33%, limitada a 20%. A Selic acumulada permanece entrada explícita porque o módulo não consulta nem replica tabela oficial de taxas.

## Histórico e reprodução

Cada resultado contém os metadados completos da regra resolvida. `ToolRunHistory` registra:

- versão normativa efetivamente utilizada;
- data de referência original, correspondente ao vencimento;
- entrada e resultado originais.

Atualizações futuras devem criar nova versão e nova vigência. É proibido editar silenciosamente a versão já utilizada ou recalcular resultados históricos.

## Limitações

O calendário do módulo ajusta apenas fins de semana nos vencimentos básicos de GPS. Feriados, expediente bancário, código, período de apuração e Selic devem ser confirmados no sistema oficial.
