# Automação E2E — Lote 1 — Fundação e Inventário

## Estado reconstruído

O lote foi iniciado a partir do ZIP original, após leitura integral do README raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `docs/PRODUCT-TOOLS-INVENTORY.md`, `config/product_tools.php` e relatórios anteriores.

Nenhum slug, rota pública, cálculo, manifesto ou módulo foi alterado.

## Escopo entregue

- inventário E2E executável em `config/e2e_quality.php`;
- classificação das 32 ferramentas por risco e superfície de interação;
- definição dos perfis smoke, regressão, completo e exploratório;
- contrato oficial da futura automação;
- gate arquitetural contra ferramentas omitidas, duplicadas ou divergentes;
- registro de continuidade para o próximo lote.

## Decisões

### Fonte de verdade

`config/product_tools.php` permanece a fonte de verdade do produto. O inventário E2E não redefine nome, slug, ordem ou estado; ele acrescenta somente metadados de qualidade.

### Classificação de risco

Vinte e cinco módulos já possuem `Quality/RiskProfile.php`; o inventário referencia essa origem. Sete módulos ainda não possuem esse perfil e receberam avaliação provisória somente para priorização E2E:

- `AccountingFeesCalculator`;
- `BusinessDocumentValidator`;
- `LaborTerminationCalculator`;
- `MarginMarkupCalculator`;
- `ProLaboreSimulator`;
- `ProfitDistributionCalculator`;
- `SimplesNacionalCalculator`.

A avaliação provisória não substitui nem promove antecipadamente um contrato de domínio. A regularização dos perfis deve ocorrer em tarefa própria ou quando um lote precisar deles concretamente.

### Superfícies inventariadas

O inventário registra formulários, resultados, downloads, histórico, uploads, processamento em lote, geração documental e ações secundárias. Esses metadados orientarão a ordem de implantação, sem tentar descrever ainda cada campo ou clique.

## Matriz de prioridade

1. **Crítica**: risco tributário/laboral elevado, múltiplos fluxos ou geração sensível.
2. **Alta**: cálculo normativo, documento profissional, upload ou persistência relevante.
3. **Moderada**: fluxo financeiro/documental menos volátil, ainda exigindo navegador.
4. **Baixa**: cálculo informacional com menor exposição, sem dispensar smoke e regressão.

A prioridade controla a ordem de cobertura; nenhuma ferramenta é excluída.

## Critérios de aceite do lote

- exatamente 32 entradas E2E;
- módulos, IDs e slugs idênticos ao inventário oficial;
- riscos pertencentes à lista fechada;
- cenários mínimos declarados para todas as entradas;
- formatos de download normalizados e sem duplicidade;
- documentos do contrato e do lote existentes;
- teste arquitetural executável.

## Fora de escopo

- instalação do Playwright;
- ambiente `.env.e2e`;
- banco isolado de navegador;
- `data-testid` nas views;
- execução real de formulários;
- logs correlacionados;
- validação de downloads.

Esses itens pertencem aos próximos lotes e não foram antecipados para manter o projeto leve e evitar abstrações sem consumidor.

## Continuidade obrigatória para o Lote 2

1. Reconstruir o projeto a partir do ZIP original.
2. Aplicar este ZIP incremental antes de qualquer alteração.
3. Reler README, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, este relatório, o contrato E2E e os dois inventários executáveis.
4. Preservar as 32 ferramentas, slugs e classificação deste lote.
5. Implementar somente o ambiente E2E isolado, com configuração, banco, storage, filas, e-mail e integrações seguras.
6. Não instalar o runner de navegador antes de o ambiente isolado estar validado.
