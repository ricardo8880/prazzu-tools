# Lote 12 — exploração, governança e manutenção contínua

## Continuidade analisada

Este lote parte do projeto-base com os Lotes 9, 10 e 11 aplicados. Preserva sessões por perfil, cobertura mínima das 32 ferramentas, validação de downloads, diagnóstico correlacionado, sharding e comparação de regressões.

## Entregas

- Exploração controlada com semente reproduzível e limite de ações, executada fora do gate bloqueante.
- Bateria completa em Chromium, Firefox e WebKit.
- Subconjunto responsivo para ferramentas críticas e de alto risco em celular e tablet.
- Regra arquitetural que rejeita ferramenta oficial sem cenários válido e inválido.
- Painel JSON e HTML com cobertura, falhas, flakiness, ignorados e duração.
- Limites mensuráveis de saúde da suíte.
- Política automática de retenção para artefatos e relatórios executivos.

## Operação

- `composer e2e:governance:check`: valida catálogo e contratos obrigatórios.
- `composer e2e:browser:complete`: executa a bateria determinística completa.
- `composer e2e:browser:exploratory`: executa exploração controlada, sem bloquear regressão.
- `php scripts/e2e-governance.php dashboard <resumo.json>`: gera o painel de saúde.
- `composer e2e:retention`: remove artefatos vencidos.

## Critério de aceite

Ferramentas novas passam a exigir contrato E2E no catálogo, a bateria completa mede múltiplos navegadores e responsividade, e a saúde da suíte pode ser acompanhada por cobertura, flakiness e duração.
