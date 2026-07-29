# Analytics das Ferramentas — Lote 1

## Escopo concluído

Este lote cria somente a fundação transversal do funil detalhado das ferramentas. Nenhuma das 32 ferramentas recebeu instrumentação específica.

## Capacidades entregues

- Eventos canónicos para a jornada completa.
- Contrato v1 do endpoint `POST /analytics/tools`.
- Versionamento por evento.
- Allowlist e validação estrita de metadados.
- Compatibilidade com os eventos públicos anteriores.
- Contrato opcional para módulos declararem formulários, etapas, campos e ações.
- Registry central para descoberta das jornadas declaradas.
- Testes de contrato, privacidade e consistência estrutural.

## Regras de privacidade

A telemetria aceita apenas identificadores semânticos e métricas agregadas. É proibido enviar valores de campos, documentos, resultados, nomes, e-mails, CPF, CNPJ ou qualquer dado que permita reconstruir o conteúdo informado pelo utilizador.

## Próximo ponto de continuidade

O próximo lote deve implementar um capturador frontend compartilhado. Ele deverá operar somente em formulários explicitamente declarados, evitar duplicidade, tratar múltiplos formulários por página e produzir os eventos definidos neste lote.
