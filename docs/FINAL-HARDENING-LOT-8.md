# Hardening final — Lote 8

## Escopo

Este lote encerra a sequência de melhoria iniciada em `TOOL-MATURITY-LOT-1.md`. O estado foi reconstruído na ordem obrigatória **ZIP original → Lotes 1–7**, seguido de releitura de `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, relatórios acumulados e `config/product_tools.php`.

O lote não cria ferramenta, não altera fórmula, slug, rota pública, vertical, `release_order`, maturidade nem fronteira Essencial/Plus. O foco é garantir que a árvore entregue possua comandos de qualidade executáveis e que regressões de integração sejam detectadas antes da release.

## Achado crítico de pipeline

A auditoria encontrou `composer.json` referenciando sete scripts PHP ausentes do repositório (`e2e-tool-catalog.php`, `e2e-observability.php`, `e2e-downloads.php`, `e2e-access.php`, `e2e-governance.php`, `e2e-report-history.php` e `e2e-run-all.php`) e oito comandos npm E2E inexistentes. A dívida já havia sido registrada em `ACCOUNTING-PAINS-LOT-6-FINAL-AUDIT.md`, mas permanecia ativa.

Criar stubs vazios apenas para satisfazer o pipeline seria uma falsa evidência de qualidade. O Lote 8 removeu as superfícies sem implementação e consolidou os comandos E2E sobre os artefatos reais existentes hoje: `e2e-environment.php`, `e2e-browser.php`, `e2e-tool-scenarios.php`, `tool-actions.spec.ts` e `tool-downloads.spec.ts`.

## Gate de integridade do repositório

Foi criado `scripts/check-repository-integrity.php`. O gate valida sem depender do Laravel inicializado:

- JSON de `composer.json` e `package.json`;
- referências de Composer a scripts PHP/Node locais;
- aliases Composer chamados por outros scripts;
- comandos `npm run` chamados pelo Composer;
- presença dos documentos e scripts constitucionais/operacionais;
- quantidade oficial contra `expected_module_count`;
- unicidade de `slug` e `release_order`.

O comando foi incorporado como `composer repository:check` e passa a ser a primeira etapa de `composer quality`. O workflow `.github/workflows/quality.yml` também executa o gate antes da instalação das dependências da aplicação. `scripts/package-distribution.ps1` executa o mesmo gate dentro da árvore de staging antes de validar e compactar a distribuição, evitando publicar pacote estruturalmente quebrado.

## E2E e CI

A superfície E2E suportada ficou explícita:

- `e2e:scenarios` / `e2e:scenarios:check`;
- `e2e:browser:actions`;
- `e2e:browser:downloads`;
- `e2e:browser:test`;
- `e2e:ci:complete`;
- `e2e:browser:complete`.

O npm recebeu `e2e:test:downloads`, simétrico ao teste de ações já existente. O GitHub Actions passou a validar também o contrato estático dos 100 cenários E2E das 50 ferramentas antes de `composer release:check`.

## Estado preservado

- 50 ferramentas oficiais;
- 49 em `contabilidade` e 1 em `rh`;
- 13 `active`, 37 `beta`, 0 `draft`;
- 141 contratos Plus declarados/estritos/funcionais;
- dívida Plus zero.

`schema_version` avança para `3.26.0` e `release_readiness` para `final_hardening_lot_8_audited`.

## Limitações de validação do ambiente

O ambiente usado para o lote não possui as extensões PHP `dom`, `mbstring`, `pdo_sqlite`, `xml` e `xmlwriter`, portanto `composer release:check`, PHPUnit integral e caches Laravel que dependem dessas extensões não podem ser usados como evidência local. O projeto já possui `scripts/check-platform.php` e o GitHub Actions instala explicitamente essas extensões; a ausência local não foi mascarada nem tratada como defeito do projeto.

## Validações executadas

Passaram neste ambiente:

- `php scripts/check-repository-integrity.php`;
- `php scripts/e2e-tool-scenarios.php check` — 100 cenários, 50 ferramentas, válido + inválido;
- `php artisan tools:check-architecture`;
- `php scripts/check-accounting-pains.php` — 50 ferramentas e Plus 141/141 com dívida zero;
- `php artisan analytics:check`;
- `node --check resources/js/app.js`;
- lint dos arquivos PHP alterados;
- reconstrução independente do estado **ZIP original → Lotes 1–7**, com 0 arquivos faltantes, 0 extras e 0 divergentes contra a base do Lote 8.

O lint global do repositório iniciou normalmente, mas excedeu o limite operacional do ambiente antes de concluir; portanto não é registrado como aprovação global.

