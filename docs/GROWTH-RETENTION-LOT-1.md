# Crescimento e Retenção — Lote 1 — Captura e continuidade pós-resultado

## Objetivo

Este lote inicia a frente de crescimento e retenção sem alterar a identidade do Prazzu Tools definida no README da raiz. O produto continua sendo uma plataforma de ferramentas pontuais, não um SaaS de gestão, e nenhum cálculo passa a exigir conta.

O escopo deste lote é deliberadamente curto:

1. transformar a newsletter existente em um canal real e persistente de retorno;
2. tornar a captura acessível também abaixo do breakpoint `xxl`;
3. substituir o CTA pós-resultado centrado em venda de Plus por continuidade coerente com o estado real do usuário e da ferramenta.

As mudanças de `Meu Prazzu`, Home personalizada, recomendações editoriais, SEO de ferramentas e rodapé permanecem para lotes posteriores.

## Newsletter persistente

A submissão de `/newsletter` agora grava `newsletter_subscribers` com:

- e-mail normalizado e único;
- usuário associado quando autenticado;
- vertical ativa quando disponível;
- caminho de origem;
- data de inscrição;
- campo de cancelamento preparado para evolução posterior.

Submissões repetidas do mesmo e-mail são idempotentes e não criam duplicatas. A rota possui throttling técnico, sem relação com Essencial ou Plus.

O evento `newsletter.subscribed` é registrado uma única vez por nova inscrição para que a criação desse canal de retorno possa ser medida no Analytics compartilhado.

## Presença responsiva

O formulário foi extraído para um componente Blade compartilhado porque agora existem duas utilizações concretas:

- sidebar desktop `xxl`;
- bloco dentro do conteúdo principal em telas menores.

A mensagem deixa de prometer apenas “novidades” e passa a explicar o valor prático: novas ferramentas e atualizações relevantes de regras, tabelas ou conteúdos.

## CTA pós-resultado

O CTA continua aparecendo apenas quando existe resultado significativo e mantém limite de uma exibição por ferramenta durante a sessão.

O conteúdo agora respeita a filosofia do README:

- visitante: conta gratuita é apresentada somente como continuidade opcional; o texto afirma explicitamente que os cálculos seguem disponíveis sem cadastro;
- autenticado + ferramenta com histórico real: o CTA aponta para a rota de histórico existente;
- autenticado + ferramenta sem histórico: o CTA sugere voltar ao catálogo, sem inventar persistência inexistente.

Nenhum recurso Essencial ou Plus teve gate, fórmula, catálogo, slug ou política comercial alterados.

## Compatibilidade e arquitetura

- Nenhuma ferramenta individual foi modificada.
- Nenhum módulo foi adicionado ou removido do inventário oficial.
- Nenhuma URL canônica de vertical foi alterada.
- A newsletter permanece capacidade global da plataforma e registra vertical apenas como contexto, sem criar implementação paralela por nicho.
- A extração do formulário compartilhado ocorreu somente após existir reutilização concreta em duas superfícies, em linha com `CORE_CANDIDATES.md`.

## Validação do lote

A cobertura automatizada adicionada verifica:

- persistência, normalização e idempotência da newsletter;
- vínculo opcional com usuário autenticado;
- disponibilidade do formulário no desktop e em telas menores;
- CTA de visitante sem venda forçada de Plus;
- CTA autenticado apontando para histórico somente quando a rota real existe.

No ambiente de preparação deste lote, `php -l`, `node --check`, inspeção de rotas e `php artisan tools:check-architecture` puderam ser executados; o gate arquitetural encerrou sem violações. A execução de PHPUnit/Blade cache ficou bloqueada pela ausência das extensões PHP `dom`, `mbstring`, `xmlwriter` e do driver `pdo_sqlite`; o build Vite ficou bloqueado porque o `node_modules` fornecido no ZIP não contém o binário opcional nativo do Rollup para Linux. Essas limitações são do ambiente de validação e não foram contornadas alterando dependências do projeto.

## Próximo lote planejado

O Lote 2 deve partir do ZIP original + este lote e tratar `Minha conta` como hub de continuidade (`Meu Prazzu`), reaproveitando histórico e favoritos existentes sem introduzir CRM, tarefas, workflow ou gestão operacional.
