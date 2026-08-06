# Automação E2E — Lote 6 — Motor declarativo de cenários

## Objetivo

Permitir que as ferramentas descrevam dados, ações e expectativas sem duplicar navegação Playwright. O catálogo continua responsável pela descoberta; o manifesto de cenários acrescenta somente o comportamento específico do domínio.

## Entregas

- `ToolScenario` como DTO imutável do Core de Qualidade.
- contrato opcional `ProvidesE2EScenarios` para módulos que futuramente desejem declarar cenários em classe própria;
- registro inicial em `config/e2e_scenarios.php`;
- validador/exportador `scripts/e2e-tool-scenarios.php`;
- manifesto temporário `storage/app/e2e/runtime/tool-scenarios.json`;
- executor Playwright genérico para `fill`, `select`, `check`, `uncheck`, `click` e `submit`;
- expectativas genéricas de visibilidade, ausência, texto, URL e valor de campo;
- dois cenários piloto da Calculadora de Custo de Funcionário CLT: fluxo válido e rejeição de salário negativo;
- gate arquitetural do contrato.

## Regras consolidadas

1. O TypeScript não contém regras contábeis ou valores esperados específicos.
2. Cada cenário pertence a um slug oficial e possui identificador kebab-case.
3. Etapas e expectativas usam exclusivamente seletores estáveis do Lote 4.
4. O manifesto é gerado antes da suíte pelo `global-setup.ts`.
5. Downloads, autenticação e logs correlacionados permanecem nos lotes próprios.
6. A cobertura das 32 ferramentas não foi antecipada; este lote valida o motor com pilotos reais.

## Comandos

```bash
composer e2e:scenarios:check
composer e2e:scenarios
composer e2e:browser:scenarios
```

## Continuidade para o Lote 7

- reconstruir o ZIP original e aplicar os Lotes 1 a 6 em ordem;
- preservar o schema `1.0.0` do manifesto ou versioná-lo explicitamente;
- implementar correlação de execução/cenário e logs estruturados sem editar código durante os testes;
- anexar logs correlacionados ao relatório Playwright;
- não iniciar validação profunda de downloads antes do Lote 8.
