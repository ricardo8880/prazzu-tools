# E2E — Lote 7 — Instrumentação, correlação e logs

## Objetivo

Relacionar cada interação do Playwright aos registros produzidos pelo Laravel sem inserir ou remover logs durante o teste.

## Entregas

- cabeçalhos `X-E2E-Run-Id` e `X-E2E-Scenario-Id`;
- middleware global inerte fora do ambiente `e2e`;
- canal exclusivo em JSON Lines no storage isolado;
- registro de requisição concluída e exceção não tratada;
- aviso de query lenta sem registrar bindings;
- IDs devolvidos nos cabeçalhos da resposta;
- anexação dos logs correlacionados ao relatório do Playwright;
- limpeza do arquivo de log no início da suíte;
- gate executável `composer e2e:observability:check`.

## Segurança

A instrumentação só é ativada quando `APP_ENV=e2e` e `E2E_OBSERVABILITY_ENABLED=true`. O canal reutiliza o processador de sanitização já existente, não registra corpo integral das requisições e não grava bindings SQL. Os logs ficam em `storage/app/e2e/logs/e2e.jsonl` e são removidos por `composer e2e:clean`.

## Contrato do registro

Todo registro correlacionado deve possuir no contexto:

- `e2e_run_id`;
- `e2e_scenario_id`.

Registros de requisição podem incluir método, path, rota, status, duração e ID interno do usuário. Exceções incluem classe, mensagem, arquivo e linha. Dados de formulário, cookies, tokens e documentos não são registrados por esta camada.

## Continuidade

O Lote 8 deve consumir os mesmos IDs ao validar downloads, preservando no relatório o arquivo recebido, a resposta HTTP e os logs Laravel correspondentes. Não deve criar uma segunda infraestrutura de correlação.
