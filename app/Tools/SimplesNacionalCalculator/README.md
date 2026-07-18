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
- O cálculo principal utiliza `Money` e `Percentage`; projeções e alertas ainda
  possuem conversões legadas para `float` e não servem como referência para
  novas ferramentas até essa dívida ser removida.
- Comparações já delegam ao domínio. Projeções e alertas ainda concentram parte
  da regra em Actions e devem ser extraídos para serviços de domínio.
- Visitantes acessam capacidades Essenciais e Plus sem autenticação durante a
  política gratuita de lançamento.
- Login é exigido somente para salvar e recuperar histórico.
- A ferramenta declara quais capacidades são Plus, mas não consulta plano,
  cobrança ou gratuidade. Essa decisão pertence ao Core.
- Cobrança recorrente não é implementada pelo módulo.

## Dependências

- `Money`, `Percentage`, arredondamento e exceções compartilhadas do Core;
- contratos centrais de acesso e política comercial;
- contratos centrais de histórico, que substituirão a persistência local
  legada antes da promoção do módulo;
- Laravel somente em Presentation e Infrastructure;
- Bootstrap e componentes visuais compartilhados da plataforma;
- JavaScript específico, quando necessário, dentro de `Resources/js` deste
  módulo e limitado por `[data-tool="calculadora-simples-nacional"]`.

O módulo não depende de outra ferramenta nem conhece organizações, assinaturas,
vagas empresariais ou detalhes de cobrança.

## Dívida arquitetural formal

O histórico mensal atual é uma implementação legada local. Ele grava inclusive
o nome da empresa, enquanto o manifesto ainda declara `supportsHistory: false`
e `storesSensitiveData: false`. O módulo permanece em `beta` e não pode ser
promovido para `active` até que esse histórico seja migrado para o Core, os
dados existentes sejam tratados com segurança e o manifesto passe a refletir a
capacidade real. Essa persistência não deve ser copiada para novos módulos.

## Histórico de versões

- `1.0.0` — estado beta atual: cálculo do Simples e Fator R, comparações,
  projeção, alertas, capacidades e histórico mensal.
- Lote 1: registro do módulo, rota inicial, domínio de anexos e faixas, catálogo
  de capacidades, página Bootstrap e testes básicos.
