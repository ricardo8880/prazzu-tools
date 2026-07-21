# Registro normativo

## Finalidade

Este documento define as famílias de regras e fontes que deverão sustentar a
Calculadora de Pró-Labore e Distribuição de Lucros.

Ele não substitui a legislação nem fixa valores no Lote 1. Tabelas, alíquotas,
limites e deduções somente poderão entrar no domínio com vigência explícita,
fonte oficial, caso dourado e revisão técnica.

## Política de versionamento

Cada conjunto de regras deverá possuir:

- identificador estável;
- versão interna;
- início e fim de vigência;
- data de publicação da fonte;
- data da última consulta;
- URLs oficiais;
- observações de transição;
- política de arredondamento;
- testes de fronteira.

O resultado deverá registrar as versões efetivamente utilizadas.

## Famílias normativas

### 1. Contribuição previdenciária do sócio

O contrato deverá considerar o sócio remunerado por pró-labore como contribuinte
individual a serviço da empresa, respeitando:

- alíquota de retenção aplicável;
- salário de contribuição mínimo e máximo;
- teto mensal;
- contribuições já sofridas em outros vínculos, quando informadas e suportadas;
- competência do pagamento ou crédito conforme a regra aprovada.

Fontes oficiais de referência:

- INSS — contribuição dos segurados contribuinte individual e facultativo:
  https://www.gov.br/inss/pt-br/direitos-e-deveres/inscricao-e-contribuicao/contribuicao-dos-segurados-facultativo-e-contribuinte-individual
- INSS — tabela de contribuição mensal:
  https://www.gov.br/inss/pt-br/direitos-e-deveres/inscricao-e-contribuicao/tabela-de-contribuicao-mensal
- Receita Federal — legislação e orientações previdenciárias vigentes.

### 2. IRRF sobre pró-labore

O contrato deverá resolver por competência:

- tabela progressiva mensal;
- parcela a deduzir;
- dedução por dependente;
- contribuição previdenciária dedutível;
- desconto simplificado mensal, quando vigente e aplicável;
- regras de redução ou isenção que alterem o cálculo mensal;
- arredondamento e valor mínimo de retenção, quando aplicável.

Fontes oficiais de referência:

- Receita Federal — tabelas anuais do Imposto de Renda:
  https://www.gov.br/receitafederal/pt-br/assuntos/meu-imposto-de-renda/tabelas
- Receita Federal — tributação de 2026:
  https://www.gov.br/receitafederal/pt-br/assuntos/meu-imposto-de-renda/tabelas/2026
- Receita Federal — orientações de IRRF e rendimentos do trabalho.

### 3. Contribuição previdenciária patronal

O cálculo deverá distinguir explicitamente:

- Simples Nacional fora do Anexo IV;
- Simples Nacional com atividade sujeita ao Anexo IV;
- Lucro Presumido;
- Lucro Real.

A ferramenta não inferirá anexo ou enquadramento. O usuário informará a premissa.
Incidências adicionais além da contribuição patronal básica somente serão
incluídas quando seu contrato normativo estiver aprovado.

Fontes oficiais de referência:

- Receita Federal — contribuição previdenciária no Anexo IV do Simples Nacional:
  https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cobrancas-e-intimacoes/contribuicao-previdenciaria-anexo-iv-do-simples-nacional
- Portal do Simples Nacional — legislação, manuais e perguntas e respostas:
  https://www8.receita.fazenda.gov.br/simplesnacional/
- Lei Complementar nº 123/2006 e regulamentação vigente.

### 4. Lucro disponível e distribuição

A ferramenta não apura lucro contábil. Ela recebe valores preparados pelo
profissional e valida a distribuição segundo as premissas informadas.

O contrato deverá distinguir:

- lucro contábil comprovado;
- prejuízos acumulados;
- reservas e indisponibilidades;
- antecipações;
- limites aplicáveis quando não houver escrituração contábil suficiente;
- distribuição proporcional ou por critério societário informado;
- tributação vigente de lucros e dividendos na competência analisada.

Fontes oficiais de referência:

- Receita Federal — Manual do PGDAS-D e DEFIS;
- Receita Federal — orientações de rendimentos do trabalho, lucros e dividendos;
- SPED — regras de Escrituração Contábil Digital;
- legislação societária, tributária e atos vigentes na competência.

## Casos não aplicáveis no primeiro contrato

O domínio deverá recusar, com mensagem explícita:

- MEI;
- produtor rural;
- cooperativas;
- residente no exterior;
- entidades com regime previdenciário específico;
- decisões judiciais ou suspensões de exigibilidade;
- múltiplos vínculos sem informação suficiente;
- competências sem tabela normativa cadastrada;
- distribuição sem base contábil mínima informada.

## Processo de atualização

1. identificar alteração oficial;
2. registrar fonte, publicação e vigência;
3. criar nova versão imutável da regra;
4. adicionar casos dourados de transição e fronteira;
5. obter revisão técnica;
6. publicar sem alterar resultados históricos anteriores.

## Regras implementadas no Lote 2

- vigência 2026 da retenção previdenciária do contribuinte individual;
- teto previdenciário mensal de R$ 8.475,55;
- retenção de 11% limitada ao saldo do teto após outros vínculos informados;
- contribuição patronal básica de 20% nos enquadramentos aprovados;
- tabela progressiva mensal do IRRF de 2026;
- dedução por dependente de R$ 189,59;
- desconto simplificado mensal de R$ 607,20;
- redução mensal integral ou parcial para rendimentos até R$ 7.350,00;
- arredondamento monetário `HalfUp` em cada operação percentual.

A compensação de outros vínculos depende do valor oficial informado pelo usuário.
Sem essa informação, a ferramenta calcula apenas o vínculo simulado. Critérios de
lucro distribuível permanecem pendentes para o Lote 3.
