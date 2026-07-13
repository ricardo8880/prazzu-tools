# Arquitetura da Prazzu Tools

## Regra central

A plataforma possui um núcleo compartilhado e ferramentas isoladas por módulo.
Uma ferramenta pode depender do núcleo, mas nunca da implementação interna de
outra ferramenta.

## Fonte única do catálogo

`App\Core\Tools\ToolCatalog` é a única fonte usada pela home, catálogo, busca,
filtros, contadores, páginas individuais, ferramentas relacionadas e painéis
laterais.

As ferramentas ainda não implementadas ficam provisoriamente em
`config/tools.php`. Quando um módulo real registra o mesmo `slug`, a definição do
módulo substitui a provisória automaticamente. Isso permite implementar uma
ferramenta sem alterar as páginas globais.

## Front-end

- Bootstrap e Bootstrap Icons são carregados por CDN.
- Existe apenas uma folha global: `public/assets/css/style.css`.
- O Bootstrap resolve primeiro grid, espaçamento, responsividade e componentes.
- O JavaScript global é servido diretamente por `public/assets/js/app.js`, sem
  depender do manifesto do Vite.
- JavaScript específico deve ficar em `public/assets/js/tools/<slug>.js`, ser
  carregado apenas pela ferramenta e atuar somente dentro de `[data-tool]`.

## Registro de módulos

Cada ferramenta implementa `ToolModule` e é registrada explicitamente em
`config/tools.php`, na chave `modules`. O `ToolRegistry` valida slugs duplicados e
disponibiliza os metadados ao `ToolCatalog`.

## Rotas

Cada módulo informa seu próprio arquivo de rotas. `routes/tools.php` é carregado
antes da rota provisória `tools.show`, permitindo que a rota real de uma
ferramenta tenha prioridade sobre o placeholder da plataforma.

## Checklist de uma nova ferramenta

1. Criar `app/Tools/<NomeDaFerramenta>`.
2. Implementar `ToolModule`.
3. Criar controller, request e serviço somente quando necessários.
4. Criar views em `resources/views/tools/<slug>`.
5. Criar JavaScript isolado apenas quando houver comportamento específico.
6. Criar o arquivo de rotas do módulo.
7. Registrar a classe em `config/tools.php`.
8. Criar testes unitários e de funcionalidade.
9. Remover a entrada provisória do catálogo, opcionalmente; o módulo já a
   substitui pelo mesmo slug.

## Crescimento

Não criar pastas vazias ou abstrações sem uso. Cada módulo começa pequeno e cresce
conforme a necessidade. Recursos compartilhados só entram no Core depois de serem
claramente reutilizáveis.
