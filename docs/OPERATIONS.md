# Operação, segurança e recuperação

Este documento complementa o README da raiz, que permanece soberano.

## Segurança HTTP

Os cabeçalhos globais são aplicados por `ApplySecurityHeaders`. Ferramentas não devem definir políticas próprias. A CSP pode ser ajustada por `SECURITY_CONTENT_POLICY` quando uma integração aprovada exigir origem adicional.

## Logs

Contextos de log passam por `SensitiveDataProcessor`. Senhas, tokens, documentos fiscais e dados remuneratórios conhecidos são substituídos por `[REDACTED]`. Novos campos sensíveis devem ser adicionados ao catálogo central em `config/operations.php`; não devem ser mascarados isoladamente em ferramentas.

## Retenção

Os valores de retenção em `config/operations.php` são o contrato operacional mínimo. Rotinas de limpeza devem consumir essa configuração. Dados temporários não devem ser promovidos a históricos permanentes sem consentimento e finalidade explícita.

## Backup e recuperação

Produção deve possuir backup criptografado, diário e com restauração testada. O padrão inicial é RPO de 24 horas e RTO de 4 horas. Uma restauração completa deve ser ensaiada periodicamente e registrada fora do ambiente restaurado.

## Observabilidade

Monitorar no mínimo: exceções, latência por ferramenta, falhas de filas, integrações externas, disponibilidade de `/up` e versão normativa usada em cálculos regulados. Analytics de produto não substitui monitoramento técnico.

## Incidentes

1. Conter o impacto e preservar evidências.
2. Identificar ferramentas, usuários e versões normativas afetadas.
3. Corrigir sem reescrever silenciosamente históricos.
4. Comunicar impacto e orientação aplicável.
5. Registrar causa, correção, testes de regressão e ação preventiva.
