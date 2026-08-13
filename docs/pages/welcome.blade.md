# Home — Prazzu Tools

- **Tipo:** página pública da plataforma por vertical
- **Rota:** `home`
- **Controller:** `App\Http\Controllers\Platform\HomeController`
- **Implementação principal:** `resources/views/welcome.blade.php`
- **Fonte das ferramentas:** `App\Core\Tools\ToolCatalog`
- **Status:** ativa

## Objetivo

Apresentar a proposta da vertical ativa e oferecer caminhos rápidos para buscar, descobrir e retomar ferramentas sem transformar a Home em dashboard de gestão.

## Como funciona

`BuildContextualHome` combina conteúdo padrão ou contexto de aquisição com o catálogo central. A lista principal continua mostrando exatamente as oito ferramentas mais recentes pela regra `release_order` do inventário executável.

Fora do modo contextual de aquisição, existe uma seção separada de continuidade:

- usuário autenticado: até quatro ferramentas recentes do próprio histórico, filtradas pela vertical ativa;
- visitante: até quatro slugs usados na mesma sessão do navegador, lidos de `sessionStorage` e separados por vertical.

A continuidade autenticada abre somente uma rota GET existente (`open_url`). Repetição de histórico permanece POST dentro das superfícies responsáveis.

## Conteúdos e estados

- apresentação, benefícios e busca pública;
- faixa de categorias com contagens reais;
- continuidade de usuário autenticado quando houver histórico;
- continuidade temporária de visitante apenas quando existirem slugs válidos na sessão;
- oito ferramentas recentes por `release_order`;
- chamada para o catálogo completo;
- variação contextual de aquisição, que continua tendo prioridade sobre personalização de retorno.

## Analytics

A Home contextual mantém os eventos de aquisição já existentes.

Atalhos de continuidade usam origem controlada para que o destino registre `retention.continuity.used`. Nenhum valor digitado ou resultado é enviado nessa atribuição.

## Regras de manutenção

- não cadastrar ferramentas ou contagens diretamente na view;
- não alterar a regra das oito ferramentas da Home para acomodar personalização;
- não persistir cálculos de visitante;
- manter a continuidade temporária em `sessionStorage`, nunca `localStorage`;
- manter contexto de aquisição acima da personalização de retorno;
- nunca apontar `<a>` para rota que exige POST;
- atualizar esta documentação junto com mudanças de descoberta ou navegação.

## Validação mínima após alterações

- conferir busca, categorias e links de ferramentas;
- validar Home padrão, contextual, autenticada e visitante;
- confirmar isolamento por vertical;
- confirmar ausência de payloads privados;
- validar responsividade e acessibilidade;
- executar os testes de Home, navegação, Analytics e descoberta.
