# Calculadora de Pró-Labore e Distribuição de Lucros

## Descrição

A ferramenta auxilia contadores e profissionais contábeis a simular retiradas de
sócios por pró-labore e por distribuição de lucros, com separação clara entre
remuneração, retenções, encargos empresariais, lucro disponível e valores
recebidos.

Todo resultado deverá apresentar competência, premissas, versões normativas,
política de arredondamento, alertas e memória de cálculo. A ferramenta apoia a
análise profissional, mas não substitui escrituração contábil, folha de
pagamento, obrigações acessórias ou revisão do responsável técnico.

## Estado do módulo

O módulo está em `beta` como release candidate `1.0.0-rc.1`.

Os Lotes 1, 2, 3, 4 e 5 consolidam o contrato e implementam os domínios Essenciais
de pró-labore e distribuição de lucros. O pró-labore cobre competências de 2026
com INSS retido, IRRF, redução mensal do IR, pró-labore líquido, contribuição
patronal básica e custo total da empresa. A distribuição de lucros cobre lucro
contábil, prejuízos acumulados, reservas, ajustes, antecipações, lucro máximo
disponível, rateio proporcional ou por valores definidos e saldo não
distribuído.

A experiência Essencial de formulário e resultado consolidado para um sócio e uma competência de 2026 está implementada. O Lote 5 acrescenta simulações temporárias com dois a quatro cenários, até doze competências por cenário, até dez sócios por competência, consolidação por sócio e competência e comparação contra o cenário-base. Histórico e exportações profissionais continuam pendentes. O módulo pode ser validado em catálogo beta, mas não deve ser promovido a `active` antes das revisões técnica e de privacidade e do `composer release:check` completo.

As capacidades operacionais de histórico e persistência continuarão
desativadas até o lote específico de integração com o Core. Elas já fazem parte
do escopo de produto Prazzu Plus, mas não podem ser declaradas como disponíveis
antes de sua implementação.

## Público atendido

- contadores e equipes fiscais, trabalhistas ou contábeis;
- escritórios de contabilidade;
- profissionais que precisam documentar simulações de retirada de sócios.

## Funcionalidades

- cálculo de pró-labore com INSS, IRRF, valor líquido e custo empresarial;
- apuração e distribuição de lucros com validações contábeis;
- experiência Essencial para um sócio e uma competência;
- simulações Plus com vários sócios, competências e cenários;
- histórico autenticado com reabertura, repetição e exclusão;
- exportações em CSV, JSON e PDF pela infraestrutura compartilhada do Core.

## Escopo funcional aprovado

### Pró-labore

- pró-labore bruto por sócio e competência;
- contribuição previdenciária retida do sócio;
- base de cálculo do IRRF;
- deduções admitidas pelo contrato normativo implementado;
- IRRF, quando aplicável;
- pró-labore líquido;
- contribuição previdenciária patronal, quando aplicável ao enquadramento;
- custo total da empresa;
- memória de cálculo por rubrica.

### Distribuição de lucros

- lucro contábil do período informado pelo usuário;
- prejuízos acumulados e ajustes informados;
- reservas e valores indisponíveis;
- antecipações de lucros já realizadas;
- lucro máximo disponível para distribuição;
- distribuição proporcional às participações;
- distribuição por valores definidos, desde que o total e o critério sejam
  válidos;
- saldo não distribuído;
- detalhamento por sócio.

### Simulações

- um ou vários sócios;
- uma ou várias competências;
- múltiplos cenários de retirada;
- comparação lado a lado;
- consolidação por sócio, competência e cenário;
- indicação de mudança normativa entre competências.

### Histórico

- salvar simulações para usuários autenticados;
- recuperar, duplicar e excluir simulações;
- preservar versão da ferramenta, versão do schema e versões normativas;
- exportar resultados atuais e históricos por meio da infraestrutura
  compartilhada.

O módulo não criará tabela, repository, autenticação ou mecanismo de histórico
próprio. A implementação utilizará exclusivamente o Core técnico.

## Experiência Essencial

A experiência Essencial permite calcular, em uma única competência, o pró-labore e a distribuição de lucros de um sócio. O resultado apresenta retenções, valor líquido, encargos empresariais, lucro disponível, distribuição, total recebido, premissas, alertas, fontes normativas e memória de cálculo.

## Matriz Essencial e Prazzu Plus

### Essencial

