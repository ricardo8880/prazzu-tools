# Experiência do Usuário — Lote 7 — higiene, documentação e distribuição

## Objetivo

Encerrar a rodada posterior aos UX Lotes 1–4 sem reabrir UX ou Analytics, corrigindo somente inconsistências de documentação e riscos de distribuição detectados na auditoria do projeto consolidado.

## Estado de origem

Este lote parte do projeto atual enviado pelo usuário, com os UX Lotes 5 e 6 reaplicados antes de qualquer alteração.

Antes das mudanças foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `docs/PRODUCT-TOOLS-INVENTORY.md`, `docs/RELEASE-CHECKLIST.md`, `docs/LOTE-10-LIMPEZA-E-DISTRIBUICAO.md` e os relatórios `docs/UX-EXPERIENCE-LOT-1.md` até `docs/UX-EXPERIENCE-LOT-6.md`.

## Mudanças

- O README volta a refletir a fonte de verdade executável: **50 ferramentas oficiais**, sendo 49 em `contabilidade` e 1 em `rh`.
- Referências históricas a 32/33 ferramentas continuam documentadas como cronologia, mas deixam de ser apresentadas como estado vigente.
- `.gitignore` passou a proteger `backup/` e resíduos temporários `~$*`, `.DS_Store` e `Thumbs.db`.
- `.gitattributes` passou a excluir `backup/` também de `git archive`.
- `scripts/package-distribution.ps1` passou a excluir `backup/` do staging oficial.
- `scripts/verify-distribution.php` passou a rejeitar `backup/` em qualquer profundidade do pacote.
- `scripts/cleanup-project.ps1` passou a remover `node_modules` e `backup` do índice Git com `--cached`, preservando os arquivos locais, e a apagar resíduos temporários de Office/sistema.
- O checklist e a documentação oficial de distribuição agora exigem ausência de backups/dumps no pacote.

## Segurança e distribuição

O ZIP bruto recebido continha `.env`, `.git`, `node_modules`, `vendor`, dumps SQL em `backup/` e um arquivo temporário do Office versionado (`tests/~$ia_Comandos_Automacao_Prazzu_Tools.docx`). O projeto já possuía um empacotador oficial capaz de excluir a maior parte desses resíduos, porém `backup/` ainda não fazia parte do bloqueio. Este lote fecha essa lacuna.

O pacote de distribuição deve continuar sendo produzido exclusivamente por:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\package-distribution.ps1
```

Compactar manualmente a raiz do projeto não é um fluxo de release suportado.

## Limites preservados

- Nenhuma fórmula, ferramenta, rota, slug, vertical, view, CSS, JavaScript de produto, Analytics ou regra de negócio foi alterada.
- Nenhuma dependência foi adicionada ou removida.
- Os dumps locais não são apagados automaticamente do disco; a limpeza de Git usa somente `--cached`.
- `CORE_CANDIDATES.md` não foi alterado porque o lote não cria capacidade transversal nova.

## Continuidade obrigatória

Qualquer lote futuro deve partir do projeto consolidado contendo os UX Lotes 1–7 e reler novamente o README, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `config/product_tools.php` e todos os relatórios `docs/UX-EXPERIENCE-LOT-*.md`.

## Validação executada

- `php -l scripts/verify-distribution.php`: aprovado;
- `php artisan tools:check-architecture`: aprovado, sem violações;
- `php artisan analytics:check`: aprovado;
- `node --check resources/js/app.js`: aprovado;
- o verificador rejeitou corretamente a raiz bruta por conter `.env`, `.git`, `node_modules`, `vendor`, `backup/` e resíduos locais;
- um staging equivalente ao empacotador oficial, sem resíduos locais, foi aprovado por `scripts/verify-distribution.php`;
- PowerShell (`pwsh`/`powershell`) não está instalado no ambiente de análise, então o wrapper `package-distribution.ps1` não pôde ser executado diretamente; sua política de exclusão foi validada por inspeção e staging equivalente;
- comparação por conteúdo contra o estado consolidado **projeto atual + UX Lote 5 + UX Lote 6** confirmou que somente os arquivos deste lote foram alterados.
