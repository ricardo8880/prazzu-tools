# Satisfação e Retorno — Lote 1 — Confiança percebida

## Objetivo

Transformar metadados normativos que já existem no projeto em confiança percebida pelo usuário, sem alterar fórmulas, regras de domínio, catálogo, acesso, persistência ou a filosofia Essencial × Plus definida no `README.md` da raiz.

Este lote cobre exclusivamente:

1. referência usada no cálculo;
2. vigência da regra;
3. última verificação registrada;
4. fontes oficiais clicáveis;
5. versão e identificador técnico da regra;
6. indicação de estimativa;
7. premissas e limites já registrados na memória de cálculo.

## Estado de origem e continuidade

O lote partiu do ZIP original `prazzu-tools.zip` e releu obrigatoriamente:

- `README.md`;
- `CORE_CANDIDATES.md`;
- `docs/IMPLEMENTATION-LOTS.md`;
- relatórios UX/Growth relevantes;
- `config/product_tools.php`.

Como este é o primeiro lote desta nova frente, não existe lote anterior desta sequência para reaplicar. Antes do próximo lote, a reconstrução obrigatória é:

**ZIP original → Satisfação e Retorno Lote 1**, seguida de nova leitura dos documentos obrigatórios e comparação do estado acumulado antes de qualquer alteração.

## Decisão arquitetural

O projeto já possuía `NormativeRuleMetadata`, `NormativeRuleSnapshot` e `CalculationMemory` no Core técnico. Portanto, o lote não criou regra tributária, trabalhista ou fiscal compartilhada nova.

Foi criada apenas uma camada de apresentação reutilizável, `NormativeTrustContent`, que normaliza os metadados já produzidos pelos módulos. O componente Blade `x-tools.normative-trust` consome esse contrato e não conhece fórmulas, alíquotas, regimes ou regras específicas de qualquer ferramenta.

Isso evita a duplicação que já existia: a Calculadora de Salário Líquido exibia uma lista técnica própria de regras, enquanto outras ferramentas carregavam os mesmos snapshots sem apresentar fontes e vigência ao usuário.

## Experiência entregue

Quando o resultado realmente contém regra normativa com fonte oficial, a interface passa a exibir um bloco compacto **Confiança do resultado** com:

- quantidade de regras versionadas;
- quantidade de fontes oficiais;
- badge de estimativa quando aplicável;
- referência do cálculo, quando o snapshot possui data de referência;
- período de vigência;
- data da última verificação registrada;
- versão da regra;
- links para as fontes oficiais em nova aba com `noopener noreferrer`;
- responsável registrado pela verificação;
- identificador técnico como informação secundária;
- premissas e limites em disclosure separado.

A primeira regra fica aberta por padrão. As demais e as premissas permanecem recolhíveis para não aumentar desnecessariamente a carga visual do resultado.

## Ferramentas cobertas

A superfície compartilhada foi conectada às ferramentas que, no estado original, já entregavam regra normativa estruturada no resultado:

- Calculadora DIFAL / ICMS Interestadual + FCP;
- Calculadora de Custo de Funcionário;
- Calculadora de INSS Patronal;
- Simulador de Fator R;
- Calculadora de ICMS-ST;
- Calculadora de Retenções na Nota Fiscal;
- Calculadora de Encargos Trabalhistas;
- Calculadora de DAS em Atraso;
- Calculadora de Salário Líquido;
- Calculadora de Hora Extra, Adicional Noturno e DSR;
- Calculadora PIS e COFINS;
- Calculadora de IRPJ e CSLL — Lucro Presumido;
- Planejador de Retirada de Sócios;
- Simulador de Pró-Labore;
- Calculadora de DAS Retroativo + Regularização do Simples;
- Comparador Tributário.

Ferramentas sem fonte normativa estruturada não recebem data, vigência ou fonte inventada. O lote preserva a regra de transparência do projeto: ausência de evidência não é preenchida com copy genérica.

## Compatibilidade com Essencial e Plus

A confiança normativa não foi colocada atrás de autenticação ou Plus. Fonte, vigência e rastreabilidade necessárias para interpretar o resultado permanecem disponíveis no fluxo gratuito.

Nenhuma feature foi reclassificada e nenhum gate comercial foi alterado.

## Testes adicionados

- `tests/Unit/Core/Normative/NormativeTrustContentTest.php`: normalização de datas, vigência, fonte oficial, estimativa e premissas; também garante que a superfície não aparece sem fonte oficial.
- `tests/Architecture/NormativeTrustExperienceTest.php`: protege o conteúdo obrigatório da superfície e a presença do componente nas 16 ferramentas que já expõem regra normativa estruturada.
- O teste também garante que a Calculadora de Salário Líquido não volte à lista técnica isolada anterior.

## Limites preservados

- nenhuma fórmula foi alterada;
- nenhuma regra normativa de domínio foi alterada;
- nenhum slug, rota, vertical ou `release_order` foi alterado;
- `config/product_tools.php` não foi alterado;
- nenhuma fonte foi criada ou inferida;
- nenhuma promessa “sempre atualizado” ou de precisão absoluta foi adicionada;
- nenhuma funcionalidade de gestão, CRM, workflow ou cadastro operacional foi criada;
- nenhum cálculo passou a exigir conta;
- nenhuma capacidade Essencial foi movida para Plus.

## Continuidade obrigatória para o Lote 2

Antes de iniciar o próximo lote desta frente:

1. reabrir e analisar o ZIP original;
2. reaplicar este Lote 1 sobre o original;
3. comparar o estado acumulado com o relatório deste lote;
4. reler `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md` e `config/product_tools.php`;
5. somente então implementar o Lote 2.

O Lote 2 planejado permanece limitado a **redução de carga visual e progressive disclosure dos campos opcionais**, sem reabrir o trabalho de confiança normativa concluído aqui.
