# Calculadora de Margem e Markup

## Descrição

Ferramenta de formação de preço para calcular custo total, preço de venda,
lucro, margem e markup de produtos ou serviços. O módulo foi utilizado como
piloto do padrão modular no Lote 8.

## Funcionalidades

- cálculo individual de preço, margem, markup e composição de custos;
- consideração de frete, embalagem, despesas fixas, impostos, comissão, cartão
  e marketplace;
- cálculo em lote de produtos;
- pré-visualização e processamento de importação CSV;
- modelo de arquivo para importação;
- simulação e comparação de cenários de preço;
- exportação do cálculo atual e dos resultados em lote;
- relatório para impressão e salvamento como PDF pelo navegador;
- histórico persistente para usuários autenticados;
- repetição e exclusão de cálculos salvos;
- compartilhamento por link protegido ou público;
- métricas, auditoria e autorização integradas à plataforma.

## Regras

- `custo total = custo base + custos adicionais`;
- `preço de venda = custo total / (1 - margem e deduções variáveis)`;
- `lucro = preço de venda - custo total - deduções`;
- `markup = lucro / custo total`;
- custo total deve ser positivo;
- a soma de margem e percentuais variáveis deve preservar um divisor válido;
- dinheiro e percentuais usam `Money` e `Percentage`, nunca `float`;
- a regra de cálculo é versionada e o resultado é uma estimativa gerencial;
- visitantes acessam cálculo, lote, cenários e exportação atual durante a fase
  gratuita;
- autenticação é exigida somente para histórico e persistência;
- limites, planos e acesso Plus são resolvidos pela política central do Core;
- CSV, PDF, impressão, histórico e compartilhamento devem utilizar os serviços
  compartilhados, sem exportadores ou armazenamentos paralelos no módulo.

## Dependências

- value objects `Money` e `Percentage` e arredondamento do Core;
- contratos do Core para acesso, uso, métricas, auditoria e histórico;
- infraestrutura compartilhada de importação tabular;
- sistema central de exportação e impressão;
- Laravel para requests, responses e adaptadores de persistência;
- JavaScript específico, quando necessário, isolado em `Resources/js` deste
  módulo e limitado por `[data-tool="calculadora-margem-markup"]`.

O módulo não depende de outra ferramenta.

## Histórico de versões

- `1.0.0` — Lote 8: domínio e cálculo inicial, lote, importação, cenários,
  histórico, compartilhamento, métricas, autorização, CSV, impressão e testes.
