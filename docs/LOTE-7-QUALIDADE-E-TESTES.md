# Lote 7 — Qualidade e testes arquiteturais

Este lote transforma as convenções dos lotes anteriores em verificações executáveis. Ele não altera layout, regras visuais ou comportamento das páginas públicas.

## Comandos oficiais

```bash
composer lint
composer format:check
composer architecture
composer test
composer quality
```

`composer quality` é a porta de entrada usada pela integração contínua.

## Regras arquiteturais verificadas

- nomes de rota principal únicos;
- namespaces de views únicos;
- cada módulo expõe rota web ou API;
- um módulo não depende diretamente da implementação de outro módulo;
- classes de `Domain` não dependem do Laravel;
- classes de `Domain` não consultam a hora atual implicitamente;
- classes de `Domain` não utilizam `float` para cálculos financeiros;
- controllers não consultam banco ou APIs diretamente;
- arquivos de rota não usam closures;
- stubs e marcadores necessários ao gerador continuam presentes.

A análise é deliberadamente focada nas decisões arquiteturais do projeto e não substitui testes de negócio.

## Casos de referência obrigatórios

Cada ferramenta contábil deve incluir fixtures ou data providers com:

- entrada completa;
- resultado esperado;
- data de referência;
- versão da regra;
- fonte normativa;
- casos de fronteira e arredondamento.

Os testes devem falhar quando uma atualização de regra alterar um resultado histórico sem que a versão da regra também seja alterada.

## Integração contínua

O workflow `.github/workflows/quality.yml` executa em pushes e pull requests:

1. instalação reproduzível com `composer install` e `npm ci`;
2. lint de PHP;
3. Laravel Pint em modo de verificação;
4. verificação arquitetural;
5. PHPUnit;
6. build do Vite;
7. caches de configuração, rotas e views.

A pipeline instala explicitamente as extensões PHP necessárias aos testes e ao console do Laravel.

## Política para novas regras

Uma nova regra arquitetural só deve ser adicionada quando:

- protege um limite já decidido pelo projeto;
- produz mensagem objetiva e acionável;
- pode ser executada localmente e na CI;
- não obriga todas as ferramentas a criar camadas desnecessárias.
