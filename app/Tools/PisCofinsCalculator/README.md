# Calculadora PIS e COFINS

## Descrição

Calcula PIS/Pasep e Cofins nas alíquotas gerais dos regimes cumulativo e não cumulativo para competências de 2026. A ferramenta recebe bases já classificadas pelo usuário e não tenta inferir NCM, CST, monofasia, alíquota zero, suspensão, importação, benefícios ou regimes setoriais.

## Funcionalidades

- cálculo nos regimes cumulativo e não cumulativo;
- créditos elegíveis, retenções e compensações parametrizadas;
- operações adicionais, comparação de regimes, memória, histórico e exportações.

## Experiência Essencial

- escolha do regime cumulativo ou não cumulativo;
- base tributável da competência;
- base total elegível a créditos no regime não cumulativo;
- retenções/compensações confirmadas de PIS e Cofins;
- débitos, créditos, saldo credor, valores a recolher e alíquota efetiva;
- memória de cálculo e fontes normativas.

O Essencial resolve integralmente um caso individual. Nenhuma fórmula necessária fica no Plus.

## Prazzu Plus

- até 3 operações adicionais no mesmo envio, somadas à base principal;
- créditos detalhados por operação;
- comparação matemática cumulativo × não cumulativo com as mesmas bases;
- PDF e XLSX;
- histórico autenticado.

Durante a fase de lançamento, os recursos Plus seguem disponíveis sem bloqueio comercial, conforme o README raiz.

## Regras de domínio

### Cumulativo

- PIS/Pasep: `base tributável × 0,65%`;
- Cofins: `base tributável × 3%`;
- não há desconto dos créditos gerais do regime não cumulativo.

### Não cumulativo

- débito de PIS/Pasep: `base tributável × 1,65%`;
- débito de Cofins: `base tributável × 7,6%`;
- crédito de PIS/Pasep: `base elegível × 1,65%`;
- crédito de Cofins: `base elegível × 7,6%`;
- contribuição antes de retenções: `máx(0, débito - crédito)`;
- a recolher: `máx(0, contribuição - retenção/compensação confirmada)`;
- eventual excesso de crédito é exibido separadamente e não convertido em tributo negativo.

Todos os valores usam `Money` e percentuais usam `Percentage`; não há `float`. Arredondamento monetário é HalfUp em centavos a cada aplicação percentual.

## Escopo normativo e 2026

Fontes oficiais registradas na memória normativa:

- Lei nº 9.718/1998;
- Lei nº 10.637/2002;
- Lei nº 10.833/2003;
- LC nº 214/2025;
- orientações da Receita Federal para a transição de CBS/IBS em 2026.

Em 2026, CBS e IBS estão em fase de teste/transição. A ferramenta não soma CBS/IBS como ônus adicional ao PIS/Cofins e exibe alerta para o tratamento oficial de compensação e obrigações acessórias.

## Limitações deliberadas

A ferramenta não decide se uma receita ou aquisição está sujeita a monofasia, alíquota zero, suspensão, substituição, regime especial, benefício fiscal ou direito a crédito. A base informada deve estar juridicamente classificada antes do cálculo. A comparação entre regimes é matemática e não determina enquadramento jurídico.

## Dependências

- value objects `Money` e `Percentage` do Core;
- contratos normativos, histórico e exportação do Core;
- componentes visuais compartilhados da plataforma.

## Integração com a plataforma

- Slug: `calculadora-pis-cofins`
- Rota: `tools.calculadora-pis-cofins.index`
- Vertical: `contabilidade`
- Categoria: `fiscal`
- Histórico: habilitado pelo Core
- Exportação: PDF e XLSX pelo Core
- Dados sensíveis: nenhum

## Histórico de versões

| Versão | Estado | Alterações |
| --- | --- | --- |
| 1.0.0 | Beta | Cumulativo e não cumulativo, créditos, operações adicionais, comparação, memória, histórico e exportações. |
