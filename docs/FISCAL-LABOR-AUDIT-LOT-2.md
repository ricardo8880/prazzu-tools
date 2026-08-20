# Auditoria fiscal e trabalhista — Lote 2

## Continuidade

O estado foi reconstruído obrigatoriamente na ordem:

1. ZIP original `prazzu-tools.zip`;
2. patch `prazzu-tools-lote-1.zip`;
3. leitura de `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, relatório do Lote 1 e `config/product_tools.php`.

Nenhum slug, ID oficial, vertical, rota pública, `release_order` ou quantidade de ferramentas foi alterado.

## Achado crítico corrigido — Reforma Tributária em 2026

O `TaxReformSimulator` somava a carga federal legada integral à CBS de 0,9% e ao IBS de 0,1% em 2026. Isso contradizia a própria nota do módulo sobre compensação/dispensa do ano-teste e podia produzir aumento artificial da carga simulada.

A versão de cálculo `1.1.0` agora:

- mantém CBS 0,9% e IBS 0,1% visíveis;
- calcula seus valores líquidos pela aproximação de créditos já existente;
- aplica compensação contra a carga federal legada informada, até o limite desta carga;
- não trata CBS/IBS de 2026 como custo adicional automático;
- explicita que a legislação também prevê hipótese de dispensa do recolhimento vinculada ao cumprimento das obrigações acessórias;
- adiciona regressões para 2026 sem crédito, 2026 com crédito aproximado e a transição de 2029.

A ferramenta permanece `beta`: a correção elimina um erro identificado, mas não transforma um simulador paramétrico em apuração fiscal oficial.

## Transparência trabalhista — Férias

A Calculadora de Férias não apura automaticamente INSS e IRRF. Apesar disso, o resultado era rotulado como `Total líquido estimado`, o que podia ser interpretado como líquido legal após incidências obrigatórias.

O rótulo público foi alterado para `Total após descontos informados` e o README passou a explicitar que o escopo atual é bruto + descontos manuais. A fórmula existente não foi modificada.

## Correção de maturidade declarada

A auditoria detectou ferramentas marcadas `active` enquanto seus próprios `QUALITY.md` ainda possuem checklists abertos. Foram corrigidas para `beta` sem retirar funcionalidades:

- `TaxRegimeComparator`;
- `ProLaboreProfitDistributionCalculator`;
- `VacationCalculator`.

Durante a validação do gate criado no Lote 1, também foi encontrado o mesmo conflito em `ReceiptIssuer`. Embora documental e fora do núcleo fiscal/trabalhista, ele foi corrigido para `beta` como reparo de continuidade, pois o gate do Lote 1 exige que ferramenta Active sob o framework atual não possua checklist aberto.

Estado de maturidade após este lote:

- 13 `active`;
- 37 `beta`;
- 0 `draft`.

As sete promoções legítimas do Lote 1 permanecem `active`.

## Fontes normativas verificadas

Para o achado que exigiu alteração de cálculo, foram confrontadas fontes oficiais vigentes em 19/08/2026:

- Emenda Constitucional nº 132/2023;
- Lei Complementar nº 214/2025;
- Receita Federal — `Entenda a Reforma Tributária do Consumo`;
- referências regulatórias de 2026 aplicáveis à implementação do IBS/CBS.

As demais ferramentas fiscais estaduais continuam paramétricas quando alíquota, benefício, NCM, município ou enquadramento não podem ser determinados com segurança. Este lote não introduz tabelas estaduais inventadas nem falsa precisão.

## Gates adicionados

`FiscalLaborAuditLot2Test` impede:

- retorno silencioso a `active` das quatro ferramentas com dívida de qualidade explicitamente aberta;
- remoção acidental da documentação/implementação que neutraliza a soma indevida de CBS/IBS no ano-teste de 2026.

## Core técnico

Nenhum novo candidato justificou promoção ao Core. A correção de 2026 pertence ao domínio de transição da Reforma Tributária e não possui uma segunda ferramenta com necessidade equivalente. `CORE_CANDIDATES.md` permanece sem nova abstração.

## Continuidade obrigatória

Antes do Lote 3, reconstruir o estado na ordem: ZIP original → Lote 1 → Lote 2. Em seguida reler o README da raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos Lotes 1 e 2 e o inventário executável.
