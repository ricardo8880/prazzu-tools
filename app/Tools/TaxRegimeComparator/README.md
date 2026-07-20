# Comparador Tributário

## Descrição

Ferramenta fiscal pontual para comparar estimativas de carga tributária entre
Simples Nacional, Lucro Presumido e Lucro Real. O resultado deve apresentar a
diferença mensal e anual entre os regimes, as premissas adotadas, os tributos
incluídos e os alertas necessários para interpretação responsável.

A ferramenta atende principalmente contadores, consultores e empresários que
precisam realizar uma triagem inicial de enquadramento. Ela não substitui
planejamento tributário formal, escrituração contábil ou apuração definitiva.

## Problema resolvido

Responder, para um cenário informado e uma data de referência explícita, qual
regime apresenta a menor carga tributária estimada e qual é a diferença
financeira entre as alternativas comparáveis.

## Escopo da primeira versão

- Simples Nacional;
- Lucro Presumido;
- Lucro Real em modo estimativo, com entradas e limitações explícitas;
- comparação mensal e anual;
- identificação do menor ônus estimado;
- premissas e alertas por regime;
- memória resumida do cálculo.

Não fazem parte do escopo:

- escrituração ou fechamento contábil;
- opção ou alteração cadastral de regime;
- transmissão de obrigações;
- gestão de clientes ou empresas;
- recomendação definitiva sem revisão profissional;
- cobertura automática de toda particularidade estadual ou municipal.

## Contrato de entrada

O contrato inicial está em
`Application/Data/TaxComparisonInput.php` e prevê:

- data de referência;
- atividade econômica classificada por enum de domínio;
- faturamento mensal;
- receita acumulada nos últimos doze meses;
- folha dos últimos doze meses;
- custos operacionais mensais;
- despesas dedutíveis mensais;
- base mensal elegível a créditos de PIS/Cofins, quando houver estimativa do Lucro Real;
- estado e município opcionais.

Valores monetários utilizam `App\Core\Money\Money`. Nenhuma regra financeira
pode usar `float`.

## Contrato de saída

O resultado de domínio está definido por:

- `Domain/Data/TaxComparisonResult.php`;
- `Domain/Data/TaxRegimeEstimate.php`;
- `Domain/Enums/TaxRegime.php`.

Cada regime deve informar uma situação tipada, estimativas mensal e anual,
itens tributários tipados, premissas e alertas. O resultado consolidado pode indicar o
menor ônus apenas quando houver dados suficientes e regimes comparáveis.

## Modelo de domínio do Lote 2

O cenário validado está em `Domain/Data/TaxComparisonScenario.php`. Ele impede
valores monetários incompatíveis, receitas não positivas, custos negativos e
localização inconsistente. Atividade, período e situação de estimativa são enums
de domínio. Nenhum desses objetos calcula tributos neste lote.

## Experiência Essencial

A experiência gratuita deverá resolver completamente o caso individual:

- comparar os regimes suportados;
- apresentar estimativas mensal e anual;
- mostrar a diferença financeira;
- expor as premissas e a memória resumida;
- explicar indisponibilidades e limitações.

A conclusão principal nunca será bloqueada pelo Plus.

## Prazzu Plus

Recursos previstos para produtividade e continuidade:

- múltiplos cenários;
- projeção de crescimento e análise anual;
- relatório profissional;
- histórico e reaproveitamento de dados, quando implementados pelo Core.

## Regras normativas

A dependência normativa é alta e o risco do resultado é tributário. Toda regra
de cálculo deverá:

- receber a data de referência explicitamente;
- possuir vigência e versão;
- apontar fonte oficial ou revisão responsável;
- falhar explicitamente diante de lacunas ou sobreposições;
- preservar reprodução histórica;
- possuir casos dourados aprovados antes da ativação.

## Funcionalidades

### Essenciais

- comparar estimativas do Simples Nacional, Lucro Presumido e Lucro Real;
- apresentar ranking pelo menor ônus tributário estimado;
- exibir premissas, alertas e memória resumida de cálculo.

### Prazzu Plus

- projeção anual;
- histórico de comparações;
- exportações CSV e JSON;
- relatório profissional imprimível/PDF;
- comparação de múltiplos cenários.

## Integração entre ferramentas

- Contratos publicados: nenhum neste lote.
- Contratos aceitos: nenhum neste lote.

