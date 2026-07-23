# Listagem de recursos por categoria

- **Tipo:** Página dinâmica da plataforma
- **Implementação principal:** `resources/views/pages/resources/listing.blade.php`
- **Status:** ativa no código atual

## Objetivo

Listar os recursos pertencentes a uma categoria válida da Central de Recursos, atualmente Guias práticos ou Modelos profissionais.

## Como funciona

A página recebe do `ContentPageController` a categoria atual, seus metadados e a coleção de itens correspondentes. O cabeçalho e o estado vazio são montados com os dados de `config/resources.php`.

A navegação de categorias é exibida permanentemente abaixo do breadcrumb. Ela utiliza `nav-pills` do Bootstrap e mantém a categoria atual destacada mesmo depois que o usuário acessa Guias ou Modelos.

Quando existem itens, cada recurso é apresentado pelo componente `resources.card`. Quando não existem, a página apresenta um estado vazio específico da categoria.

## Estrutura e conteúdos identificados

- Breadcrumb para retorno à Central de Recursos;
- Navegação persistente entre Todos, Guias e Modelos;
- Hero dinâmico da categoria;
- Grade responsiva de cards;
- Estado vazio da categoria.

## Regras de interface

- A categoria atual deve usar classe `active` e `aria-current="page"`.
- Os botões dos cards devem ser legíveis nos temas claro e escuro.
- Ações principais devem utilizar componentes Bootstrap antes de CSS personalizado.
- Categorias não configuradas não devem aparecer na navegação.
- A navegação deve quebrar adequadamente em dispositivos móveis.

## Regras de manutenção

- Ler o `README.md` da raiz e `docs/pages/README.md` antes de alterar esta página.
- Preservar o objetivo e os fluxos descritos neste documento.
- Usar primeiro componentes e utilitários do Bootstrap.
- Criar CSS próprio apenas quando necessário e atualizar também o arquivo compilado pelo Vite.
- Manter a página leve, responsiva, acessível e sem dependências desnecessárias.
- Atualizar este arquivo sempre que comportamento, objetivo, conteúdo, estados ou dependências mudarem.

## Validação mínima após alterações

- Conferir Guias e Modelos nos temas claro e escuro.
- Conferir estados com conteúdo e sem conteúdo.
- Confirmar navegação entre Todos, Guias e Modelos.
- Confirmar contraste, foco visível e área clicável dos botões.
- Executar os testes relacionados e o build do Vite quando houver mudança visual ou de JavaScript.
