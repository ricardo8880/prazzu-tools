# Lote 8 — Ferramenta-piloto

Foi implementada a Calculadora de Margem e Markup para validar a arquitetura consolidada nos Lotes 1 a 7.

## Capacidades validadas

- manifesto e registro explícito;
- rotas e views isoladas;
- domínio sem Laravel e sem `float`;
- valores monetários e percentuais tipados;
- regra com versão `1.0.0`;
- data de referência explícita;
- histórico com lista permitida e retenção de 90 dias;
- autorização e limite de uso centrais;
- métrica desacoplada do histórico;
- exportação CSV;
- testes unitário e funcional.

## Decisões após a validação

A estrutura modular mostrou-se adequada para repetição. O Core não precisou receber abstrações específicas de margem ou markup. Novas ferramentas devem copiar o fluxo arquitetural, não o domínio desta ferramenta.

O layout global e os arquivos visuais existentes não foram alterados.
