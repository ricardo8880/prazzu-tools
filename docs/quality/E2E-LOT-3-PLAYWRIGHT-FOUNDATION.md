# Automação E2E — Lote 3 — Fundação Playwright

## Estado reconstruído

O lote foi iniciado a partir do ZIP original, com aplicação sequencial dos Lotes 1 e 2. Foram relidos o README raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios E2E anteriores e os inventários executáveis.

## Escopo entregue

- Playwright Test fixado em `1.62.1` como dependência de desenvolvimento;
- configuração central em `playwright.config.ts`;
- Chromium desktop como projeto inicial;
- servidor Laravel iniciado com `--env=e2e`;
- screenshots, vídeos e traces preservados em falhas;
- relatórios `list`, HTML e JSON;
- captura base de console, page errors, requests falhas e respostas HTTP >= 400;
- teste piloto da Home e de `custo-funcionario-clt`;
- comandos npm e Composer para instalar, verificar e executar;
- gate arquitetural da fundação do navegador.

## Decisões

A fundação usa apenas Chromium neste lote. Firefox, WebKit e dispositivos móveis pertencem à bateria completa futura. O teste piloto usa seletores semânticos apenas para provar a infraestrutura; o contrato estável com `data-testid` será implementado no Lote 4.

O Playwright inicia `php artisan serve --env=e2e` e reutiliza servidor local somente fora de CI. O banco e storage continuam sob responsabilidade dos comandos do Lote 2.

## Execução

```bash
composer e2e:prepare
npm ci
composer e2e:browser:install
composer e2e:browser:test
npm run e2e:report
```

## Critérios de aceite

- configuração TypeScript carregável pelo Playwright;
- navegador Chromium configurado;
- aplicação iniciada exclusivamente com ambiente E2E;
- evidências armazenadas somente em `storage/app/e2e/artifacts`;
- teste piloto sem alteração de regra de domínio;
- relatório HTML e JSON habilitados;
- comandos registrados e verificador local disponível.

## Limitação do ambiente de construção

O registry npm disponível durante a construção não continha o pacote Playwright. Por isso, a instalação do pacote e do binário Chromium não pôde ser executada aqui. Os manifestos e lockfile foram preparados para `npm ci` no ambiente oficial com acesso ao registry npm.

## Continuidade obrigatória para o Lote 4

1. Reconstruir o ZIP original e reaplicar os Lotes 1, 2 e 3 em ordem.
2. Preservar a configuração, caminhos de artefatos e comandos deste lote.
3. Introduzir o contrato `data-testid` nos componentes compartilhados e nas superfícies obrigatórias.
4. Não implementar ainda o motor declarativo completo de cenários.
