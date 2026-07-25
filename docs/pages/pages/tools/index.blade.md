# Catálogo de ferramentas — Prazzu Tools

- **Tipo:** página pública da plataforma
- **Rota:** `tools.index` e `tools.category`
- **Controller:** `App\Http\Controllers\Platform\ToolCatalogController`
- **Implementação principal:** `resources/views/pages/tools/index.blade.php`
- **Fonte de dados:** `App\Core\Tools\ToolCatalog`
- **Status:** ativa

## Objetivo

Permitir que visitantes encontrem e acessem todas as ferramentas públicas por
nome, descrição, palavra-chave ou categoria.

## Como funciona

O controller consulta exclusivamente o catálogo central. A busca normaliza
acentos e considera nome, descrição, slug da categoria, nome público da
categoria e palavras-chave do manifesto. Os filtros apresentam somente
categorias que possuem ao menos uma ferramenta pública.

## Conteúdos e estados

- campo de busca com preservação do termo informado;
- filtros por categoria preenchida;
- cards com ícone, nome público da categoria, descrição e modalidade;
- contagem de resultados;
- estado vazio com acesso ao fluxo de sugestão de ferramenta;
- resposta `404` para categorias inexistentes ou sem ferramentas públicas.

## Dependências

- `ToolCatalog`, como fonte única de ferramentas e taxonomia pública;
- `config/tools/categories.php`, projetado pelo catálogo;
- layout e componentes compartilhados da plataforma;
- rotas principais declaradas nos manifests.

## Regras de manutenção

- não manter listas paralelas de ferramentas na view ou no controller;
- usar `category_name` e `category_icon` projetados pelo catálogo;
- não exibir categorias vazias nos filtros;
- manter busca, catálogo, menus e sitemap baseados na mesma fonte;
- atualizar este documento quando os critérios de descoberta mudarem.

## Validação mínima após alterações

- testar busca por nome, palavra-chave e nome público da categoria;
- testar filtro preenchido e categoria vazia;
- confirmar que todos os cards apontam para a rota do manifesto;
- validar estados com e sem resultados em telas pequenas e grandes;
- executar os testes de descoberta da plataforma.
