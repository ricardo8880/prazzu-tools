# Remediação Prazzu Plus — Lote 6 — Encerramento do legado estrutural

## Reconstrução

O projeto foi reconstruído do ZIP original e recebeu, em ordem, as três correções direcionadas e os Lotes de Remediação 1–5. README, candidatos ao Core, inventário executável, governança e relatórios acumulados foram relidos antes da seleção do escopo.

## Contratos certificados

- `calculadora-das-retroativo-regularizacao-simples`: `multiple_competencies` e `report`.
- `calculadora-depreciacao-ativos`: `multiple_assets`, `methods` e `export`.
- `calculadora-ferias`: `history`, `vacation_planning` e `professional_export`.
- `calculadora-iss`: `retention`, `multiple_services`, `municipality_scenarios` e `export`.
- `calculadora-parcelamento-tributario`: `scenario_comparison` e `export`.
- `calculadora-retencoes-nota-fiscal`: `custom_rules`, `multiple_notes` e `export`.

## Evidências funcionais

Os testes executam resultados observáveis: dívida consolidada por competência; carteira de ativos com método acelerado; abertura do histórico, planejamento de equipe e formatos profissionais de férias; retenção, múltiplos serviços e cenários municipais no ISS; comparação e cronograma de parcelamento; bases configuráveis e agregação de notas; e ações reais de PDF/XLSX nas respectivas páginas.

Cada chave possui autorização central em `ToolFeatureRequestAuthorizer` ou middleware `tool.feature`, além do contrato comercial Free × Plus em modo monetizado. Nenhuma fórmula Essencial passou a depender do Plus.

## Estado acumulado

- catálogo declarado: 137 contratos Plus;
- contratos estritos: 120 → 137;
- dívida legada estrutural: 17 → 0;
- checksum da dívida legada vazia: `e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855`;
- contratos funcionalmente certificados: 59 → 76;
- dívida funcional: 78 → 61.

## Arquitetura

Nenhuma infraestrutura paralela foi criada. Exportações, histórico, autorização, dinheiro e documentos continuam no Core existente; fórmulas e planejamentos permanecem nos módulos responsáveis. Não houve mudança de slug, rota pública, inventário, fórmula Essencial ou página.

## Continuidade

Antes do Lote 7, reconstruir novamente o ZIP original e reaplicar todas as correções e Lotes 1–6 em ordem. Como `legacy_debt` está vazio, o próximo lote deve certificar funcionalmente uma seleção dos 61 contratos estritos restantes, sem recriar gates nem alterar o snapshot declarado de 137 benefícios.
