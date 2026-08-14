# Experiência do Usuário — Lote 1 — Valor primeiro

## Objetivo

Aplicar a primeira etapa da nova rodada de UX sem alterar regras de negócio, fórmulas, slugs, catálogo, acesso ou persistência. Este lote cobre exclusivamente:

1. reduzir informação antes da tarefa principal;
2. deslocar a explicação Essencial × Plus para depois do fluxo principal e, quando houver resultado, imediatamente depois dele;
3. tornar o resultado visual e navegacionalmente mais protagonista.

## Estado de origem

O lote partiu do ZIP original `prazzu-tools.zip` e releu os documentos obrigatórios definidos no README da raiz: `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, relatórios anteriores relevantes e `config/product_tools.php`.

A frente Growth/Retention existente foi preservada. Favoritos, histórico, CTA pós-resultado, Analytics e continuidade não foram recriados.

## Mudanças

- O wrapper compartilhado `x-tools.page` deixou de renderizar Essencial × Plus antes do formulário.
- As sete páginas legadas que ainda chamavam `x-tool-feature-tiers` diretamente receberam a mesma ordem de experiência.
- O bloco Essencial × Plus virou um disclosure compacto e recolhido por padrão.
- Quando existe resultado, o frontend posiciona esse disclosure logo depois da superfície principal do resultado.
- `x-tools.result-panel` recebeu hierarquia visual própria, eyebrow `Seu resultado` e hook compartilhado `data-tool-result-panel`.
- `x-tools.result-metric` recebeu hook visual compartilhado, permitindo destaque consistente sem alterar valores ou semântica do domínio.
- Após um POST que gera resultado, a interface leva o usuário ao resultado e move foco navegacional para ele. `prefers-reduced-motion` continua respeitado pelo CSS global; nenhum cálculo ou payload é tocado.
- O cabeçalho das ferramentas ficou mais compacto para diminuir o caminho visual até a tarefa.

## Limites preservados

- Nenhuma fórmula, controller, request, rota ou regra normativa foi alterada.
- Nenhum módulo, slug, vertical, `release_order` ou estado do inventário foi alterado.
- Essencial e Plus mantêm exatamente a classificação já declarada nos manifests.
- Nenhum cálculo passou a exigir conta.
- Analytics existente não recebeu valores de campos nem payloads adicionais.
- Nenhum novo candidato ao Core foi criado: o lote reutiliza componentes compartilhados que já existem.

## Continuidade obrigatória

O próximo lote desta rodada de UX deve reconstruir a base na ordem:

`ZIP original → UX Lote 1`

Depois deve reler novamente o README, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, este relatório, os relatórios anteriores relevantes e `config/product_tools.php` antes de alterar o projeto.

O próximo escopo planejado é continuidade: melhorar `Continue de onde parou` e evoluir recomendações de relacionadas para próximos passos sem duplicar Growth/Retention já implementado.

## Validação executada

- `php artisan tools:check-architecture`: aprovado, sem violações;
- `php artisan analytics:check`: aprovado;
- `node --check resources/js/app.js`: aprovado;
- `php -l tests/Architecture/ToolValueFirstExperienceTest.php`: aprovado;
- `git diff --check`: aprovado;
- assertions estáticas confirmaram tiers depois da tarefa nas sete páginas legadas e no wrapper compartilhado;
- PHPUnit não iniciou porque o PHP do ambiente não possui `dom`, `mbstring` e `xmlwriter`;
- compilação isolada de Blade também ficou bloqueada pela ausência de `mbstring` (`mb_split`).

Essas duas últimas limitações são ambientais e não foram contornadas alterando dependências do projeto.
