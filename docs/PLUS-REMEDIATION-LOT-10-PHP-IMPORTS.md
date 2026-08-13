# Remediação Prazzu Plus — Lote 10 — Correção de imports PHP

## Falha observada

A execução real de `composer release:check` em PHP 8.2 encontrou erro de parse no primeiro dos testes afetados. O namespace do atributo funcional havia sido gravado com duas barras entre seus segmentos:

```php
use App\\Core\\Quality\\Attributes\\CoversPlusFeature;
```

Declarações `use` exigem um único separador de namespace.

## Correção

Os 38 testes afetados passaram a importar corretamente:

```php
use App\Core\Quality\Attributes\CoversPlusFeature;
```

A alteração é estritamente sintática. Todos os atributos `CoversPlusFeature`, seus slugs e suas chaves foram preservados.

## Estado preservado

- ferramentas oficiais: 43;
- benefícios Plus declarados: 137;
- contratos estritos: 137;
- contratos funcionalmente certificados: 137;
- marcadores funcionais únicos: 137;
- dívidas estrutural e funcional: zero.

## Verificação local

Após aplicar este lote, execute:

```powershell
composer release:check
composer e2e:browser:test
powershell -ExecutionPolicy Bypass -File .\scripts\cleanup-project.ps1
powershell -ExecutionPolicy Bypass -File .\scripts\package-distribution.ps1
```
