# Automação E2E — Lote 5: descoberta automática e smoke universal

## Objetivo

Eliminar listas manuais no runner de navegador. Toda ferramenta oficial registrada em `config/product_tools.php` deve entrar automaticamente no smoke test e ser confrontada com o inventário de qualidade de `config/e2e_quality.php`.

## Fluxo implementado

1. `scripts/e2e-tool-catalog.php` lê as duas fontes oficiais.
2. O exportador valida contagem, slug, módulo, cenário `page_load` e superfície `form`.
3. Um manifesto temporário é gravado em `storage/app/e2e/runtime/tool-catalog.json`.
4. O `globalSetup` do Playwright regenera esse manifesto antes da suíte.
5. `tool-smoke.spec.ts` cria um teste para cada ferramenta descoberta.
6. Cada teste abre a rota pública, verifica HTTP, raiz da ferramenta, painel de formulário e erros técnicos bloqueantes.

## Garantias

- Não existe lista duplicada de slugs no TypeScript.
- A ferramenta 33 entrará no smoke quando for adicionada ao catálogo oficial e ao inventário E2E.
- Divergências entre catálogo e inventário falham antes de abrir o navegador.
- Cada falha inclui diagnóstico do navegador e um resumo JSON da ferramenta.
- Respostas HTTP 5xx, falhas de rede e exceções JavaScript são bloqueantes.
- Respostas 4xx de recursos secundários continuam registradas para diagnóstico, mas não são bloqueadas genericamente neste lote.

## Comandos

```bash
composer e2e:catalog:check
composer e2e:catalog
composer e2e:browser:smoke
```

## Limites preservados

Este lote não preenche formulários nem valida resultados de domínio. O motor declarativo de cenários válidos e inválidos pertence ao Lote 6. Downloads profundos pertencem ao Lote 8.
