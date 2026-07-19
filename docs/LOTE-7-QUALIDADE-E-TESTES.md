# Lote 7 — Qualidade e testes arquiteturais

> **Registro histórico, não normativo.** Este documento descreve o estado do
> lote em que foi produzido. Para as regras atuais, prevalecem o `README.md` da
> raiz, `app/Tools/README.md` e `docs/ARCHITECTURE.md`. Referências antigas a
> CRM ou compartilhamento de cálculos não autorizam essas capacidades.

Este lote transforma as regras do `README.md` da raiz em verificações
executáveis. A documentação raiz é a autoridade máxima para aceitar ou rejeitar
uma implementação.

## Comandos oficiais

```bash
composer lint
composer format:check
composer architecture
composer test
composer quality
```

`composer quality` é a porta de entrada usada pela integração contínua.

Antes de concluir qualquer lote de ferramenta, também devem ser validados:

```bash
php artisan optimize:clear
php artisan route:list
php artisan test
vendor/bin/pint --test
php artisan view:cache
php artisan view:clear
```

## Regras arquiteturais obrigatórias

As verificações arquiteturais devem proteger, no mínimo:

- presença de `Application`, `Domain`, `Infrastructure`, `Presentation`,
  `Resources`, `Routes`, `Tests`, `README.md` e `Tool.php` em cada módulo;
- correspondência entre caminho, namespace e camada;
- nomes de rota principal e namespaces de views únicos;
- cada módulo expõe rota web ou API por arquivo próprio;
- endpoints de ferramenta apontam para controllers, sem handlers em closures;
- uma ferramenta não depende da implementação interna de outra;
- classes de `Domain` não dependem do Laravel, configuração, ambiente ou hora
  atual implícita;
- `Domain` não utiliza `float` para dinheiro ou percentuais;
- regras de negócio permanecem no `Domain`, não em Actions ou controllers;
- controllers seguem `Request -> Action -> Response` e não consultam banco ou
  APIs diretamente;
- controllers não geram PDF, CSV, XLSX ou impressão;
- histórico, exportação, compartilhamento, favoritos, analytics, auditoria,
  autenticação e limites utilizam contratos do Core;
- rotas, views, migrations e assets específicos permanecem dentro do módulo;
- README da ferramenta contém as cinco seções obrigatórias;
- stubs e marcadores necessários ao gerador continuam presentes.

A análise arquitetural complementa, mas não substitui, testes de negócio e
validação manual.

## Testes obrigatórios por ferramenta

Toda ferramenta possui:

- testes Unit para regras, cálculos, arredondamentos e casos de fronteira;
- testes Feature para páginas, requests, rotas e respostas de cada caso de uso;
- Browser Tests quando a interface possuir formulários ou interações complexas.

Os testes de rotas devem confirmar que o controller e o método de destino
existem, evitando rotas compiláveis que falhem somente durante a requisição.

## Casos de referência obrigatórios

Cada ferramenta contábil deve incluir fixtures ou data providers com:

- entrada completa;
- resultado esperado;
- data de referência;
- versão da regra;
- fonte normativa;
- casos de fronteira e arredondamento.

Os testes devem falhar quando uma atualização de regra alterar um resultado
histórico sem que a versão da regra também seja alterada.

## Validação funcional

Além dos testes automatizados, deve ser verificado manualmente:

- digitação e máscaras de todos os inputs;
- funcionamento de selects, limpar e calcular;
- mensagens e erros de validação;
- correção e transparência do resultado;
- exportação, impressão e histórico quando oferecidos;
- comportamento mobile e desktop;
- ausência de erros JavaScript e Laravel.

## Integração contínua

O workflow `.github/workflows/quality.yml` executa em pushes e pull requests:

1. instalação reproduzível com `composer install` e `npm ci`;
2. lint de PHP;
3. Laravel Pint em modo de verificação;
4. verificação arquitetural;
5. PHPUnit;
6. build do Vite;
7. caches de configuração, rotas e views.

A pipeline instala explicitamente as extensões PHP necessárias aos testes e ao
console do Laravel.

## Política para novas regras

Uma nova regra arquitetural deve ser adicionada quando:

- protege um limite já decidido pelo README raiz;
- produz mensagem objetiva e acionável;
- pode ser executada localmente e na CI;
- impede que uma ferramenta desloque responsabilidade para a camada errada;
- mantém obrigatória a estrutura oficial, ainda que alguma pasta não possua
  implementação concreta no primeiro lote.
