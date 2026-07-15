# Calculadora de Honorários Contábeis

Ferramenta para estimar honorários mensais a partir do faturamento, regime tributário, equipe, segmento, volume operacional e complexidade do cliente.

## Estado atual

- formulário completo de precificação;
- cálculo de honorário mínimo, recomendado e referência superior;
- índice de complexidade de 0 a 100;
- detalhamento percentual da composição do custo-base;
- visualização dos fatores aplicados;
- recomendações automáticas para apoiar a negociação;
- gerador de proposta comercial com dados do cliente, serviços, implantação, vencimento e validade;
- proposta em página própria pronta para impressão;
- regra de cálculo versionada em `1.0.0`.

## Arquitetura

- `Domain`: regras, enums, resultados e dados da proposta;
- `Application`: coordenação do cálculo e montagem da proposta;
- `Presentation`: validação, controllers e interfaces Blade;
- `Tests`: cobertura unitária e funcional.

A estimativa é gerencial e deve ser ajustada conforme custos internos, região, escopo contratado e posicionamento do escritório.

## Lote 5 — Contrato de prestação de serviços

O módulo também gera um modelo contratual com partes, escopo, honorários, vencimento, vigência, reajuste, multa, aviso de rescisão e cláusulas opcionais de LGPD e confidencialidade. O documento é apresentado em página própria para revisão e impressão e inclui aviso para validação jurídica.

## Lote 6 — CRM

O módulo passa a incluir um CRM simples e persistente para registrar prospects e clientes com:

- empresa, CNPJ/CPF, responsável, telefone e e-mail;
- honorário mensal negociado;
- etapas de prospect, negociação, cliente e inativo;
- situação da proposta e do contrato;
- pesquisa, filtro, edição, exclusão e observações comerciais;
- isolamento dos cadastros por usuário autenticado ou sessão anônima.

Antes de usar o CRM, execute as migrations da aplicação.


## Lote 7 — Reajustes

O módulo passa a oferecer cálculo e histórico de reajustes com:

- IPCA, INPC, IGP-M ou percentual informado manualmente;
- competência de referência e identificação do cliente;
- valor atual, diferença monetária e novo honorário;
- suporte a percentuais negativos e arredondamento em centavos;
- histórico persistente isolado por usuário autenticado ou sessão anônima;
- exclusão de registros e observações sobre fonte e período do índice.

Os índices não são consultados automaticamente. O usuário deve informar a taxa oficial aplicável ao contrato, mantendo a origem do percentual nas observações.


## Lote 8 — Histórico e produtividade

A versão final deste ciclo inclui:

- salvamento automático de cada precificação;
- histórico isolado por usuário autenticado ou sessão anônima;
- filtro e marcação de cálculos favoritos;
- duplicação de cenários para criar novas propostas e contratos sem redigitação;
- exportação CSV em UTF-8, compatível com Excel;
- link público por token aleatório para compartilhamento do resultado;
- impressão do resultado compartilhado ou salvamento em PDF pelo navegador;
- exclusão segura de registros do histórico.

Antes de utilizar o histórico, execute as migrations da aplicação.
