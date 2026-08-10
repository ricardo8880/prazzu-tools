# Calculadora de ICMS-ST

## Descrição

Calcula uma estimativa paramétrica de ICMS-ST para competências de 2026. A ferramenta não classifica NCM/CEST nem presume MVA ou alíquota por UF: esses parâmetros devem ser confirmados pelo usuário conforme produto e operação.

## Funcionalidades

- cálculo de base ICMS-ST, ICMS próprio, ICMS-ST e FCP parametrizado;
- MVA original ou ajustada em operações interestaduais;
- múltiplos itens, memória de cálculo, relatório e exportações.

## Experiência Essencial

- operação interna com valor da mercadoria, componentes da base, MVA e alíquota interna;
- cálculo da base de ICMS-ST, ICMS próprio e ICMS-ST estimado;
- memória de cálculo, premissas e referências normativas.

## Prazzu Plus

- operação interestadual;
- MVA ajustada pela fórmula `[(1 + MVA original) × (1 - ALQ inter) / (1 - ALQ intra)] - 1`;
- FCP-ST parametrizado;
- múltiplos itens na mesma operação;
- relatório PDF, planilha XLSX e histórico autenticado.

Durante a fase de lançamento, os recursos Plus permanecem disponíveis sem bloqueio comercial, conforme o README raiz.

## Regras

A ferramenta é paramétrica: MVA, alíquotas, FCP e componentes da base devem ser confirmados conforme NCM/CEST, UF e operação.

## Fórmulas

1. Base da operação = mercadoria + frete + seguro + outras despesas + IPI informado − desconto incondicional.
2. Base ICMS-ST = base da operação × (1 + MVA utilizada).
3. ICMS próprio = base da operação × alíquota própria, salvo valor destacado informado manualmente.
4. ICMS-ST = máximo entre zero e `(base ICMS-ST × alíquota interna) − ICMS próprio`.
5. FCP-ST = base ICMS-ST × alíquota FCP informada.

## Fontes e limites

- Convênio ICMS 142/2018 (CONFAZ);
- fórmula oficial de MVA ajustada publicada pela SEFAZ-PE;
- orientação de cálculo do ICMS-ST da Receita Estadual do RS.

A legislação estadual pode determinar base diversa, pauta/PMPF, redução, benefício, carga efetiva, tratamento específico do IPI/FCP e outras exceções. O resultado é estimativo e exige validação fiscal antes de recolhimento ou emissão documental.

## Dependências

- value objects monetários e percentuais do Core;
- contratos de histórico e exportação do Core;
- componentes visuais compartilhados da plataforma.

## Integração

- Slug: `calculadora-icms-st`
- Rota: `tools.calculadora-icms-st.index`
- Vertical: `contabilidade`
- Categoria: `fiscal`
- Histórico: habilitado
- Exportações: PDF e XLSX
- Dados sensíveis: nenhum


## Histórico de versões

- `1.0.0` — implementação inicial completa com operação interna, interestadual, MVA ajustada, FCP, múltiplos itens e exportações.
