# Checklist de release

> O Lote 10 validou o que era executável localmente. A aprovação final exige o CI oficial com todas as extensões PHP requeridas. Não marque itens bloqueados como aprovados sem execução real.

## Código

- [ ] `composer release:check` aprovado.
- [ ] `scripts/finalize-quality.ps1` aplicado quando o Pint indicar dívida de estilo.

- [ ] `composer format:check` aprovado.
- [ ] `composer architecture` aprovado.
- [ ] `composer test` aprovado.
- [ ] `composer quality` aprovado.
- [ ] `npm run build` aprovado.
- [ ] `composer e2e:browser:test` aprovado com Chromium instalado por `npm run e2e:install`.

## Laravel

- [ ] `php artisan config:cache` aprovado.
- [ ] `php artisan route:cache` aprovado.
- [ ] `php artisan view:cache` aprovado.
- [ ] `php artisan migrate:status` sem migrations pendentes inesperadas.
- [ ] `php artisan tools:check-architecture` sem violações.

## Ferramentas

- [ ] Manifestos válidos e sem slugs duplicados.
- [ ] Versões de ferramenta e regra atualizadas quando necessário.
- [ ] Casos de referência testados.
- [ ] Política de histórico revisada.
- [ ] Dados sensíveis declarados e protegidos.
- [ ] Permissões, limites e métricas revisados.

## Distribuição

- [ ] `.env` não está no pacote.
- [ ] `.env` não está versionado (`git ls-files .env` não retorna resultado).
- [ ] `.git`, `.idea`, `vendor`, `node_modules` e `backup` não estão no pacote.
- [ ] Dumps SQL e backups locais não estão no pacote nem versionados.
- [ ] Não existe outra raiz Laravel aninhada no pacote.
- [ ] Banco SQLite local não está no pacote.
- [ ] Logs locais não estão no pacote.
- [ ] README e documentação correspondem ao estado atual.
- [ ] `scripts/verify-distribution.php` aprovou o diretório empacotado.
- [ ] O pacote foi gerado por `scripts/package-distribution.ps1`, nunca compactando a pasta de trabalho diretamente.
- [ ] Nenhum arquivo temporário `~$*`, `.DS_Store` ou `Thumbs.db` está no pacote.
