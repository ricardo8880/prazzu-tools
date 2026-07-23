# Planejamento de férias

- **Tipo:** Página de ferramenta
- **Implementação principal:** `app/Tools/VacationCalculator/Resources/views/planner.blade.php`
- **Status:** ativa no código atual

## Objetivo

Oferecer a interface e o fluxo funcional de **Planejamento de férias**, respeitando as regras de domínio do módulo ao qual pertence.

## Como funciona

A página é renderizada pela view Blade indicada acima e utiliza o layout, os componentes compartilhados e os dados fornecidos pelo controller ou fluxo responsável. A implementação atual contém 1 formulário(s), 1 tabela(s), comportamento JavaScript associado.

## Estrutura e conteúdos identificados

- Planejamento de férias

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
