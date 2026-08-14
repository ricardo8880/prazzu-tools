# EcadRoyaltySimulator

## Descrição
Simulador orientativo para conferir matematicamente parâmetros de preço de execução pública musical informados pelo usuário.

## Funcionalidades
- Cálculo por quantidade de UDA.
- Cálculo por UDA por m².
- Cálculo por percentual sobre base monetária.
- Projeção opcional por múltiplos períodos.

## Experiência Essencial
Calcula por quantidade de UDA, UDA por m² ou percentual sobre base monetária. O módulo não tenta adivinhar o enquadramento do estabelecimento/evento e não emite licença.

## Prazzu Plus
`period_projection` projeta o mesmo valor de referência por múltiplos períodos, sem presumir reajustes futuros.

## Regras
O módulo automatiza somente a matemática do parâmetro informado. Não infere categoria, região, grau de utilização, descontos, mínimos ou licença.

## Referência normativa
- Lei 9.610/1998, especialmente execução pública musical.
- Regulamento de Arrecadação do Ecad, revisão 12/01/2026.
- UDA de 2026: R$ 107,31, vigente até dezembro de 2026.

## Dependências
Sem integrações externas em tempo de execução. Reutiliza Money, Percentage, IntegerRounding e CalculationMemory do Core.

## Limites
Não substitui o simulador, orçamento, boleto ou autorização do Ecad. O usuário deve confirmar a linha, os critérios, mínimos, descontos, região e demais parâmetros vigentes.

## Histórico de versões
- 1.0.0 — Cobertura das dores contábeis, Lote 5.
