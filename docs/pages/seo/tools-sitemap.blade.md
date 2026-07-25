# Sitemap de ferramentas

- **Tipo:** documento XML público de descoberta
- **Rota:** `tools.sitemap`
- **URL:** `/sitemap-tools.xml`
- **Controller:** `App\Http\Controllers\Seo\ToolSitemapController`
- **Implementação principal:** `resources/views/seo/tools-sitemap.blade.php`
- **Status:** ativo

## Objetivo

Publicar para mecanismos de busca as URLs do catálogo, das categorias
preenchidas e de todas as ferramentas públicas.

## Como funciona

O controller recebe somente `ToolCatalog`. As categorias e ferramentas são
projetadas por esse catálogo, sem listas paralelas, consultas a módulos
específicos ou acesso direto ao banco. A resposta usa
`application/xml; charset=UTF-8`.

## Conteúdos e estados

- URL principal do catálogo;
- uma URL para cada categoria que possui ferramenta pública;
- uma URL para cada manifesto visível no catálogo;
- documento XML válido mesmo quando não houver categorias ou ferramentas.

Ferramentas fora do catálogo público não são incluídas.

## Dependências

- `App\Core\Tools\ToolCatalog`;
- rotas principais declaradas nos manifests;
- taxonomia pública projetada pelo catálogo;
- referência estática em `public/robots.txt`.

## Regras de manutenção

- nunca manter slugs manualmente no controller ou na view;
- não consultar `ToolRegistry`, banco ou módulos diretamente;
- incluir somente URLs fornecidas ou resolvidas a partir do catálogo;
- preservar o cabeçalho XML e o tipo de conteúdo;
- atualizar esta documentação se a composição do sitemap mudar.

## Validação mínima após alterações

- confirmar o tipo de conteúdo XML;
- comparar todas as URLs com `ToolCatalog::all()`;
- confirmar que categorias vazias não aparecem;
- validar a referência em `public/robots.txt`;
- executar `ToolDiscoveryTest`.
