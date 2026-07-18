# Calculadora de Simples Nacional

## Descrição

Módulo fiscal para estimar DAS, identificar anexo, faixa e alíquota efetiva e
analisar o Fator R. A ferramenta permanece em evolução e seus resultados devem
ser apresentados como estimativas tributárias transparentes.

## Funcionalidades

- cálculo por anexos e faixas do Simples Nacional;
- alíquota efetiva e DAS estimado;
- cálculo do Fator R e indicação entre Anexos III e V;
- comparação de cenários;
- comparação entre anexos;
- projeção anual;
- alertas de proximidade de faixa, limite e sensibilidade do Fator R;
- histórico mensal para usuários autenticados;
- catálogo interno de capacidades Essenciais e Prazzu Plus;
- página inicial e formulários construídos com Bootstrap;
- registro no catálogo e rotas próprias do módulo.

## Regras

- Tabelas, faixas e versões de regra permanecem explícitas no Domain.
- O cálculo recebe RBT12, receita mensal, anexo e, quando aplicável, folha dos
  últimos 12 meses.
- Fator R igual ou superior a 28% seleciona o Anexo III nos casos aplicáveis;
  abaixo desse limite, seleciona o Anexo V.
- Valores monetários e percentuais utilizam `Money` e `Percentage`, nunca
  `float`.
- Projeções, comparações e alertas são regras de domínio; Actions apenas
  orquestram os casos de uso.
- Visitantes acessam capacidades Essenciais e Plus sem autenticação durante a
  política gratuita de lançamento.
- Login é exigido somente para salvar e recuperar histórico.
- A ferramenta declara quais capacidades são Plus, mas não consulta plano,
  cobrança ou gratuidade. Essa decisão pertence ao Core.
- Cobrança recorrente não é implementada pelo módulo.

## Dependências

- `Money`, `Percentage`, arredondamento e exceções compartilhadas do Core;
- contratos centrais de acesso e política comercial;
- sistema central de histórico para persistência autenticada;
- Laravel somente em Presentation e Infrastructure;
- Bootstrap e componentes visuais compartilhados da plataforma;
- JavaScript específico, quando necessário, dentro de `Resources/js` deste
  módulo e limitado por `[data-tool="calculadora-simples-nacional"]`.

O módulo não depende de outra ferramenta nem conhece organizações, assinaturas,
vagas empresariais ou detalhes de cobrança.

## Histórico de versões

- `1.0.0` — estado beta atual: cálculo do Simples e Fator R, comparações,
  projeção, alertas, capacidades e histórico mensal.
- Lote 1: registro do módulo, rota inicial, domínio de anexos e faixas, catálogo
  de capacidades, página Bootstrap e testes básicos.
