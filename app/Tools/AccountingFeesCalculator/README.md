# Calculadora de Honorários Contábeis

## Descrição

Ferramenta para estimar honorários mensais a partir do faturamento, regime
tributário, equipe, segmento, volume operacional e complexidade do cenário.

A estimativa é gerencial e apoia a precificação do serviço contábil. Ela não
substitui a análise de custos, escopo, região e posicionamento de cada
profissional ou escritório.

## Funcionalidades

## Experiência Essencial

A experiência gratuita resolve integralmente os dois problemas centrais do
módulo: precificação completa de honorários e cálculo completo de reajuste. O
resultado, a memória de cálculo e as orientações necessárias não dependem do
Prazzu Plus.

## Prazzu Plus

O Plus agrega produtividade e continuidade por meio da geração pontual de
proposta e contrato, histórico individual, favoritos, duplicação e exportação
do histórico. Durante a fase gratuita de lançamento, a política central da
plataforma mantém esses recursos disponíveis para todos.

## Detalhamento das funcionalidades

### Precificação

- formulário completo de precificação;
- cálculo de honorário mínimo, recomendado e referência superior;
- índice de complexidade de 0 a 100;
- detalhamento percentual da composição do custo-base;
- visualização dos fatores aplicados;
- recomendações para apoiar a negociação;
- regra de cálculo versionada.

### Documentos comerciais

- proposta comercial com dados do cliente, serviços, implantação, vencimento e
  validade;
- contrato com partes, escopo, honorários, vencimento, vigência, reajuste,
  multa, aviso de rescisão e cláusulas opcionais de LGPD e confidencialidade;
- páginas próprias para revisão e impressão pelo navegador;
- dados informados são usados somente para gerar o documento atual e não são
  vinculados a cadastros nem persistidos pelo módulo;
- aviso para validação jurídica do contrato.

### Reajustes

- cálculo por IPCA, INPC, IGP-M ou percentual manual;
- competência e rótulo genérico para identificar o cenário calculado;
- valor atual, diferença monetária e novo honorário;
- percentuais positivos ou negativos;
- histórico e exclusão de reajustes implementados no ciclo anterior.

### Histórico e produtividade

- salvamento de precificações;
- filtro e marcação de favoritos;
- duplicação de cenários;
- exportação CSV em UTF-8;
- exclusão de registros.

### Limites do módulo

O módulo não cadastra nem gerencia prospects, clientes, funil comercial,
atividades ou documentos persistentes. Também não publica cálculos por links ou
tokens. Essas capacidades pertencem a outros produtos do ecossistema Prazzu.

## Regras

- O resultado deve ser apresentado como estimativa gerencial transparente.
- O cálculo considera porte, regime, equipe, segmento, volume e complexidade.
- Índices de reajuste não são consultados automaticamente; o usuário informa a
  taxa oficial aplicável e registra sua origem nas observações.
- A referência do cenário não cria cadastro nem relacionamento com cliente. O
  histórico pode persistir esse rótulo e as observações, que são tratados como
  campos sensíveis e devem evitar dados pessoais desnecessários.
- Dinheiro e percentuais devem utilizar os value objects do Core, nunca
  `float`.
- Visitantes podem calcular, gerar os documentos atuais, exportar e imprimir
  sem autenticação durante a fase gratuita.
- Persistência, histórico e favoritos pertencem ao Core e exigem autenticação.
- Exportação e impressão devem utilizar os contratos centrais da plataforma.
- Propostas e contratos são modelos auxiliares e devem ser revisados por
  profissional habilitado antes do uso.

## Dependências

- value objects `Money` e `Percentage` e regras de arredondamento do Core;
- contratos centrais de histórico, favoritos, exportação e impressão;
- Laravel para Presentation e adaptadores de Infrastructure;
- banco de dados para recursos persistentes, após execução das migrations;
- recurso nativo de impressão do navegador para os documentos atuais.

O módulo não pode depender de outra ferramenta. Funcionalidades compartilháveis
devem ser consumidas por contratos do Core.

## Histórico de versões

- `1.2.0`: remoção do CRM e do compartilhamento de cálculos; reajustes passaram
  a utilizar referência genérica de cenário, sem vínculo persistente a clientes.
- `1.1.0`: consolidação dos ciclos de contrato, reajustes, histórico, favoritos,
  duplicação e CSV.
- `1.0.0`: precificação inicial, detalhamento, recomendações e proposta
  comercial.

## Conformidade arquitetural — Lote 9

Esta ferramenta foi migrada para o padrão definitivo da plataforma: manifesto final,
capacidades Essencial/Plus, contratos de cálculo e integração, componentes visuais
compartilhados e políticas transversais de histórico, persistência versionada,
exportação, compartilhamento e dados sensíveis. Implementações particulares dessas
responsabilidades não devem ser introduzidas no módulo.
