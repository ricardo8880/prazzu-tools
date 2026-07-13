# Lote 5 — Versionamento, auditoria e histórico

Este lote estabelece a infraestrutura para reproduzir resultados sem obrigar todas as ferramentas a persistirem dados.

## Princípios

1. Histórico é opt-in: uma ferramenta só persiste execuções se implementar `HasHistoryPolicy`.
2. Campos são autorizados por lista explícita; não existe persistência automática de todo o formulário.
3. Payloads e metadados são criptografados pelo cast `encrypted:array` do Laravel.
4. Toda execução registra versão da ferramenta, versão da regra e data de referência.
5. Auditoria registra ações e identificadores, sem copiar entrada ou resultado do cálculo.
6. Cada política define retenção, e históricos expirados podem ser removidos por comando.

## Habilitando histórico em uma ferramenta

O manifesto deve declarar:

```php
supportsHistory: true,
storesSensitiveData: true,
```

O módulo implementa:

```php
HasHistoryPolicy
```

E fornece uma política:

```php
return new ToolHistoryPolicy(
    enabled: true,
    retentionDays: 90,
    inputFields: ['reference_date', 'employee.salary', 'employee.cpf'],
    resultFields: ['gross_total', 'net_total'],
    sensitiveFields: ['employee.cpf'],
);
```

Os campos de entrada e resultado são allowlists. Campos omitidos nunca são persistidos.

## Ciclo da execução

```php
$run = $recorder->start(
    module: $module,
    ruleVersion: new RuleVersion('1.2.0'),
    referenceDate: ReferenceDate::fromString('2026-07-13'),
    input: $validatedInput,
    userId: auth()->id(),
);

$recorder->succeed($run, $result, $normativeReferences);
```

Em erro controlado:

```php
$recorder->fail($run, 'calculation.invalid_period');
```

Uma execução só pode sair de `running` para `succeeded` ou `failed` uma vez.

## Retenção

Agendar diariamente:

```bash
php artisan tools:purge-history
```

O comando remove apenas registros cujo `expires_at` já venceu.

## Tabelas

- `tool_runs`: histórico técnico e reproduzível das execuções autorizadas.
- `audit_logs`: trilha imutável de ações relevantes.

As tabelas globais não substituem tabelas específicas de ferramentas que possuam domínio persistente próprio.

## Proibições

- Não gravar o request inteiro.
- Não registrar senhas, tokens, arquivos ou segredos.
- Não usar a data da execução como substituta da data de referência.
- Não sobrescrever versões antigas de regras necessárias para reprodução.
- Não colocar payloads completos nos logs de auditoria.
