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
- histórico mensal individual, criptografado e centralizado no Core;
- catálogo interno de capacidades Essenciais e Prazzu Plus;
- página inicial e formulários construídos com Bootstrap;
- registro no catálogo e rotas próprias do módulo.

## Experiência Essencial

A experiência gratuita calcula o DAS completo, a faixa, a alíquota efetiva e o
Fator R. Resultado, memória e correção tributária não dependem do Plus.

## Prazzu Plus

O Plus acrescenta comparação de cenários e anexos, projeção anual, alertas e
histórico mensal. Durante o lançamento, esses recursos são liberados pela
política central, mas a classificação permanece declarada no manifesto.

## Regras

- Tabelas, faixas e versões de regra permanecem explícitas no Domain.
- O cálculo recebe RBT12, receita mensal, anexo e, quando aplicável, folha dos
  últimos 12 meses.
- Fator R igual ou superior a 28% seleciona o Anexo III nos casos aplicáveis;
  abaixo desse limite, seleciona o Anexo V.
- Todos os cálculos monetários e percentuais, incluindo projeções e alertas,
  utilizam `Money` e `Percentage`; `float` não participa das regras fiscais.
- Comparações, projeções e alertas delegam suas regras a calculadores e serviços
  puros do Domain; Actions apenas convertem a entrada e apresentam o resultado.
- Visitantes acessam capacidades Essenciais e Plus sem autenticação durante a
  política gratuita de lançamento.
- Login é exigido somente para salvar e recuperar histórico.
- O histórico não recebe nome de empresa, cliente, CNPJ ou vínculo comercial.
  Competência, anexo e valores do cálculo bastam para identificar o cenário.
- RBT12 e receita mensal são dados financeiros sensíveis: o Core os armazena em
  payload criptografado, com retenção de 365 dias e acesso restrito ao titular.
- A ferramenta declara quais capacidades são Plus, mas não consulta plano,
  cobrança ou gratuidade. Essa decisão pertence ao Core.
- Cobrança recorrente não é implementada pelo módulo.

## Dependências

- `Money`, `Percentage`, arredondamento e exceções compartilhadas do Core;
- contratos centrais de acesso e política comercial;
- contrato central de histórico para gravação, consulta e exclusão de execuções;
- Laravel somente em Presentation e Infrastructure;
- Bootstrap e componentes visuais compartilhados da plataforma;
- JavaScript específico, quando necessário, dentro de `Resources/js` deste
  módulo e limitado por `[data-tool="calculadora-simples-nacional"]`.

O módulo não depende de outra ferramenta nem conhece organizações, assinaturas,
vagas empresariais ou detalhes de cobrança.

## Limites do histórico

O histórico serve somente para recuperar cálculos pessoais. Ele não cria
cadastro de empresa, carteira de clientes, colaboração, compartilhamento ou
workflow. Registros da antiga tabela local são migrados sem o nome da empresa e
a tabela legada é removida por migration própria. Registros antigos sem titular
autenticado não são migrados, pois não há identidade segura à qual vinculá-los.

## Histórico de versões

- `1.2.0` — projeções e alertas migrados para serviços puros de domínio com
  `Money` e `Percentage`, sem conversões monetárias para `float`.
- `1.1.0` — histórico migrado para o Core com payload criptografado, exclusão da
  identificação empresarial e remoção da persistência local.
- `1.0.0` — cálculo do Simples e Fator R, comparações,
  projeção, alertas, capacidades e histórico mensal.
- Lote 1: registro do módulo, rota inicial, domínio de anexos e faixas, catálogo
  de capacidades, página Bootstrap e testes básicos.
