# Blog do Prazzu Tools

O blog é a frente editorial e de aquisição orgânica da plataforma. As postagens são administradas em `/admin/blog/posts` e os indicadores em `/admin/blog/analytics`.

## Acesso administrativo

- Em ambiente `local`, o painel pode ser liberado por `BLOG_ALLOW_LOCAL_ADMIN=true` (padrão).
- Fora do ambiente local, o usuário precisa estar autenticado e possuir função interna de administração.
- Ao conectar o login definitivo, nenhuma rota do blog precisa ser reescrita.

## Publicação

Uma postagem pode permanecer em rascunho, ser agendada ou publicada. Somente conteúdos publicados cuja data já tenha chegado aparecem no site e no sitemap. O botão **Pré-visualizar** permite revisar qualquer status.

## Analytics

Os eventos são armazenados na infraestrutura compartilhada `platform_analytics_events`. O blog registra visualizações de artigos e cliques nas ferramentas relacionadas. A mesma camada pode receber eventos das demais frentes da plataforma futuramente.

## SEO

Antes da publicação, revise título SEO, meta description, palavra-chave, canonical, indexação, imagem social, texto alternativo e ferramentas relacionadas. O painel apresenta alertas editoriais sem impedir a publicação.
