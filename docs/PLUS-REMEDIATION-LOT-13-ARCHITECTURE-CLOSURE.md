# Remediação Plus — Lote 13: fechamento arquitetural

## Objetivo

Encerrar, em um único lote, as 82 violações restantes reportadas por `composer release:check` depois da aprovação integral do E2E.

## Ajustes

- documentação obrigatória completada em cinco módulos;
- comparação monetária com `float` removida da Calculadora de ISS;
- persistência do cadastro de ativos retirada do controller e isolada em repositório de infraestrutura;
- gate `export` explicitado na Calculadora de Parcelamento Tributário;
- inspetor Plus atualizado para reconhecer o marcador comportamental exato `CoversPlusFeature` como evidência auditável, mantendo a verificação independente de gate e de certificação funcional.

## Validação esperada no ambiente oficial

```powershell
powershell -ExecutionPolicy Bypass -File scripts/finalize-quality.ps1
```

O comando deve concluir Pint, lint, `composer release:check`, E2E e empacotamento sem as 82 violações anteriores.
