# Lote 9 — acesso, autenticação e fluxos transversais

## Escopo entregue

- Perfis determinísticos de usuário gratuito, Plus e administrador continuam sendo criados pelo `E2EEnvironmentSeeder`.
- O ambiente E2E passou a usar sessões persistentes em arquivos dentro de `storage/app/e2e/sessions`, sem compartilhar sessões com desenvolvimento ou produção.
- O manifesto `storage/app/e2e/runtime/access-profiles.json` centraliza credenciais E2E e caminhos protegidos usados pelo runner.
- O projeto Playwright `auth-setup` autentica cada perfil uma vez e grava estados reutilizáveis em `storage/app/e2e/runtime/auth`.
- A suíte `access-transversal` cobre visitante, gratuito, Plus e administrador, incluindo conta, histórico, bloqueio administrativo e rejeição de POST sem CSRF.
- O acesso administrativo é conferido tanto na interface quanto na resposta HTTP; usuários comuns recebem `403`.

## Comandos

```bash
composer e2e:prepare
php scripts/e2e-access.php export
composer e2e:browser:access
```

## Critério de aceite

Recursos protegidos devem falhar ou funcionar conforme o perfil declarado, e as sessões autenticadas devem ser reutilizáveis sem login repetido em cada cenário.
