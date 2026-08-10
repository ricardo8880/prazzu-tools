# Calculadora de Turnover

## Descrição

Ferramenta da vertical `rh` criada como prova arquitetural do Lote 6 multi-nicho. Calcula um indicador operacional de rotatividade usando admissões, desligamentos e quadro médio do mesmo período.

## Funcionalidades

- cálculo da taxa de turnover;
- memória transparente da fórmula;
- validação de contagens não negativas e quadro médio maior que zero.

## Experiência Essencial

O usuário informa admissões, desligamentos e quadro médio e recebe a taxa percentual. Nenhum dado pessoal é solicitado ou persistido.

## Prazzu Plus

O manifesto reserva análises avançadas por período e segmento como evolução Plus. O Lote 6 valida somente a experiência essencial, sem criar uma implementação paralela de RH.

## Regras

Fórmula adotada: `((admissões + desligamentos) / 2) / quadro médio * 100`. A ferramenta declara explicitamente que outras organizações podem usar metodologias diferentes e que o resultado não é uma regra legal.

## Dependências

Somente contratos compartilhados do Core. O módulo não importa classes internas de outras ferramentas.

## Histórico de versões

- `1.0.0` — primeira implementação, Lote 6 multi-nicho.
