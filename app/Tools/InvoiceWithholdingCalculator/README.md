# Calculadora de Retenções na Nota Fiscal

## Descrição

Estima retenções incidentes sobre notas fiscais de serviços a partir dos parâmetros confirmados pelo usuário. O módulo calcula IRRF, INSS, ISS, PIS/Pasep, Cofins e CSLL, apresenta líquido estimado e preserva memória auditável.

## Funcionalidades

- cálculo paramétrico de IRRF, INSS, ISS, PIS/Pasep, Cofins e CSLL;
- total retido e líquido estimado;
- memória de cálculo por tributo;
- múltiplas notas/serviços, relatório, PDF/XLSX e histórico no Plus.

## Experiência Essencial

- uma nota ou serviço por cálculo;
- valor bruto e descrição;
- seleção explícita dos tributos aplicáveis;
- alíquotas revisáveis;
- resultado por tributo, total retido, líquido e memória de cálculo.

O Essencial não esconde fórmula nem resultado necessário para resolver uma nota individual.

## Prazzu Plus

- bases percentuais configuráveis por tributo;
- múltiplas notas/serviços no mesmo cálculo;
- relatório de conferência por nota e tributo;
- PDF/XLSX;
- histórico autenticado.

Durante a fase gratuita de lançamento, os recursos classificados como Plus continuam disponíveis conforme a política global do produto.

## Regras

As incidências são selecionadas explicitamente e os parâmetros devem ser revisados pelo usuário.

## Regras de domínio

Para cada tributo marcado como aplicável:

`base do tributo = valor bruto × percentual de base`

`retenção = base do tributo × alíquota informada`

`líquido estimado = valor bruto − soma das retenções`

Todos os cálculos monetários usam `Money`; percentuais usam `Percentage`; arredondamento monetário ocorre em centavos pelo padrão HalfUp do Core.

A ferramenta é deliberadamente paramétrica. Natureza do serviço, prestador/tomador, regime tributário, município competente, limites de dispensa, cessão de mão de obra, empreitada, retenções de órgãos públicos e outras exceções devem ser confirmados no caso concreto.

## Referências normativas verificadas em 10/08/2026

- Lei 10.833/2003 — retenções de CSLL, Cofins e PIS/Pasep nas hipóteses legais.
- Lei 8.212/1991, art. 31 — retenção previdenciária nas hipóteses abrangidas.
- Lei Complementar 116/2003 — ISS e competência municipal.
- IN RFB 1.234/2012 — retenções em pagamentos abrangidos por entes públicos.

## Dependências

- value objects `Money` e `Percentage` do Core;
- contratos normativos, histórico e exportação do Core;
- componentes Bootstrap compartilhados da plataforma.

## Integração entre ferramentas

- Contratos publicados: Nenhum.
- Contratos aceitos: Nenhum.

## Integração com a plataforma

- Slug: `calculadora-retencoes-nota-fiscal`
- Rota principal: `tools.calculadora-retencoes-nota-fiscal.index`
- Namespace de views: `tools-calculadora-retencoes-nota-fiscal`
- Histórico: habilitado
- Exportações: PDF e XLSX


## Histórico de versões

- `1.0.0` — implementação inicial completa em 10/08/2026.
