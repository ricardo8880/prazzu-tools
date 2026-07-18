# Calculadora de Honorários Contábeis

## Descrição

Ferramenta para estimar honorários mensais a partir do faturamento, regime
tributário, equipe, segmento, volume operacional e complexidade do cliente.

A estimativa é gerencial e apoia a precificação do serviço contábil. Ela não
substitui a análise de custos, escopo, região e posicionamento de cada
profissional ou escritório.

## Funcionalidades

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
- aviso para validação jurídica do contrato.

### Reajustes

- cálculo por IPCA, INPC, IGP-M ou percentual manual;
- competência de referência e identificação do cliente;
- valor atual, diferença monetária e novo honorário;
- percentuais positivos ou negativos;
- histórico e exclusão de reajustes implementados no ciclo anterior.

### Histórico e produtividade

- salvamento de precificações;
- filtro e marcação de favoritos;
- duplicação de cenários;
- exportação CSV em UTF-8;
- link público por token para compartilhamento;
- impressão do resultado compartilhado;
- exclusão de registros.

### Cadastro comercial legado

O módulo recebeu em um ciclo anterior cadastro de prospects e clientes, etapas
comerciais, proposta, contrato, pesquisa, filtros e observações. Essa informação
é preservada para registrar o histórico da implementação, mas CRM, gestão de
clientes e workflow não pertencem ao Prazzu Tools segundo o README raiz. Essa
capacidade não deve ser expandida nem usada como referência para novas
ferramentas; sua evolução exige extração para o produto apropriado do
ecossistema.

## Regras

- O resultado deve ser apresentado como estimativa gerencial transparente.
- O cálculo considera porte, regime, equipe, segmento, volume e complexidade.
- Índices de reajuste não são consultados automaticamente; o usuário informa a
  taxa oficial aplicável e registra sua origem nas observações.
- Dinheiro e percentuais devem utilizar os value objects do Core, nunca
  `float`.
- Visitantes podem calcular, gerar os documentos atuais, exportar e imprimir
  sem autenticação durante a fase gratuita.
- Persistência, histórico e favoritos pertencem ao Core e exigem autenticação.
- Exportação, impressão e compartilhamento devem utilizar os contratos centrais
  da plataforma; implementações próprias existentes são dívida arquitetural.
- Propostas e contratos são modelos auxiliares e devem ser revisados por
  profissional habilitado antes do uso.

## Dependências

- value objects `Money` e `Percentage` e regras de arredondamento do Core;
- contratos centrais de histórico, favoritos, compartilhamento, exportação e
  impressão;
- Laravel para Presentation e adaptadores de Infrastructure;
- banco de dados para recursos persistentes, após execução das migrations;
- recurso nativo de impressão do navegador para os documentos atuais.

O módulo não pode depender de outra ferramenta. Funcionalidades compartilháveis
devem ser consumidas por contratos do Core.

## Histórico de versões

- `1.1.0`: consolidação dos ciclos de contrato, cadastro comercial legado,
  reajustes, histórico, favoritos, duplicação, CSV e compartilhamento.
- `1.0.0`: precificação inicial, detalhamento, recomendações e proposta
  comercial.
