# Calculadora de IRPJ e CSLL — Lucro Presumido

## Descrição

Calculadora fiscal trimestral para pessoas jurídicas em geral tributadas pelo Lucro Presumido em 2026. Calcula base presumida do IRPJ e da CSLL, IRPJ de 15%, adicional de IRPJ de 10%, CSLL de 9%, adições tributáveis integralmente e créditos informados pelo usuário.

## Funcionalidades

- Quatro perfis comuns de receita com percentuais de presunção explícitos.
- Múltiplas atividades no mesmo trimestre.
- Aplicação da LC 224/2025 sobre a parcela da receita acima do limite proporcional de 2026.
- Ajuste por receita de períodos anteriores para aproveitar corretamente o limite acumulado.
- Adições integrais, como receitas financeiras e ganhos de capital quando aplicáveis ao caso.
- Créditos/retenções parametrizados, histórico autenticado, PDF e XLSX.
- Memória de cálculo com snapshot normativo.

## Experiência Essencial

O Essencial entrega a apuração completa do trimestre: bases, IRPJ, adicional, CSLL, total e memória de cálculo. Nenhuma fórmula necessária ao cálculo principal é escondida.

## Prazzu Plus

O Plus acrescenta múltiplas atividades, ajuste acumulado entre trimestres, créditos confirmados, histórico e exportações. Durante a fase inicial da plataforma esses recursos permanecem liberados sem cobrança.

## Regras de domínio

- IRPJ: 15% sobre a base; adicional de 10% sobre a parcela da base trimestral que exceder R$ 60.000,00.
- CSLL: 9% para pessoas jurídicas em geral cobertas pelo escopo.
- Perfis de presunção-base: 8%/12%, 1,6%/12%, 16%/12% e 32%/32% (IRPJ/CSLL), conforme enquadramento informado.
- LC 224/2025: acréscimo de 10% nos percentuais de presunção sobre a receita que exceder o limite. No IRPJ, desde Q1/2026; na CSLL, desde Q2/2026.
- Limite normal proporcional: R$ 1.250.000,00 por trimestre. Em 2026, o limite anual da CSLL é R$ 3.750.000,00.
- Em múltiplas atividades, a faixa normal disponível é distribuída proporcionalmente à participação da receita de cada atividade.
- Receitas adicionadas integralmente às bases não consomem o limite da LC 224/2025.
- Dinheiro e percentuais não usam `float`; valores são arredondados em centavos com `HalfUp`.

Veja `docs/NORMATIVE_RULES.md` para fontes, escopo e versionamento.

## Integração entre ferramentas

- Contratos publicados: Nenhum.
- Contratos aceitos: Nenhum.

A ferramenta funciona integralmente sem outro módulo e não importa classes internas de outras ferramentas.

## Dependências

- `App\Core\Money` para dinheiro e percentuais.
- `App\Core\Normative` para vigência e snapshot normativo.
- Contrato padrão de cálculo, histórico e exportadores compartilhados do Core.
- Bootstrap e componentes Blade compartilhados.

## Histórico de versões

- `1.0.0` — implementação inicial para regras vigentes em 2026, incluindo LC 224/2025 e orientação RFB V5 de 30/07/2026.