A experiência Essencial resolve integralmente um cálculo pontual:

- cálculo completo do pró-labore;
- INSS do sócio;
- IRRF;
- pró-labore líquido;
- encargos empresariais dos enquadramentos suportados;
- lucro disponível;
- distribuição de lucros;
- resultado por sócio;
- memória de cálculo, fontes, vigências e alertas.

Nenhuma fórmula, retenção obrigatória, premissa necessária ou explicação do
resultado poderá ficar restrita ao Prazzu Plus.

### Prazzu Plus

O Prazzu Plus acrescenta produtividade, volume e continuidade:

- vários sócios na mesma simulação;
- várias competências ou meses;
- múltiplos cenários;
- comparação e consolidação;
- histórico, recuperação e duplicação;
- exportações profissionais em CSV, JSON e PDF.

Durante a fase inicial da plataforma, os recursos Plus permanecem disponíveis a
visitantes. A autenticação será exigida somente para persistência e continuidade,
conforme o README da raiz.

## Enquadramentos aprovados

### Regimes empresariais

O domínio deverá suportar explicitamente:

1. Simples Nacional fora do Anexo IV, sem calcular contribuição patronal fora
   do DAS para o pró-labore, salvo futura regra normativa específica aprovada;
2. Simples Nacional com atividade sujeita ao Anexo IV, com contribuição
   patronal calculada fora do DAS;
3. Lucro Presumido;
4. Lucro Real.

A ferramenta não inferirá o anexo, o regime ou o enquadramento da empresa. O
usuário deverá selecionar a situação aplicável e confirmar a premissa.

### Situações inicialmente não suportadas

- MEI;
- produtor rural e regras previdenciárias rurais;
- entidades imunes ou isentas com tratamento específico;
- cooperativas;
- contribuinte residente no exterior;
- múltiplos vínculos previdenciários sem dados suficientes para apuração do
  limite mensal;
- decisões judiciais, imunidades, suspensões ou regimes especiais;
- distribuição sem lucro contábil ou sem premissas mínimas de comprovação.

Esses casos deverão retornar situação não aplicável, nunca estimativa silenciosa.

## Entradas obrigatórias previstas

### Contexto

- competência de cada cálculo;
- regime tributário;
- enquadramento previdenciário empresarial;
- confirmação de atividade sujeita ou não ao Anexo IV, quando aplicável.

### Sócios

- rótulo temporário opcional;
- participação societária;
- pró-labore bruto;
- dependentes e deduções admitidas;
- contribuição previdenciária já sofrida em outros vínculos, quando o contrato
  implementado permitir considerar o teto mensal.

### Lucros

- lucro contábil do período;
- prejuízos acumulados;
- reservas e valores indisponíveis;
- ajustes;
- antecipações já realizadas;
- critério e valor pretendido de distribuição.

Valores monetários serão recebidos e processados em centavos ou `Money`, nunca
em `float`.

## Saídas obrigatórias previstas

- pró-labore bruto, INSS, base do IRRF, IRRF e pró-labore líquido;
- contribuição patronal e custo total da empresa, quando aplicáveis;
- lucro disponível, valor distribuído e saldo não distribuído;
- total recebido por sócio;
- totais por competência e cenário;
- memória de cálculo reproduzível;
- versões normativas e datas de vigência;
- alertas, limitações e situações não aplicáveis.

## Regras arquiteturais aprovadas

1. Controllers seguem exclusivamente `Request -> Action -> Response`.
2. Cálculos ficam no domínio, separados de HTTP, Blade e persistência.
3. Pró-labore e distribuição de lucros são domínios independentes e são
   combinados somente pela camada Application.
4. Valores financeiros usam `Money` ou inteiros em centavos.
5. Percentuais e arredondamentos usam contratos compartilhados do Core.
6. Regras normativas são resolvidas por competência e versão.
7. A ferramenta não recomenda automaticamente a retirada “melhor”.
8. Distribuição superior ao lucro disponível é inválida.
9. Situações fora do contrato aprovado são não aplicáveis.
10. Histórico, exportação, analytics e autenticação são capacidades do Core.
11. O módulo não depende de implementações internas de outra ferramenta.

## Dados pessoais, analytics e persistência

- nome, CPF ou documento não são necessários ao cálculo;
- rótulos de sócios são opcionais e temporários;
- rótulos, remunerações e resultados não podem ser enviados para analytics;
- visitantes processam cenários temporariamente;
- histórico depende de autenticação;
- a futura política de histórico deverá persistir somente os campos necessários
  para reproduzir o cálculo;
