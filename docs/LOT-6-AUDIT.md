# Auditoria do Lote 6

## Problemas corrigidos

- Removida a dependência de `@vite`, que exigia `public/build/manifest.json`.
- Unificado o catálogo antes duplicado entre `config/home.php`, controllers e
  sidebars.
- Removida a inferência de categoria baseada no prefixo do slug.
- Contadores de categorias agora são calculados a partir das ferramentas reais.
- Home, busca, filtros, páginas e painéis laterais usam a mesma fonte.
- Validações de newsletter e sugestão foram movidas para Form Requests.
- Rotas reais dos módulos passaram a ter prioridade sobre a página provisória.
- Adicionados testes de consistência do catálogo.

## Continuidade

O próximo lote ou a primeira ferramenta deve partir de `ToolModule`, registrar o
módulo em `config/tools.php` e manter sua lógica, rotas, view, JavaScript e testes
isolados das demais ferramentas.
