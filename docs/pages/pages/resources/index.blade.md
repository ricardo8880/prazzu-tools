# Recursos profissionais — Prazzu Tools

- **Tipo:** Página da plataforma
- **Implementação principal:** `resources/views/pages/resources/index.blade.php`
- **Status:** ativa no código atual

## Objetivo

Apresentar a Central de Recursos do Prazzu Tools e conduzir o usuário para os guias e modelos profissionais disponíveis.

## Como funciona

A página é renderizada pelo `ContentPageController`, que recebe as seções e os itens definidos em `config/resources.php`. O conteúdo utiliza cards reutilizáveis e links gerados pelas rotas `resources.index`, `resources.show` e `resources.item`.

A navegação de categorias permanece visível abaixo do cabeçalho da página. Ela utiliza o componente `nav-pills` do Bootstrap e oferece acesso direto a:

- Todos os recursos;
- Guias práticos;
- Modelos profissionais.

A categoria atual deve permanecer visualmente destacada e identificada por `aria-current="page"`.

## Estrutura e conteúdos identificados

- Hero da Central de Recursos;
- Navegação persistente entre categorias;
- Bloco de critérios editoriais;
- Cards das categorias existentes;
- Cards dos recursos publicados ou em preparação.

## Regras de interface

- Os botões e links de ação precisam manter contraste legível nos temas claro e escuro.
- As ações principais dos cards utilizam componentes de botão do Bootstrap.
- Categorias inexistentes em `config/resources.php` não devem ser exibidas na navegação.
- A navegação deve continuar acessível em telas pequenas, permitindo quebra de linha sem overflow horizontal.

## Regras de manutenção

- Ler o `README.md` da raiz e `docs/pages/README.md` antes de alterar esta página.
- Preservar o objetivo e os fluxos descritos neste documento.
- Usar primeiro componentes e utilitários do Bootstrap.
- Criar CSS próprio apenas quando necessário e atualizar também o arquivo compilado pelo Vite.
- Manter a página leve, responsiva, acessível e sem dependências desnecessárias.
- Atualizar este arquivo sempre que comportamento, objetivo, conteúdo, estados ou dependências mudarem.

## Validação mínima após alterações

- Conferir renderização nos temas claro e escuro.
- Conferir renderização em telas pequenas e grandes.
- Confirmar que Todos, Guias e Modelos permanecem visíveis e navegáveis.
- Confirmar contraste, foco visível e área clicável dos botões.
- Executar os testes relacionados e o build do Vite quando houver mudança visual ou de JavaScript.
