# Experiência do Usuário — Lote 6 — Analytics de sessões, pessoas e retenção de coorte

## Objetivo

Executar o segundo lote posterior à rodada UX 1–4, restrito aos pontos definidos na auditoria consolidada:

1. separar corretamente sessões de pessoas nas métricas de jornada das ferramentas;
2. remover nomenclaturas ambíguas como “audiência única” quando a base real é sessão;
3. adicionar retenção de coorte D1, D7 e D30 sem transformar coortes imaturas em falso 0%.

## Estado de origem

Este lote parte do projeto atual enviado pelo usuário após os UX Lotes 1–4, com o UX Lote 5 aplicado por cima antes de qualquer alteração.

Antes das mudanças foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `config/product_tools.php`, `docs/UX-EXPERIENCE-LOT-1.md` até `docs/UX-EXPERIENCE-LOT-5.md` e o contrato atual de Analytics das ferramentas.

## Mudanças

- `ToolAnalyticsQuery` passou a manter duas bases explícitas:
  - **sessão**: prioriza `analytics_session_id` e usa `session_id` como compatibilidade;
  - **pessoa**: aceita somente identidade persistente por `user_id` ou `visitor_id`.
- O funil principal de atrito continua sendo calculado por sessão, pois ele responde onde uma visita/tentativa perde o usuário durante a tarefa.
- O painel também mostra um funil separado por pessoas identificáveis, evitando chamar sessões de “pessoas”.
- As métricas por ferramenta agora expõem `session_opens`, `session_starts`, `session_results` e equivalentes `people_*`.
- Os campos internos `unique_*` foram preservados como aliases de sessão para compatibilidade com consumidores anteriores, mas deixaram de ser apresentados com nomenclatura ambígua na interface.
- Alertas de UX passaram a falar em sessões quando a taxa usada é baseada em sessões.
- Retenção geral continua significando “resolveu novamente em outro dia dentro do período”.
- Foram adicionadas retenções de coorte exatas:
  - **D1**: novo resultado exatamente 1 dia após o primeiro resultado observado no período;
  - **D7**: exatamente 7 dias depois;
  - **D30**: exatamente 30 dias depois.
- O denominador de D1/D7/D30 inclui somente pessoas cuja coorte já teve tempo suficiente para atingir o dia medido até o final do período observado.
- Quando nenhuma coorte está madura, a taxa é `null` e a interface mostra `—`, nunca `0%` artificial.
- O painel geral e o detalhe individual da ferramenta passaram a explicar explicitamente a diferença entre sessão, pessoa e retenção de coorte.

## Privacidade

Nenhuma nova coleta foi criada. Nenhum `input_payload`, `result_payload`, CPF, CNPJ, nome, e-mail ou valor financeiro é consultado.

A leitura de pessoa usa somente as identidades técnicas persistentes que já existiam no Analytics (`user_id` / `visitor_id`). Sessões sem identidade persistente não entram em métricas de pessoas ou retenção.

## Limites preservados

- Nenhuma fórmula, ferramenta, request, controller de cálculo, rota, slug, vertical, inventário ou `release_order` foi alterado.
- Nenhum evento de Analytics novo foi criado.
- Nenhuma migration ou coluna nova foi adicionada.
- O histórico das métricas operacionais foi preservado mantendo aliases internos para os antigos campos `unique_*`.
- O Lote 5 de UX não foi reaberto.
- `CORE_CANDIDATES.md` não foi alterado: a mudança pertence ao Core de Analytics já existente e não cria uma nova abstração transversal.

## Continuidade obrigatória

Qualquer lote posterior deve usar como fonte de verdade o projeto consolidado contendo os UX Lotes 1–6 e reler novamente o README, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `config/product_tools.php` e todos os relatórios `docs/UX-EXPERIENCE-LOT-*.md` antes de alterar o projeto.

O próximo lote planejado é o Lote 7 de higiene e consistência do projeto, sem reabrir UX ou Analytics fechados aqui.

## Validação executada

- `php -l app/Core/Analytics/Application/Queries/ToolAnalyticsQuery.php`: aprovado;
- `php -l tests/Unit/Core/Analytics/ToolExperienceMetricsTest.php`: aprovado;
- `php artisan tools:check-architecture`: aprovado, sem violações;
- `php artisan analytics:check`: aprovado;
- `node --check resources/js/app.js`: aprovado;
- `git diff --check` nos arquivos do lote: aprovado;
- compilação direta das views `admin.analytics.tools` e `admin.analytics.tool` com `BladeCompiler`: aprovada;
- verificação direta da identidade confirmou 3 sessões distintas representando 2 pessoas persistentes;
- PHPUnit não iniciou porque o PHP do ambiente não possui `dom`, `mbstring` e `xmlwriter`; as dependências do projeto não foram alteradas para contornar o runtime.