A ferramenta não importa classes internas das calculadoras existentes. O uso
futuro de resultados do Simples Nacional ou de perfis operacionais dependerá
de contratos oficiais do Core e continuará opcional e revisável.

## Dependências

- contratos e objetos de valor compartilhados do Core;
- `App\Core\Money\Money`;
- Bootstrap e componentes visuais compartilhados.

## Integração com a plataforma

- Slug: `comparador-tributario`;
- rota principal: `tools.comparador-tributario.index`;
- namespace de views: `tools-comparador-tributario`;
- estado inicial: `draft`;
- histórico: habilitado pelo Core, com retenção de 365 dias e schema 1;
- exportação: CSV, JSON e relatório imprimível/PDF pelo Core;
- compartilhamento: desabilitado;
- dados pessoais: não previstos no contrato inicial.

## Lotes planejados

1. Escopo, manifesto e contratos estruturais.
2. Modelo e validações de domínio.
3. Motor do Simples Nacional por contrato compartilhado.
4. Motor do Lucro Presumido.
5. Motor estimativo do Lucro Real.
6. Orquestração e ranking da comparação.
7. Interface completa.
8. Plus, relatórios, histórico e qualidade final.

## Histórico de versões

| Versão | Estado | Alterações |
| --- | --- | --- |
| 0.8.0 | Draft | Histórico Plus, exportações CSV/JSON, relatório imprimível e cobertura transversal final no Lote 8. |
| 0.7.0 | Draft | Interface completa, validação HTTP e apresentação responsiva dos resultados no Lote 7. |
| 0.6.0 | Draft | Orquestração dos três regimes, ranking comparável e economia contra a segunda melhor alternativa no Lote 6. |
| 0.5.0 | Draft | Motor estimativo do Lucro Real, créditos explícitos de PIS/Cofins e transição de 2026 tratada no Lote 5. |
| 0.4.0 | Draft | Motor estimativo do Lucro Presumido, tributos indiretos informados e lacunas normativas explícitas do Lote 4. |
| 0.3.0 | Draft | Integração do Simples Nacional por contrato tributário neutro do Core. |
| 0.2.0 | Draft | Modelo de domínio, cenário validado, enums e itens tributários do Lote 2. |
| 0.1.0 | Draft | Estrutura, escopo, perfil de risco e contratos iniciais do Lote 1. |

## Lote 3 — estimativa do Simples Nacional

A estimativa do Simples Nacional é obtida por um contrato neutro do Core (`App\Core\Taxation`). O Comparador não importa DTOs, calculators, Actions ou regras internas da Calculadora de Simples Nacional.

O adaptador mantido pelo módulo de origem: 

- resolve Anexo I para comércio;
- resolve Anexo II para indústria;
- resolve Anexo III para serviços contábeis;
- usa o Fator R para decidir entre Anexo III e V nos demais serviços suportados;
- retorna DAS mensal, projeção anual, alíquota efetiva, premissas e alertas;
- recusa atividades mistas e datas anteriores ao início da regra suportada.

Na ausência de um fornecedor compatível, o Comparador retorna o regime como indisponível e continua operacional. Lucro Presumido e Lucro Real permanecem fora deste lote.

## Lote 4 — estimativa do Lucro Presumido

O motor do Lucro Presumido calcula uma estimativa mensal e anual com memória
separada para IRPJ, adicional de IRPJ, CSLL, PIS/Pasep, Cofins e tributos
indiretos. A alíquota efetiva de ISS, ICMS ou ICMS/IPI é uma entrada explícita
do cenário, pois não existe uma alíquota nacional única que possa ser inferida
apenas por estado e município.

Escopo suportado:

- comércio e indústria com presunção de 8% para IRPJ e 12% para CSLL;
- serviços e serviços contábeis com presunção de 32% para IRPJ e CSLL;
- IRPJ de 15% e adicional de 10% sobre a base mensal que exceder R$ 20.000;
- CSLL de 9%;
- PIS/Pasep cumulativo de 0,65%;
- Cofins cumulativa de 3%;
- tributos indiretos pela alíquota efetiva informada;
- projeção anual pela repetição do cenário mensal.

Atividades mistas permanecem indisponíveis sem segregação de receitas. Para
datas a partir de 2026, cenários com receita acumulada superior a R$ 5 milhões
são recusados neste lote, pois a alteração dos percentuais de presunção exige
receita acumulada no ano-calendário e ajustes por período que ainda não fazem
parte do contrato de entrada. O limite geral de elegibilidade considerado é de
R$ 78 milhões de receita acumulada.

