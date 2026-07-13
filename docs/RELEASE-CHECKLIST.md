# Checklist de release

## Código

- [ ] `composer format:check` aprovado.
- [ ] `composer architecture` aprovado.
- [ ] `composer test` aprovado.
- [ ] `composer quality` aprovado.
- [ ] `npm run build` aprovado.

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
- [ ] `.git`, `.idea`, `vendor` e `node_modules` não estão no pacote.
- [ ] Banco SQLite local não está no pacote.
- [ ] Logs locais não estão no pacote.
- [ ] README e documentação correspondem ao estado atual.
