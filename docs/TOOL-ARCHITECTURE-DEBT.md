# Dívida arquitetural das ferramentas existentes

Atualizado em 18/07/2026. O `README.md` da raiz continua sendo a autoridade
máxima; este documento apenas registra divergências encontradas no código
legado para que elas não sejam tratadas como precedente.

## Regra para novas ferramentas

Novos módulos devem partir de `php artisan make:tool`, iniciar em `draft` e
seguir `app/Tools/README.md`. Implementações legadas listadas abaixo não podem
ser copiadas. O comando `php artisan tools:check-architecture` valida estrutura,
camadas de recursos, namespaces, documentação, migrations, rotas, controllers e
dependências básicas.

## Prioridade 0 — Core compartilhado

1. **Histórico:** Accounting e Simples ainda persistem históricos em tabelas
   próprias. Business Document, Labor e Margin gravam pelo Core, mas parte da
   leitura e exclusão ainda depende do model Eloquent `ToolRun`. É necessário
   completar um contrato central de consulta, posse, exclusão e auditoria,
   migrar os dados locais e aposentar os armazenamentos paralelos.
2. **Favoritos:** Accounting mantém favorito na tabela própria de cálculo. O
   Core precisa de um gerenciador de favoritos vinculado à execução central.
3. **Compartilhamento:** Accounting e Margin possuem implementações próprias.
   Além da duplicação, o README raiz proíbe compartilhamento de cálculos. A
   remoção de rotas e dados exige decisão explícita de produto e migration.
4. **Acesso por capacidade:** o plano efetivo e a política de lançamento agora
   são resolvidos pelo Core. Falta mover limites por slug/capacidade para uma
   política central, retirando números dos controllers legados.

## Prioridade 1 — domínio e apresentação

1. Projeções e alertas do Simples ainda usam `float` para dinheiro e percentual
   e mantêm regra em Actions. Devem migrar para `Money`, `Percentage` e serviços
   puros de domínio.
2. Há impressão direta por `window.print()` em views antigas. Toda impressão e
   exportação deve passar pelos serviços do Core e por componentes Blade
   compartilhados.
3. O projeto ainda precisa dos componentes visuais comuns `tool-intro`,
   `form-panel`, `validation-summary`, `result-panel`, `history-actions` e
   `export-button`; até lá, HTML de módulos existentes não é um padrão para
   cópia.
4. O cadastro comercial legado do Accounting é CRM/gestão de clientes e está
   fora do produto definido no README raiz. Não pode receber evolução dentro do
   Prazzu Tools.

## Critério de encerramento

Uma dívida só sai deste registro quando código, dados, documentação e testes
estiverem migrados; o gate arquitetural correspondente deve ser adicionado no
mesmo lote para impedir regressão.
