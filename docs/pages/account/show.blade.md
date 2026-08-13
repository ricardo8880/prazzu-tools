# Meu Prazzu — Prazzu Tools

- **Tipo:** página autenticada de continuidade da plataforma
- **Rota:** `account.show` (`/minha-conta`)
- **Controller:** `App\Http\Controllers\Auth\AccountController`
- **Implementação principal:** `resources/views/account/show.blade.php`
- **Status:** ativa

## Objetivo

Ser o ponto de retorno da conta para históricos, favoritos e ferramentas já utilizadas, sem transformar a conta em CRM, agenda ou sistema de gestão.

## Como funciona

`UserToolContinuityQuery` agrega somente metadados de `ToolRun` pertencentes ao usuário autenticado. A view não lê nem renderiza `input_payload` ou `result_payload`.

A página apresenta:

- total de resultados salvos;
- total de favoritos;
- quantidade de ferramentas com histórico;
- até quatro continuidades recentes;
- favoritos recentes;
- resumo de histórico por ferramenta;
- conta e segurança;
- acessos empresariais já existentes.

`Refazer cálculo` aparece somente quando a rota real `history.repeat` existe e continua sendo um formulário POST com CSRF. Caso não exista, a página abre a ferramenta normalmente.

## Estados vazios

- conta sem histórico: explica que os resultados aparecerão quando uma ferramenta usar persistência e oferece o catálogo;
- conta com histórico sem favoritos: explica a capacidade de favoritos sem inventar dados ou obrigar uso;
- se uma ferramenta não possui histórico/repetição, a interface não promete essa ação.

## Analytics

Atalhos de continuidade, favoritos e histórico recebem uma origem controlada. No destino, o Analytics registra `retention.continuity.used` com a superfície utilizada, sem incluir payload de cálculo ou dados pessoais.

## Regras de manutenção

- ler README da raiz, `CORE_CANDIDATES.md` e continuidade dos lotes antes de alterar;
- manter toda consulta limitada ao usuário autenticado;
- nunca renderizar payloads privados no hub;
- reutilizar rotas reais das ferramentas e os mecanismos compartilhados de histórico/favoritos;
- não criar uma segunda persistência ou um sistema de gestão dentro da conta;
- manter ações mutáveis como POST/PUT/DELETE com CSRF, nunca convertê-las em links GET.

## Validação mínima após alterações

- conferir conta vazia, com histórico, com/sem favoritos e com ferramentas sem repetição;
- testar isolamento entre usuários;
- confirmar métodos HTTP das ações;
- validar responsividade e acessibilidade;
- executar testes de conta, histórico, autenticação e Analytics.
