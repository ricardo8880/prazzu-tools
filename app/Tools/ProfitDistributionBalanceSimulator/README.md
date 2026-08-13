# Simulador de Distribuição de Lucros com Balanço × sem Balanço

## Descrição

Compara capacidades estimadas de distribuição de lucros com e sem balanço a partir de parâmetros informados.

## Funcionalidades

- comparação dos dois cenários e memória de cálculo;
- planejamento mensal e projeção no Plus;
- relatório em PDF e XLSX no Plus.

## Escopo

Compara dois cenários parametrizados sem importar o domínio da Calculadora de Distribuição de Lucros já existente. O objetivo desta ferramenta é responder especificamente à diferença entre uma capacidade baseada em lucro contábil informado e uma referência sem balanço informada pelo usuário.

## Experiência Essencial

- com balanço: lucro contábil informado menos distribuições anteriores;
- sem balanço: receita × percentual de referência informado − tributos informados − distribuições anteriores;
- comparação e memória de cálculo.

## Prazzu Plus

- pró-labore mensal para planejamento de retiradas;
- projeção de receita/lucro/tributos usando proporções do cenário informado;
- distribuição acumulada e planejamento de até 24 meses;
- relatório PDF/XLSX.

A ferramenta não escolhe automaticamente percentual de presunção, atividade, regime, limite de isenção, retenção ou tratamento tributário dos dividendos. Em 2026, regras tributárias de distribuição podem depender do beneficiário, valores e fatos jurídicos específicos, portanto o resultado deve ser tratado como capacidade contábil estimada.

## Regras de domínio

O cenário com balanço desconta distribuições anteriores do lucro informado. O cenário sem balanço aplica o percentual de referência à receita e desconta tributos e distribuições anteriores. Valores são processados em centavos.

## Dependências

- objetos financeiros do Core;
- memória de cálculo e exportadores compartilhados;
- regras de acesso Plus da plataforma.

## Histórico de versões

- `1.0.0` — implementação inicial em 12/08/2026.
