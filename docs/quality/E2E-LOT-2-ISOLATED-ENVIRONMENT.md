# Automação completa de qualidade — Lote 2 — Ambiente E2E isolado

## Objetivo

Criar um ambiente reproduzível para os testes futuros de navegador sem instalar o Playwright antecipadamente e sem utilizar dados, credenciais, storage ou integrações reais.

## Entregas

- `.env.e2e.example` com configurações exclusivas e sem segredos reais.
- SQLite dedicado em `database/e2e.sqlite`.
- disco `e2e` em `storage/app/e2e`.
- cache e sessão em memória.
- filas síncronas e falhas de fila desativadas.
- e-mails no mailer `array`.
- rede externa desativada por contrato.
- perfis determinísticos `free`, `plus` e `administrator`.
- seeder protegido contra execução fora de `APP_ENV=e2e`.
- comandos Composer idempotentes para preparar, verificar e limpar.
- gate arquitetural do isolamento.

## Comandos

```bash
composer e2e:prepare
composer e2e:verify
composer e2e:clean
```

`e2e:prepare` cria `.env.e2e` a partir do exemplo quando necessário, valida as travas de segurança, cria os diretórios, executa `migrate:fresh` no banco exclusivo e aplica `E2EEnvironmentSeeder`.

`e2e:clean` remove somente `database/e2e.sqlite` e `storage/app/e2e`. O arquivo `.env.e2e` é preservado para permitir ajustes locais sem versioná-lo.

## Travas de segurança

O script recusa a execução quando qualquer uma das condições abaixo não é atendida:

- `APP_ENV=e2e`;
- SQLite como conexão;
- banco exatamente em `database/e2e.sqlite`;
- storage exatamente em `storage/app/e2e`;
- mailer `array`;
- fila `sync`;
- sessão e cache `array`;
- rede externa desativada;
- falhas de fila sem persistência.

## Dados de acesso

Os três perfis são declarados em `config/e2e_environment.php`. Eles existem somente no banco E2E e usam o domínio reservado `.test`. As senhas são públicas e exclusivas para automação; nunca devem ser reutilizadas fora desse ambiente.

## Fora do escopo deste lote

- instalação do Playwright;
- inicialização automática do navegador;
- seletores `data-testid`;
- instrumentação de logs correlacionados;
- cenários das ferramentas;
- validação profunda de downloads.

## Continuidade para o Lote 3

- reconstruir o projeto com o ZIP original;
- aplicar, em ordem, os patches dos Lotes 1 e 2;
- reler README, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md` e os relatórios E2E;
- preservar as 32 ferramentas e seus slugs;
- instalar e configurar o Playwright usando exclusivamente o ambiente criado neste lote;
- não antecipar alterações visuais reservadas ao Lote 4.
