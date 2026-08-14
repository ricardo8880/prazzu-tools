# Experiência do Usuário — Lote 5 — fechamento das superfícies de ferramenta

## Objetivo

Executar o primeiro lote posterior à rodada UX 1–4, limitado aos três ajustes identificados na auditoria do projeto consolidado:

1. impedir que “Próximos passos” seja apresentado antes de existir um resultado;
2. alinhar a copy de “Continue de onde parou” ao trabalho salvo que a Home realmente mostra;
3. revisar as 11 ferramentas com resultado customizado e fazê-las cumprir o mesmo contrato de superfície de resultado sem eliminar layouts específicos do domínio.

## Estado de origem

Este lote parte diretamente do ZIP consolidado enviado após os UX Lotes 1, 2, 3 e 4.

Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `config/product_tools.php` e `docs/UX-EXPERIENCE-LOT-1.md` até `docs/UX-EXPERIENCE-LOT-4.md`.

## Mudanças

- O bloco compartilhado de “Próximos passos” nasce oculto e só é revelado no navegador quando a página contém um resultado significativo já entregue.
- A regra reutiliza os mesmos marcadores de resultado já usados pela continuidade pós-resultado e não cria novo evento de Analytics.
- A Home autenticada passou de “atalhos para ferramentas usadas recentemente” para “retome seus últimos trabalhos e resultados salvos”, refletindo o conteúdo real dos cards.
- As 11 ferramentas com resultados customizados receberam o contrato `data-tool-result-panel` na superfície principal do resultado:
  - `AccountingFeesCalculator`;
  - `BusinessDocumentValidator`;
  - `FederalPaymentGuideGenerator`;
  - `LaborTerminationCalculator`;
  - `MarginMarkupCalculator`;
  - `ProLaboreSimulator`;
  - `ProfitDistributionCalculator`;
  - `ReceiptIssuer`;
  - `SimplesNacionalCalculator`;
  - `TaxRegimeComparator`;
  - `VacationCalculator`.
- Resultados especiais (documento, guia, validação, comparação e cálculos com layouts próprios) continuam com suas estruturas específicas. O lote padroniza o contrato transversal, não força um único HTML para domínios diferentes.
- O teste arquitetural de experiência passou a proteger tanto a ocultação pré-resultado dos próximos passos quanto o contrato compartilhado das 11 exceções.

## Limites preservados

- Nenhuma fórmula, serviço de cálculo, request, controller, rota, slug, vertical, inventário, `release_order` ou regra normativa foi alterado.
- Nenhum payload novo é exposto na Home ou nas superfícies de resultado.
- Nenhum evento ou armazenamento de Analytics foi criado.
- Os fluxos Plus, histórico, favoritos, busca, aquisição e retenção dos lotes anteriores foram preservados.
- `CORE_CANDIDATES.md` não foi alterado: este lote materializa um contrato visual já existente e não cria nova responsabilidade transversal.

## Continuidade obrigatória

Qualquer lote posterior deve usar como fonte de verdade o projeto consolidado que já contém os UX Lotes 1–5, reler novamente o README, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `config/product_tools.php` e todos os relatórios `docs/UX-EXPERIENCE-LOT-*.md` antes de alterar o projeto.

## Validação executada

- `php artisan tools:check-architecture`: aprovado, sem violações;
- `php artisan analytics:check`: aprovado;
- `node --check resources/js/app.js`: aprovado;
- `php -l tests/Architecture/ToolValueFirstExperienceTest.php`: aprovado;
- verificação estática confirmou `data-tool-result-panel` nas 11 exceções revisadas;
- comparação por checksum contra o ZIP consolidado enviado pelo usuário confirmou somente 16 arquivos pertencentes a este lote;
- PHPUnit não iniciou porque o PHP do ambiente não possui `dom`, `mbstring` e `xmlwriter`;
- compilação Blade integral também ficou bloqueada pelas mesmas extensões (`DOMDocument`/`mbstring`), sem qualquer alteração nas dependências para contornar o ambiente.
