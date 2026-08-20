# Testes e confiabilidade — Lote 3

## Continuidade

O estado foi reconstruído obrigatoriamente na ordem:

1. ZIP original `prazzu-tools.zip`;
2. patch `prazzu-tools-lote-1.zip`;
3. patch `prazzu-tools-lote-2.zip`;
4. leitura de `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, relatórios dos Lotes 1 e 2 e `config/product_tools.php`.

Nenhum slug, ID oficial, vertical, rota pública, `release_order`, fórmula de produção ou quantidade de ferramentas foi alterado.

## Dívida de confiabilidade eliminada

O Lote 1 precisou manter uma exceção explícita para quatro ferramentas `active` anteriores ao framework atual de qualidade:

- `AccountingFeesCalculator`;
- `BusinessDocumentValidator`;
- `LaborTerminationCalculator`;
- `MarginMarkupCalculator`.

A exceção foi removida neste lote. Cada uma passou a possuir:

- `Quality/RiskProfile.php`;
- `Tests/Fixtures/GoldenCases.php`;
- `Tests/Unit/ToolQualityContractTest.php`;
- `QUALITY.md` sem checklist aberto.

Os golden cases não criam resultados novos. Eles registram, em formato executável pelo framework de qualidade, cenários que já são protegidos pelos testes unitários/feature existentes e pelos limites documentados de cada módulo.

## Cobertura adicionada por ferramenta

### Honorários Contábeis

- cenário principal com honorário mínimo/recomendado conhecido;
- fronteira de ausência de sócio/titular;
- rejeição de reajuste sobre valor zero;
- HalfUp de centavo no reajuste;
- não aplicação de consulta automática a índice oficial.

### Validador CNPJ/CPF/IE

- CPF e CNPJ conhecidos;
- detecção automática por formato suportado;
- documento repetido/inválido;
- UF de IE não suportada sem inferência;
- regressão de IE de São Paulo;
- dependência externa classificada como opcional, preservando validade matemática local.

### Rescisão Trabalhista

- cenários típicos e fronteiras contratuais;
- rejeição de combinação incompatível em contrato a termo;
- arredondamento/progressividade do INSS 2026;
- limites explícitos para estabilidade e normas coletivas;
- transição normativa da tabela previdenciária;
- regressão específica do regime de empregado doméstico.

### Margem e Markup

- cenário principal de formação de preço;
- denominador inválido quando percentuais chegam a 100%;
- custo zero;
- política de arredondamento/markup;
- não aplicação de rateio automático de custos fixos empresariais.

## Gate de maturidade endurecido

`OfficialToolMaturityTest` não possui mais a lista `LEGACY_ACTIVE_WITHOUT_CURRENT_QUALITY_ARTIFACTS`.

A partir deste lote, **toda** ferramenta oficial marcada `active` precisa possuir os quatro artefatos atuais de qualidade e `QUALITY.md` sem pendências. Isso fecha a exceção temporária criada no Lote 1 e impede que uma ferramenta ativa permaneça fora do mesmo contrato de confiabilidade das demais.

## Maturidade e inventário

O estado de maturidade permanece:

- 13 `active`;
- 37 `beta`;
- 0 `draft`.

As quatro ferramentas deste lote já eram `active`; o objetivo foi produzir evidência de confiabilidade compatível com o status existente, não alterar contagem de maturidade.

`config/product_tools.php` avança para schema `3.21.0` e `release_readiness = tool_reliability_lot_3_hardened`.

## Core técnico

Nenhuma nova abstração foi promovida. `RiskProfile`, `GoldenCaseSuite`, `ToolRiskClassifier` e `GoldenCaseSuiteValidator` já resolvem a necessidade transversal. Criar outro serviço para “qualidade de ferramenta ativa” duplicaria contratos existentes.

## Continuidade obrigatória

Antes do Lote 4, reconstruir o estado na ordem: **ZIP original → Lote 1 → Lote 2 → Lote 3**. Em seguida reler o README da raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos Lotes 1–3 e o inventário executável antes de qualquer alteração.