- dados persistidos deverão usar projeção segura e versionada do Core.

## Fontes normativas

O registro oficial de fontes, escopo, vigências e critérios de atualização está
em [`docs/NORMATIVE_RULES.md`](docs/NORMATIVE_RULES.md).

Nenhuma alíquota, faixa, teto ou dedução será incorporada ao código sem:

- fonte oficial;
- período de vigência;
- data de consulta;
- caso dourado aprovado;
- revisão técnica contábil ou tributária.

## Qualidade e ativação

O perfil de risco é `Critical`. Antes da ativação serão obrigatórios:

- casos dourados comuns e de fronteira;
- testes de arredondamento;
- testes de não aplicação;
- transições normativas;
- testes Unit, Feature, Browser, histórico e exportação;
- revisão técnica especializada;
- revisão de privacidade;
- `composer release:check` verde.

## Integração entre ferramentas

- contratos publicados: nenhum;
- contratos aceitos: nenhum.

Qualquer integração futura deverá ser opcional, explícita e intermediada pelo
Core técnico.

## Limites do produto

Este módulo não pode se tornar:

- cadastro permanente de empresas ou sócios;
- folha de pagamento;
- escrituração contábil;
- controle de pagamentos ou obrigações;
- planejamento tributário automatizado;
- gestão financeira, societária ou operacional.

## Entrega do Lote 2

O domínio implementado inclui:

- valores monetários em `Money` e centavos, sem `float`;
- resolução normativa por competência pelo Core;
- tabela previdenciária de 2026 e teto mensal;
- compensação informada de contribuição oficial em outros vínculos;
- tabela progressiva mensal do IRRF de 2026;
- escolha entre deduções legais e desconto simplificado;
- redução mensal instituída para rendimentos até R$ 7.350,00;
- contribuição patronal básica de 20% nos enquadramentos aprovados;
- memória de cálculo e metadados normativos.

Não estão incluídos neste lote RAT, terceiros, salário-família, retenções de
outros regimes especiais ou inferência automática de enquadramento.

## Próximo lote

O Lote 3 deverá implementar o domínio independente de lucro disponível e
distribuição de lucros,
IRRF, custo empresarial, memória de cálculo e respectivos testes unitários.

## Lote 6 — histórico e exportações compartilhadas

O cálculo Essencial pode ser executado por visitantes e exportado em PDF, CSV e JSON sem autenticação. Quando existe usuário autenticado, o resultado é registrado pelo histórico compartilhado do Core por 180 dias, com versão da ferramenta, schema, competência e versão de regra.

O histórico permite consultar, reabrir, repetir, exportar e excluir uma simulação. O módulo não possui tabela, repository, armazenamento ou exportador próprio: usa `ToolRunRecorder`, `ToolRunHistory`, `BrowserPrintExporter` e `TabularExportService`.

As simulações avançadas continuam temporárias neste lote. A persistência delas exige um contrato de projeção específico e permanece fora do histórico até o gate final avaliar tamanho, privacidade e compatibilidade de schema.


## Lote 7 — fechamento de engenharia

O último lote remove os placeholders dos casos dourados, alinha o manifesto aos recursos efetivamente entregues e publica a versão `1.0.0-rc.1` em estado `beta`. O estado `active` não foi declarado porque revisão técnica especializada e revisão formal de privacidade são aprovações humanas externas ao código.


## Dependências

- Laravel e componentes compartilhados da aplicação;
- contratos, registro, histórico, persistência e autorização do Core de ferramentas;
- `ToolRunRecorder` e `ToolRunHistory`;
- `BrowserPrintExporter` e `TabularExportService`;
- tabelas normativas versionadas documentadas em `docs/NORMATIVE_RULES.md`;
- autenticação da aplicação somente para recursos persistentes.


## Histórico de versões

- `1.0.0-rc.1`: fechamento de engenharia, casos dourados e manifesto final em beta;
- `0.6.0`: histórico autenticado e exportações;
- `0.5.0`: múltiplos sócios, competências e cenários;
- `0.4.0`: experiência Essencial consolidada;
- `0.3.0`: domínio de distribuição de lucros;
- `0.2.0`: domínio de pró-labore e regras normativas de 2026;
- `0.1.0`: estrutura inicial do módulo.
