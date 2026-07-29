# Analytics das Ferramentas — Lote 2

## Escopo concluído

Este lote implementa a instrumentação transversal do navegador, sem ativar nenhuma ferramenta individualmente.

## Capacidades entregues

- Bootstrap automático através da entrada global do Vite.
- Configuração de jornada emitida pelo componente compartilhado de página.
- Ativação exclusiva para módulos que implementam `HasAnalyticsJourney`.
- Suporte a múltiplos formulários explicitamente declarados.
- Captura de início, etapa, campo concluído, validação, envio, execução, resultado, exportação, partilha e abandono.
- Deduplicação local de etapas, campos e códigos de erro.
- Correlação de envio e resultado pela sessão do navegador.
- Contrato de seletores CSS opcionais e convenções `data-analytics-*`.
- Remoção do listener legado que observava indiscriminadamente todos os formulários `POST`.

## Garantias de privacidade

O capturador não cria `FormData`, não lê texto de resultados, não envia valores de inputs e não serializa ficheiros. A única leitura de valor serve localmente para decidir se um campo está preenchido; o conteúdo nunca entra no payload.

## Estado das ferramentas

Nenhum dos 32 módulos foi instrumentado. Assim, a inclusão deste lote não altera métricas de funil até que os pilotos implementem o contrato e os marcadores explícitos.

## Próximo ponto de continuidade

O Lote 3 deve ativar um conjunto pequeno de pilotos representativos e validar o fluxo completo antes da expansão geral.
