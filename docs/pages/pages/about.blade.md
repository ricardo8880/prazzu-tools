# Sobre o Prazzu Tools — Plataforma de ferramentas contábeis

- **Tipo:** Página da plataforma
- **Implementação principal:** `resources/views/pages/about.blade.php`
- **Status:** ativa no código atual

## Objetivo

Apresentar e permitir a utilização da área **Sobre o Prazzu Tools — Plataforma de ferramentas contábeis** dentro da plataforma.

## Como funciona

A página é renderizada pela view Blade indicada acima e utiliza o layout, os componentes compartilhados e os dados fornecidos pelo controller ou fluxo responsável. A implementação atual contém conteúdo e navegação renderizados pelo servidor.

## Estrutura e conteúdos identificados

- Ferramentas contábeis que resolvem de verdade
- A rotina contábil não deveria depender de soluções espalhadas
- Cálculos
- Simulações
- Conteúdo
- Produtividade
- Cada ferramenta evolui sozinha. A plataforma evolui para todas.
- Plataforma compartilhada

## Regras de manutenção

- Ler o `README.md` da raiz e `docs/pages/README.md` antes de alterar esta página.
- Preservar o objetivo e os fluxos descritos neste documento.
- Usar primeiro componentes e utilitários do Bootstrap.
- Criar CSS próprio apenas quando necessário e validar sua inclusão no build do Vite.
- Manter a página leve, responsiva, acessível e sem dependências desnecessárias.
- Atualizar este arquivo sempre que comportamento, objetivo, conteúdo, estados ou dependências mudarem.

## Validação mínima após alterações

- Conferir renderização em telas pequenas e grandes.
- Validar estados vazio, carregando, sucesso, erro e permissão quando aplicáveis.
- Confirmar navegação, formulários e ações disponíveis.
- Executar os testes relacionados e o build do Vite quando houver mudança visual ou de JavaScript.
