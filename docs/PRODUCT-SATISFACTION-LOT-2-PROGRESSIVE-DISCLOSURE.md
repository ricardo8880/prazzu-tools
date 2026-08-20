# Satisfação e Retorno — Lote 2 — Progressive disclosure dos formulários

## Objetivo

Reduzir a carga visual e o esforço percebido antes do primeiro resultado, sem retirar capacidade, esconder informação necessária ao cálculo básico ou alterar a classificação Essencial × Plus definida pelo `README.md` da raiz.

O lote atua somente sobre apresentação de campos opcionais, complementares ou avançados. Fórmulas, requests, DTOs, regras normativas, persistência, catálogo e resultados permanecem inalterados.

## Reconstrução obrigatória realizada

Antes de alterar o projeto, o estado foi reconstruído nesta ordem:

1. ZIP original `prazzu-tools.zip`;
2. reaplicação integral do Lote 1 — Confiança percebida;
3. releitura de `README.md`;
4. releitura de `CORE_CANDIDATES.md`;
5. releitura de `docs/IMPLEMENTATION-LOTS.md` e do relatório do Lote 1;
6. conferência de `config/product_tools.php`;
7. nova auditoria dos formulários de maior densidade.

Isso preserva o trabalho acumulado e impede que o lote seja implementado a partir de uma cópia parcial ou de memória.

## Decisão de experiência

O projeto possuía repetição concreta de blocos opcionais/avançados ocupando o mesmo peso visual dos dados necessários para iniciar um cálculo. Foi criado o componente compartilhado `x-tools.form-disclosure` usando `details` e `summary` nativos.

O componente:

- funciona sem JavaScript;
- recebe somente título, descrição, badge, estado aberto e conteúdo;
- não conhece regras de domínio;
- não decide se algo é Essencial ou Plus;
- não altera nem desabilita campos;
- pode iniciar aberto quando a ferramenta precisa revelar erros de validação.

## Ferramentas ajustadas

### Calculadora de Salário Líquido

Os três dados de entrada principais continuam imediatamente visíveis. Proventos e descontos adicionais passam a aparecer sob **Tenho outros proventos ou descontos**, reduzindo a impressão de que todos os campos precisam ser preenchidos.

### Calculadora de Hora Extra, Adicional Noturno e DSR

O cálculo principal de hora extra permanece visível. Jornada noturna, DSR e reflexos ficam em disclosure Plus separado.

### Calculadora de ICMS-ST

Os parâmetros do item principal continuam visíveis. MVA ajustada, FCP, alíquota interestadual complementar e itens adicionais ficam agrupados em **Recursos avançados**.

### Calculadora PIS e COFINS

A apuração principal permanece exposta. Comparação de regimes e operações adicionais passam a ocupar espaço somente quando o usuário decide utilizá-las.

### Calculadora de IRPJ e CSLL — Lucro Presumido

Receita e apuração principal permanecem visíveis. Ajustes acumulados/créditos e comparação de cenários foram separados em dois disclosures Plus independentes.

### Simulador de Pró-Labore

Competência, regime e pró-labore bruto permanecem no caminho principal. Dependentes e outras contribuições oficiais ficam em **Deduções e contribuições adicionais**.

### Calculadora de Custo de Funcionário

Os dados que alteram o cálculo permanecem visíveis. Nome, departamento, empresa salva e nome do cenário foram agrupados em **Identificação do cenário**, pois servem à organização/continuidade e não são necessários para compreender o cálculo inicial.

## Regra de segurança de UX

O lote deliberadamente não recolheu campos que podem se tornar necessários conforme outra seleção feita pelo usuário. Reduzir altura da página não pode criar uma situação em que a pessoa não perceba um dado necessário ao próprio caso.

Quando um campo agrupado possui erro de validação, o disclosure correspondente recebe estado aberto, de modo que a correção não fique escondida após o submit.

## Compatibilidade Essencial × Plus

Nenhuma capacidade foi bloqueada. Todos os campos continuam presentes no HTML, submetem os mesmos nomes e permanecem disponíveis gratuitamente durante a fase de lançamento.

Progressive disclosure é hierarquia visual, não paywall.

## Testes adicionados

`tests/Architecture/ProgressiveDisclosureExperienceTest.php` protege:

- uso de `details/summary` nativo;
- ausência de dependência de Bootstrap/JavaScript para abrir o conteúdo;
- presença do padrão compartilhado nas sete ferramentas selecionadas;
- permanência dos campos Essenciais da Calculadora de Salário Líquido antes do disclosure opcional.

## Continuidade obrigatória para o Lote 3

Antes de qualquer alteração futura desta sequência:

**ZIP original → Lote 1 → Lote 2 → análise do estado acumulado.**

Depois, reler novamente `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos lotes concluídos e `config/product_tools.php`.

O Lote 3 deve focar em tornar o resultado mais útil e interpretável para o usuário. Não deve refazer confiança normativa nem progressive disclosure, exceto se um teste ou regressão concreta exigir correção.
