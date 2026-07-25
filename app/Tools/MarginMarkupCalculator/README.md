# Calculadora de Precificação de Produtos

## Descrição

Forma o preço de venda a partir do custo total, da margem líquida desejada e das deduções percentuais incidentes sobre a venda. O módulo permanece independente e não importa regras da ferramenta de comissão.

## Funcionalidades

- formação do preço sugerido a partir do custo total;
- detalhamento de margem, markup, deduções e lucro líquido;
- memória reproduzível com política de arredondamento.

## Regras

- `custo total = custo base + custos adicionais + frete + embalagem + despesas fixas rateadas`;
- `preço de venda = custo total / (1 - margem líquida - impostos - comissão - taxas)`;
- `lucro líquido = preço de venda - custo total - deduções`;
- `markup percentual = (preço de venda - custo total) / custo total`;
- `multiplicador de markup = preço de venda / custo total`;
- margem líquida é percentual sobre a venda; markup é acréscimo bruto sobre o custo e não é sinônimo de margem;
- a soma dos percentuais deve ser menor que 100%;
- dinheiro é preservado em centavos e divisões usam arredondamento HalfUp;
- custos fixos devem ser previamente rateados por unidade ou venda;
- o resultado é uma estimativa gerencial baseada no cenário informado.

## Memória de cálculo

O resultado inclui memória estruturada com custo total, preço sugerido, lucro líquido e markup, além das premissas e políticas de arredondamento. O schema da regra foi incrementado para `2.1.0` no Lote 8.

## Experiência Essencial

O cálculo individual completo permanece disponível sem autenticação, com fórmulas, valores intermediários e resultado visíveis.

## Prazzu Plus

Lotes, cenários, importação, exportações e histórico são capacidades de produtividade e continuidade, sem alterar a correção do cálculo individual.

## Dependências

`Money`, `Percentage`, `IntegerRounding` e `CalculationMemory` do Core técnico. Não depende de outra ferramenta.


## Histórico de versões

- `2.1.0`: separação formal entre margem, markup, multiplicador e lucro líquido, com memória estruturada.
