# Remediação Prazzu Plus — Lote 11 — Gate de qualidade e E2E

## Falhas observadas

Depois da correção sintática, o lint aprovou 1.845 arquivos. O `release:check` avançou até o Pint e encontrou dívida de estilo acumulada. O comando E2E também não iniciava porque arquivos referenciados pelo `composer.json` e scripts npm necessários estavam ausentes.

## Ajustes

- `scripts/e2e-environment.php` prepara e verifica `.env.e2e`, banco SQLite e storage exclusivamente E2E;
- `scripts/e2e-browser.php` confirma a instalação do Playwright e do Chromium;
- `scripts/e2e-report-txt.mjs` inicializa o relatório textual da execução;
- `npm run e2e:test` executa as auditorias de ações e downloads existentes;
- `npm run e2e:install` instala somente o Chromium requerido;
- `scripts/finalize-quality.ps1` aplica o Pint oficial e executa novamente o gate completo.

## Aplicação

Após sobrepor os arquivos do lote, execute:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\finalize-quality.ps1
npm run e2e:install
composer e2e:browser:test
powershell -ExecutionPolicy Bypass -File .\scripts\package-distribution.ps1
```

O primeiro comando formata arquivos PHP do projeto. Revise e mantenha essas alterações no Git; elas são a correção da dívida Pint reportada pelo gate.

## Estado funcional

O lote não altera a matriz Prazzu Plus: permanecem 43 ferramentas, 137 contratos estritos, 137 contratos funcionais, 137 marcadores únicos e dívida zero.
