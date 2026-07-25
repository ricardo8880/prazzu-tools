# Prazzu Tools — Ferramentas para contabilidade

- **Tipo:** página pública da plataforma
- **Rota:** `home`
- **Controller:** `App\Http\Controllers\Platform\HomeController`
- **Implementação principal:** `resources/views/welcome.blade.php`
- **Fonte das ferramentas:** `App\Core\Tools\ToolCatalog`
- **Status:** ativa

## Objetivo

Apresentar a proposta da plataforma e oferecer caminhos rápidos para buscar,
filtrar e abrir ferramentas contábeis.

## Como funciona

`BuildContextualHome` combina o conteúdo padrão ou um contexto de aquisição com
dados do catálogo central. A página envia a busca ao catálogo, mostra somente
categorias preenchidas e apresenta até oito ferramentas selecionadas pela regra
vigente do catálogo.

## Conteúdos e estados

- apresentação, benefícios e busca pública;
- faixa de categorias com contagens reais;
- cards de ferramentas;
- chamada para o catálogo completo;
- variação contextual de aquisição quando houver contexto ativo;
- rastreamento de impressões e cliques no modo contextual.

## Dependências

- `HomeController` e `BuildContextualHome`;
- `ToolCatalog` para categorias, contagens e ferramentas;
- configuração `config/home.php`;
- layout e componentes compartilhados;
- infraestrutura central de aquisição e analytics.

## Regras de manutenção

- não cadastrar ferramentas ou contagens diretamente na view;
- ocultar categorias sem ferramentas públicas;
- manter a busca direcionada ao catálogo;
- preservar a página funcional quando não houver contexto de aquisição;
- atualizar esta documentação junto com mudanças de descoberta ou navegação.

## Validação mínima após alterações

- conferir busca, categorias e links de ferramentas;
- validar home padrão e contextual;
- confirmar ausência de categorias vazias;
- validar responsividade e acessibilidade;
- executar os testes de home, navegação e descoberta.
