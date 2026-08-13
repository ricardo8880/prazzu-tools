# Crescimento e Retenção — Lote 5 — Integração, polimento e validação final

## Objetivo

Fechar a frente de crescimento e retenção iniciada nos Lotes 1–4 sem ampliar o produto por conveniência. A base foi reconstruída obrigatoriamente na ordem ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4, seguida de nova leitura do README da raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, inventário executável e relatórios de crescimento anteriores.

O lote concentra regressão conjunta, responsividade, estados vazios, atribuição de Analytics e pequenos ajustes de integridade que só ficaram visíveis depois da integração dos quatro lotes.

## Correção da continuidade autenticada na Home

A Home do Lote 3 usava `repeat_url` como destino de um elemento `<a>`. As rotas `history.repeat` são POST por contrato; portanto, uma ferramenta com repetição disponível poderia receber GET e devolver 405.

O card autenticado agora usa `open_url`, que é sempre uma superfície GET já existente: detalhe do histórico quando houver, índice do histórico como fallback ou a própria ferramenta. O POST de repetição continua restrito aos formulários reais do Meu Prazzu, com CSRF.

Nenhuma rota foi alterada e nenhum contrato de repetição foi inventado.

## Continuidade temporária separada por vertical

A memória de ferramentas recentes do visitante continua em `sessionStorage` e continua armazenando apenas slugs. O Lote 5 passa a usar uma chave por vertical, evitando que abrir a Home de RH apague os atalhos temporários de Contabilidade, ou vice-versa.

A chave antiga `prazzu-recent-tools-session:v1` permanece apenas como origem de migração dentro da sessão atual. O armazenamento novo usa `prazzu-recent-tools-session:v2:{vertical}`. Nenhum cookie, banco, `localStorage`, valor digitado ou resultado é introduzido.

## Medição real de retorno e descoberta

Dois eventos canônicos foram adicionados ao Analytics compartilhado:

- `retention.continuity.used` — uso de atalho da Home autenticada, Home temporária de visitante, Meu Prazzu, favorito ou histórico;
- `retention.related-tool.opened` — abertura de uma ferramenta a partir da jornada editorial de outra ferramenta.

A atribuição acontece no destino e aceita somente origens controladas pela própria interface. Valores arbitrários de `source` são ignorados. Os metadados não contêm payloads, e-mails, documentos ou valores de cálculo.

Para continuidade são guardados apenas `placement`, rota e método. Para ferramenta relacionada são guardados também o slug público de origem (`from_tool`) e a posição editorial.

Dois funis padrão foram adicionados ao painel existente:

- **Retorno por continuidade:** resultado entregue → atalho de continuidade usado → novo resultado entregue;
- **Descoberta por ferramenta relacionada:** resultado entregue → ferramenta relacionada aberta → novo resultado entregue.

Os funis históricos de aquisição, Blog e Plus permanecem intactos.

O CTA pós-resultado para visitante também preserva uma origem controlada ao abrir o cadastro. Se a conta for criada diretamente desse fluxo, o evento `account.created` recebe somente `source=result_continuity` e o slug oficial da ferramenta. E-mail, nome e conteúdo do resultado não entram nos metadados.

## Meu Prazzu e estados vazios

Links de continuidade, favoritos e histórico receberam origem explícita para Analytics. O POST de `Refazer cálculo` mantém CSRF e agora também preserva a atribuição no query string.

Quando a conta já possui histórico mas nenhum favorito, o Meu Prazzu passa a mostrar um estado vazio curto explicando onde a capacidade de favorito é útil, em vez de simplesmente ocultar a seção.

Nenhum payload salvo é renderizado.

## Newsletter

A reativação de uma inscrição cancelada agora também renova `subscribed_at`. O comportamento do Lote 1 já reabria `unsubscribed_at`, mas mantinha a data original da primeira inscrição, o que prejudicava auditoria de reativação.

Submissões repetidas de uma inscrição já ativa continuam idempotentes e não geram novo evento `newsletter.subscribed`.

## Responsividade

Foram feitos ajustes pequenos, sem novo sistema visual:

- cabeçalho da continuidade na Home passa a empilhar antes do breakpoint `sm`;
- cabeçalho de ferramentas relacionadas também empilha em telas estreitas;
- estatísticas do rodapé passam a ocupar uma coluna inteira abaixo de `sm`, evitando quatro textos comprimidos em largura mínima.

O CTA pós-resultado já possuía comportamento responsivo próprio e foi preservado.

## Limites preservados

- README da raiz não foi alterado.
- `CORE_CANDIDATES.md` não exigiu nova promoção: o lote reutiliza Analytics, histórico, catálogo e infraestrutura já compartilhados.
- `config/product_tools.php`, slugs, `release_order`, fórmulas, capacidades Essenciais/Plus e inventário oficial não foram alterados.
- Nenhum cálculo passou a exigir autenticação.
- Nenhuma persistência anônima foi criada além da sessão do navegador já autorizada pelo Lote 3.
- Nenhum CRM, workflow, tarefa ou função de gestão foi adicionado.

## Cobertura adicionada/ajustada

Os testes de crescimento passam a documentar:

- Home autenticada apontando para GET seguro em vez de rota POST de repetição;
- origem `account_continuity` no formulário real de repetição do Meu Prazzu;
- estado vazio de favoritos quando existe histórico;
- evento de continuidade sem payload de cálculo;
- atribuição de ferramenta relacionada somente quando a origem pertence ao catálogo oficial.

O catálogo semântico de Analytics continua exigindo definição para todos os eventos oficiais, portanto os dois novos eventos também entram no gate já existente.

## Validação do lote

A validação final foi executada sobre a base acumulada ZIP original → Lotes 1–4:

- `php artisan tools:check-architecture`: aprovado, sem violações;
- `php artisan analytics:check`: aprovado, catálogo oficial consistente;
- sintaxe PHP de todos os arquivos PHP alterados/adicionados: aprovada com `php -l`;
- `resources/js/app.js`: sintaxe aprovada com `node --check`;
- as views Blade alteradas foram compiladas no gate isolado usado nesta execução, sem erro de compilação;
- o registro executável continua com 43 manifests oficiais;
- PHPUnit direcionado não pôde iniciar porque o PHP do ambiente não possui `dom`, `mbstring` e `xmlwriter`;
- Pint não pôde iniciar porque o ambiente não possui `mbstring` e `xml`;
- o build Vite não pôde ser concluído porque o `node_modules` recebido não contém a dependência opcional nativa `@rollup/rollup-linux-x64-gnu`; a sintaxe JavaScript, porém, foi validada separadamente.

Essas limitações são do ambiente de execução recebido; nenhum erro de arquitetura, catálogo, sintaxe PHP/JavaScript ou compilação Blade foi detectado nos arquivos do lote.

## Continuidade depois do Lote 5

Esta frente de crescimento e retenção fica encerrada como ciclo de cinco lotes. Qualquer lote futuro deve reconstruir ZIP original → Lotes 1–5 na ordem e reler os documentos obrigatórios antes de alterar o produto.

Próximas mudanças devem partir de dados reais dos novos eventos e dos indicadores de visitantes recorrentes/retorno, não de novas funcionalidades especulativas.