Fontes normativas de referência: Lei nº 9.249/1995, Lei nº 9.430/1996,
Instrução Normativa RFB nº 1.700/2017, Lei Complementar nº 224/2025 e Decreto
nº 12.808/2025. A regra permanece em estado draft até revisão profissional e
aprovação dos casos dourados.


## Lote 5 — estimativa do Lucro Real

O motor do Lucro Real permanece deliberadamente estimativo. O lucro tributável
mensal é aproximado pela receita menos custos operacionais e despesas
dedutíveis informadas. O cálculo não substitui resultado contábil ajustado por
adições, exclusões e compensações no e-Lalur/e-Lacs.

Escopo suportado:

- IRPJ de 15%;
- adicional de IRPJ de 10% sobre a parcela mensal do lucro acima de R$ 20.000;
- CSLL de 9% para pessoas jurídicas em geral;
- PIS/Pasep não cumulativo de 1,65%;
- Cofins não cumulativa de 7,6%;
- créditos de PIS/Cofins calculados exclusivamente sobre a base mensal explicitamente informada;
- ISS, ICMS ou ICMS/IPI por alíquota efetiva informada;
- projeção anual pela repetição do cenário mensal.

Atividades mistas permanecem indisponíveis sem segregação. Prejuízo fiscal,
base negativa de CSLL, compensações, incentivos, retenções, receitas financeiras
e particularidades setoriais não são inferidos. Para 2026, CBS e IBS de teste
não são somados como ônus adicional porque sua arrecadação é compensada com
PIS/Cofins no período. Datas a partir de 2027 permanecem indisponíveis até a
implementação normativa da CBS e da extinção de PIS/Cofins.


## Lote 6 — orquestração e ranking

A Action `CompareTaxRegimes` valida o cenário uma única vez, solicita as três
estimativas e entrega o conjunto ao serviço de domínio `TaxComparisonRanker`.

Regras da consolidação:

- somente estimativas com status disponível e totais completos entram no ranking;
- o ranking é ordenado pelo ônus anual estimado;
- a diferença de cada posição é calculada contra o regime de menor ônus;
- a economia principal é calculada contra a segunda melhor alternativa, evitando
  apresentar como economia o maior contraste possível;
- um vencedor só é indicado quando ao menos dois regimes são comparáveis;
- regimes indisponíveis continuam no resultado completo e seus alertas são
  preservados com identificação do regime;
- premissas e alertas são consolidados sem duplicação;
- o resultado permanece orientativo e não constitui recomendação tributária
  definitiva.


## Lote 7 — interface completa

A ferramenta passa a oferecer formulário web completo e responsivo, construído
com Bootstrap e componentes compartilhados. A experiência inclui validação
servidor, preservação dos dados informados, campos avançados recolhíveis, ranking
por regime, economia contra a segunda melhor alternativa, composição dos tributos,
premissas, alertas e exposição dos regimes não comparáveis.

A interface não oculta limitações. Quando menos de dois regimes são comparáveis,
nenhum vencedor é declarado. Os dados avançados de créditos de PIS/Cofins e
alíquota indireta permanecem entradas explícitas para evitar inferências fiscais
sem suporte no cenário.


## Lote 8 — Plus, histórico, exportações e qualidade final

A ferramenta aderiu à infraestrutura transversal do Core sem criar persistência
ou exportadores próprios. O manifesto declara histórico, persistência versionada
e exportação como capacidades reais.

Recursos implementados:

- histórico Plus autenticado, com retenção de 365 dias;
- gravação da entrada e do resultado apresentado, incluindo versões da ferramenta,
  regra e schema;
- listagem, consulta, reutilização e exclusão de comparações próprias;
- exportação estruturada em CSV e JSON pelo `ToolResultExporter` central;
- relatório profissional por `BrowserPrintExporter`, pronto para impressão ou PDF;
- gates compartilhados por funcionalidade, sem regras comerciais locais;
- estados vazios e mensagens de continuidade na interface.

O status permanece `draft`. Os casos dourados tributários ainda possuem referências
provisórias e precisam de revisão profissional aprovada. A ferramenta somente poderá
mudar para `active` após substituição dessas referências e aprovação integral do
`composer release:check`.
