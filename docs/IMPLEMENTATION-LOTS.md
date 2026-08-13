# Continuidade dos lotes de implementação

Este documento é o ponto obrigatório de continuidade entre lotes. Ele complementa o `README.md` e o `CORE_CANDIDATES.md`; nunca substitui as regras desses arquivos.

## Procedimento obrigatório antes de cada lote

1. Ler integralmente o `README.md` da raiz.
2. Ler integralmente o `CORE_CANDIDATES.md`.
3. Ler este documento e todos os relatórios de lotes concluídos.
4. Conferir `config/product_tools.php` e o estado real do código.
5. Executar os testes e verificações disponíveis antes de alterar o projeto.
6. Continuar a partir do estado entregue; não recriar decisões já consolidadas sem motivo documentado.
7. Atualizar este documento ao concluir o lote.

## Plano aprovado

| Lote | Escopo | Estado |
|---:|---|---|
| 1 | Inventário, governança e saneamento estrutural | Concluído |
| 2 | Core normativo e capacidades compartilhadas | Concluído |
| 3 | Custo CLT, Encargos e INSS Patronal | Concluído |
| 4 | CLT/PJ/Autônomo, Pró-Labore e Distribuição de Lucros | Concluído |
| 5 | Holerite, Admissão, Demissão e Reajuste | Concluído |
| 6 | Fator R, DAS em Atraso e Simulador Tributário | Concluído |
| 7 | Capital de Giro, Fluxo de Caixa e Ponto de Equilíbrio | Concluído |
| 8 | Precificação e Comissão | Concluído |
| 9 | Declaração de Rendimentos e Declaração de Trabalho/Renda | Concluído |
| 10 | Conformidade, integração, documentação e release | Concluído |

## Resultado do Lote 1

- O catálogo oficial das 20 ferramentas foi registrado em `config/product_tools.php`.
- Os 8 módulos fora do catálogo principal foram classificados sem remoção ou despublicação prematura.
- O módulo combinado de Pró-Labore e Distribuição de Lucros foi marcado formalmente para separação no Lote 4.
- Divergências de nome e escopo foram registradas como trabalho futuro, sem alterar URLs públicas neste lote.
- Foi criada validação arquitetural para impedir que o inventário oficial fique incompleto, duplicado ou desconectado do código real.
- O empacotador ZIP obsoleto do Analytics foi removido em favor do serviço compartilhado do Core técnico.
- O timestamp duplicado de migration foi corrigido.
- O README passou a exigir continuidade explícita entre lotes.

## Decisões preservadas para os próximos lotes

- Não remover os 8 módulos complementares durante a implementação das 20 ferramentas.
- Não renomear slugs públicos sem estratégia explícita de compatibilidade e redirecionamento.
- Separar `ProLaboreProfitDistributionCalculator` apenas no Lote 4, quando domínio, rotas, contratos e migração puderem ser tratados em conjunto.
- Avaliar `SimplesNacionalCalculator` no Lote 6 para evitar duplicação normativa com Fator R e Simulador Tributário.
- Tratar itens `alignment_required`, `rename_and_scope_alignment` e `split_required` como estados de implementação, não como estados de publicação.


## Resultado do Lote 2

- Foi criado o contrato `NormativeRuleCatalog` e uma implementação em memória validada pelo resolver central.
- Cálculos podem persistir `NormativeRuleSnapshot` com versão, vigência, verificação e fontes oficiais.
- Foi padronizada a memória de cálculo por meio de `CalculationMemory` e `CalculationMemoryStep`.
- A política de dinheiro, percentuais, arredondamento, estimativas e fontes foi documentada em `docs/normative/CORE-NORMATIVE-POLICY.md`.
- Nenhuma alíquota ou tabela temporal foi inventada neste lote; catálogos concretos serão adicionados pelos lotes de domínio com fontes oficiais e casos dourados.

## Decisões preservadas para o Lote 3

- Migrar Custo CLT, Encargos Trabalhistas e INSS Patronal para o catálogo e a memória padronizados.
- Não mover regras específicas de uma ferramenta para o Core sem reutilização real comprovada.
- Guardar snapshots normativos em todo resultado persistível ou exportável.


## Resultado do Lote 3

- Custo CLT, Encargos Trabalhistas e INSS Patronal passaram a usar uma regra compartilhada de encargos patronais, sem dependência direta entre ferramentas.
- A regra compartilhada registra fontes oficiais para CPP, RAT, FGTS e tratamento do Anexo IV do Simples Nacional.
- RAT ajustado e terceiros continuam parâmetros explícitos, pois dependem da atividade, FPAS, FAP e enquadramento do contribuinte.
- Os três resultados agora carregam `CalculationMemory` versionada e snapshot normativo persistível/exportável.
- Os schemas de resultado foram incrementados de forma compatível.

## Decisões preservadas para o Lote 4

- Separar Pró-Labore e Distribuição de Lucros em módulos independentes.
- Reutilizar apenas capacidades realmente compartilhadas do Core, sem importar domínio entre ferramentas.
- Preservar URLs públicas com estratégia de compatibilidade durante a separação.


## Resultado do Lote 4

- `ProLaboreSimulator` e `ProfitDistributionCalculator` foram criados como módulos oficiais independentes.
- O módulo combinado foi retirado do inventário oficial e preservado como compatibilidade legada temporária.
- As novas ferramentas possuem slugs e rotas próprias, sem importar classes de domínio uma da outra.
- Regras já validadas do módulo combinado foram copiadas para seus domínios responsáveis; nenhuma abstração compartilhada prematura foi criada.
- O Comparador CLT × PJ × Autônomo foi mantido independente e continua usando percentuais declarados pelo usuário, pois os cenários variam por enquadramento.

## Decisões preservadas para o Lote 5

- Tratar Holerite, Admissão, Demissão e Reajuste como módulos independentes.
- Reutilizar apenas capacidades transversais do Core e não importar domínio entre ferramentas.
- Não remover a compatibilidade legada de Pró-Labore/Distribuição antes do Lote 10, quando rotas, histórico e métricas puderem ser auditados.


## Resultado do Lote 5

- Holerite, Admissão, Demissão e Reajuste permaneceram módulos independentes, sem importações de domínio entre ferramentas.
- Holerite passou a expor memória estruturada de proventos, descontos e líquido, deixando explícito que tributos são informados pelo usuário.
- Admissão passou a expor memória reproduzível e a marcar a projeção como estimativa baseada no percentual declarado.
- Demissão passou a incluir memória de cálculo no resultado, cobrindo remuneração-base, verbas, descontos, líquido e FGTS estimado.
- Reajuste passou a registrar cada etapa e a premissa do impacto anual, sem incluir encargos patronais implicitamente.
- Os schemas das três ferramentas baseadas em `ToolCalculationResult` foram incrementados para `1.1.0`; a versão normativa já existente da Demissão foi preservada.

## Decisões preservadas para o Lote 6

- Tratar Fator R, DAS em Atraso e Simulador Tributário como módulos independentes.
- Reutilizar o catálogo normativo do Core e snapshots por competência.
- Avaliar `SimplesNacionalCalculator` como módulo complementar sem duplicar regras oficiais.

## Resultado do Lote 6

- Fator R passou a usar regra normativa compartilhada, snapshot por referência e memória estruturada, sem depender do módulo complementar do Simples Nacional.
- DAS em Atraso passou a obter multa diária, limite e adicional mensal de regra normativa versionada; a Selic permanece entrada explícita e o resultado é marcado como estimativa.
- Simulador Tributário passou a expor snapshot normativo, memória auditável do ranking e indicação explícita de estimativa.
- `SimplesNacionalCalculator` permanece complementar e fora do catálogo oficial; nenhuma dependência entre os quatro módulos tributários foi criada.
- Os schemas de Fator R e DAS foram incrementados de `1.0.0` para `1.1.0` de forma compatível.

## Decisões preservadas para o Lote 7

- Tratar Capital de Giro, Fluxo de Caixa e Ponto de Equilíbrio como módulos independentes.
- Padronizar memória financeira e premissas temporais sem mover fórmulas específicas prematuramente para o Core.
- Preservar dinheiro em centavos e arredondamento explícito em todas as projeções.


## Resultado do Lote 7

- Capital de Giro, Fluxo de Caixa e Ponto de Equilíbrio permaneceram módulos independentes e sem importações de domínio entre ferramentas.
- Os três resultados passaram a usar `CalculationMemory` estruturada, com entradas e resultados monetários preservados em centavos.
- Capital de Giro passou a declarar a data-base comum, a classificação informada e a limitação de uma fotografia sem sazonalidade automática.
- Fluxo de Caixa passou a declarar o período único, o regime de caixa e a natureza estimativa do saldo final previsto.
- Ponto de Equilíbrio passou a documentar o arredondamento para cima até unidade inteira, as premissas de preço/custo constantes e a inclusão de tributos e comissões no custo variável quando aplicáveis.
- Os schemas das três ferramentas foram incrementados de `1.0.0` para `1.1.0` de forma compatível.
- As fórmulas permaneceram nos respectivos domínios; não foi criada abstração financeira prematura no Core.

## Decisões preservadas para o Lote 8

- Tratar Precificação e Comissão de Vendas como módulos independentes.
- Reutilizar dinheiro, percentuais e memória de cálculo do Core, sem compartilhar regras comerciais específicas entre ferramentas.
- Tornar explícitas as diferenças entre margem, markup, base de comissão, estornos e arredondamento monetário.


## Resultado do Lote 8

- Precificação e Comissão de Vendas permaneceram módulos independentes, sem importações de domínio entre ferramentas.
- Precificação passou a expor memória estruturada de custo total, preço sugerido, lucro líquido e markup, com arredondamento monetário explícito.
- Margem líquida, markup percentual e multiplicador de markup foram diferenciados formalmente na regra, interface e documentação.
- Comissão passou a aceitar estornos e devoluções como redução explícita da base comissionável.
- A mesma base líquida passou a determinar comissão-base, bônus e atingimento da meta.
- Regras contratuais não informadas, como competência, tetos e devoluções futuras, não são inferidas e o resultado permanece estimativo.
- Os schemas foram incrementados para `2.1.0` em Precificação e `1.1.0` em Comissão.

## Decisões preservadas para o Lote 9

- Tratar Declaração de Rendimentos e Declaração de Trabalho/Renda como documentos distintos.
- Reutilizar apenas capacidades documentais, de exportação e proteção de dados do Core.
- Não criar afirmações fiscais, vínculos laborais ou validações de autenticidade que não possam ser sustentadas pelos dados e fontes disponíveis.


## Resultado do Lote 9

- Declaração de Rendimentos e Declaração de Trabalho/Renda permaneceram documentos distintos e sem importações de domínio entre ferramentas.
- Foi criada a capacidade documental compartilhada `GeneratedDocumentNotice` para propósito, limitações, revisão, assinatura e ausência de validação de autenticidade.
- A Declaração de Rendimentos passou a deixar explícito que organiza valores previamente apurados e não substitui informe fiscal oficial ou obrigação acessória.
- A Declaração de Trabalho/Renda passou a impedir datas incoerentes e a não presumir vínculo empregatício ou qualificação jurídica.
- Os dois resultados passaram a incluir memória documental estruturada e avisos de revisão e assinatura.
- As políticas de dados sensíveis, histórico, exportação e compartilhamento desativado foram preservadas.
- Os schemas e manifests dos dois módulos foram incrementados para `1.1.0`.

## Decisões preservadas para o Lote 10

- Auditar rotas, histórico, exportação, privacidade, documentação e compatibilidade legada de todos os módulos.
- Executar o saneamento final de estados do inventário apenas com evidência dos testes de release.
- Não remover o módulo legado de Pró-Labore/Distribuição sem verificar uso, redirecionamentos e histórico persistido.


## Resultado do Lote 10

- O inventário oficial foi saneado para `implemented` após a conclusão dos lotes de domínio e a auditoria final de módulos, slugs e registos.
- Foi criado um contrato arquitetural de prontidão de release para garantir 20 ferramentas oficiais distintas, módulos registados e documentação mínima.
- Rotas públicas e slugs existentes foram preservados; divergências históricas de nomes não motivaram alterações incompatíveis.
- O módulo combinado de Pró-Labore/Distribuição permanece registado como compatibilidade legada porque não há evidência suficiente, neste repositório, sobre uso externo, histórico persistido e métricas para uma remoção segura.
- Histórico, exportação e privacidade foram auditados por contrato e documentados; capacidades indisponíveis não foram simuladas.
- A documentação de release passou a distinguir verificações executáveis neste ambiente das verificações bloqueadas por extensões PHP ausentes.
- O catálogo oficial encerra o ciclo com 20 ferramentas e 20 módulos oficiais únicos, mantendo os módulos complementares classificados.

## Continuidade após o Lote 10

- Novas alterações devem partir deste estado de release e continuar seguindo README, `CORE_CANDIDATES.md`, inventário e testes arquiteturais.
- A remoção do módulo legado exige plano de migração próprio, telemetria ou evidência de uso, redirecionamentos e tratamento de histórico persistido.
- Estados `implemented` indicam alinhamento do catálogo e conclusão do escopo dos lotes; não substituem a execução integral de `composer release:check` no ambiente oficial de CI.

## Expansão — Lote 11 — Calculadora de Salário Líquido

### Resultado do Lote 11

- `NetSalaryCalculator` foi criado como módulo independente e público, sem dependência de outro namespace de ferramenta.
- O escopo Essencial calcula salário mensal CLT regular com INSS progressivo do empregado, IRRF mensal de 2026, dependentes e salário líquido.
- O escopo Plus adiciona proventos e descontos personalizados, histórico autenticado e exportações, sem esconder ou corrigir parte necessária da fórmula Essencial.
- A regra mensal de IRRF, já existente no Pró-Labore e necessária no Salário Líquido, foi promovida para `App\Core\Tax\Normative\MonthlyPersonalIncomeTaxRule`; ambos os módulos passaram a usar a mesma regra compartilhada.
- O INSS do empregado permaneceu no domínio do Salário Líquido porque difere estruturalmente da retenção previdenciária do contribuinte individual usada no Pró-Labore.
- As regras de 2026 foram registradas com fontes oficiais do INSS e da Receita Federal, memória de cálculo e snapshot normativo.
- O módulo foi classificado temporariamente em `additional_modules` como `expansion_lot_11`; a promoção do catálogo oficial e a consolidação das novas ferramentas ficam reservadas ao lote de integração da expansão, preservando os contratos do Lote 10.
- Férias, 13º, rescisão, múltiplos vínculos e classificação jurídica automática de rubricas permanecem explicitamente fora do escopo desta ferramenta.

### Continuidade para o próximo lote da expansão

- Antes de iniciar o próximo lote, reler o ZIP original, este documento, `CORE_CANDIDATES.md`, `config/product_tools.php` e todos os pacotes de lotes da expansão já produzidos.
- O próximo módulo não deve importar classes internas de `NetSalaryCalculator`; qualquer reutilização deve ocorrer somente via Core técnico quando houver equivalência real.
- Preservar o slug público `calculadora-salario-liquido` e o schema de resultado `1.0.0`, evoluindo-os apenas de forma compatível e documentada.

## Expansão — Lote 12 — Calculadora de Hora Extra, Adicional Noturno e DSR

### Resultado do Lote 12

- `OvertimeCalculator` foi criado como módulo trabalhista independente, sem importações de outros módulos de ferramenta.
- O Essencial resolve valor da hora e horas extras de 50%, 100% e percentual informado.
- O Plus adiciona adicional noturno urbano com hora reduzida de 52m30s, hora extra noturna, DSR parametrizado e projeções de reflexos, sem esconder a fórmula básica.
- Regras normativas foram ancoradas nos arts. 59 e 73 da CLT, Lei 605/1949 e TST Tema 256/Súmula 172, com snapshot e memória de cálculo.
- Divisor mensal, calendário e percentuais acima do mínimo são parâmetros do caso e não são inferidos pela ferramenta.
- Nenhuma nova promoção ao Core foi necessária; capacidades transversais existentes foram reutilizadas.
- O módulo foi classificado em `additional_modules` como `expansion_lot_12`, mantendo a consolidação do catálogo para o lote de integração.

### Continuidade para o próximo lote da expansão

- Antes do próximo lote, reler o ZIP original e reaplicar, em ordem, os lotes 11 e 12.
- Preservar os slugs `calculadora-salario-liquido` e `calculadora-hora-extra`.
- O próximo módulo não pode importar classes internas destes módulos; reutilização só via Core quando houver equivalência comprovada.


## Expansão — Lote 13 — Calculadora DIFAL / ICMS Interestadual + FCP

### Resultado do Lote 13

- `DifalIcmsCalculator` foi criado como módulo fiscal independente, sem importações de outros módulos de ferramenta.
- O Essencial calcula DIFAL a partir da base tributável e das alíquotas aplicáveis confirmadas pelo usuário, com memória fiscal completa.
- O Plus adiciona assistência para alíquota interestadual de 7%/12% e 4% quando o enquadramento da Resolução do Senado nº 13/2012 for confirmado, FCP parametrizado e simulação de base dupla/por dentro quando aplicável.
- Alíquota interna, FCP, NCM, benefícios e método de base não são inferidos apenas pela UF, evitando falsa precisão fiscal.
- A responsabilidade pelo diferencial é apresentada conforme o destinatário seja contribuinte ou não contribuinte, sem substituir a análise da operação concreta.
- As fontes normativas incluem EC 87/2015, LC 190/2022, Resolução do Senado nº 22/1989 e Resolução do Senado nº 13/2012.
- Nenhuma nova promoção ao Core foi necessária; a determinação de alíquota interestadual permanece no domínio até existir segunda reutilização equivalente.
- O módulo foi classificado em `additional_modules` como `expansion_lot_13`, mantendo a consolidação do catálogo para o lote de integração.

### Continuidade para o próximo lote da expansão

- Antes do próximo lote, reler o ZIP original e reaplicar, em ordem, os lotes 11, 12 e 13.
- Preservar os slugs `calculadora-salario-liquido`, `calculadora-hora-extra` e `calculadora-difal-icms`.
- O próximo lote deve consolidar as três novas ferramentas no produto sem alterar contratos públicos das 29 ferramentas preexistentes.


## Expansão — Lote 14 — Integração das três novas ferramentas

### Resultado do Lote 14

- O estado foi reconstruído a partir do ZIP original com aplicação sequencial dos Lotes 11, 12 e 13 antes de qualquer alteração.
- `NetSalaryCalculator`, `OvertimeCalculator` e `DifalIcmsCalculator` foram promovidos de módulos adicionais temporários para o catálogo oficial.
- O inventário executável passou de 20 para 23 ferramentas oficiais, preservando 9 módulos adicionais e totalizando 32 módulos classificados exatamente uma vez.
- Os IDs oficiais 1–20 foram preservados; as novas ferramentas receberam IDs 21–23, sem renomear slugs públicos.
- Busca, categorias, relacionados e sitemap continuam derivados do `ToolCatalog`/`ToolRegistry`; nenhuma camada paralela de catálogo foi criada.
- `release_readiness` foi atualizado para `expansion_lot_14_integrated`, indicando integração concluída e auditoria final ainda pendente.
- Nenhuma nova promoção ao Core foi necessária.

### Continuidade para o próximo lote da expansão

- Antes do Lote 15, reler o ZIP original e reaplicar, em ordem, os Lotes 11, 12, 13 e 14.
- Confirmar 32 módulos em `app/Tools`, 23 registros oficiais e 9 adicionais.
- Preservar os slugs `calculadora-salario-liquido`, `calculadora-hora-extra` e `calculadora-difal-icms`.
- O Lote 15 deve ser de auditoria/regressão final da expansão e não deve alterar escopo funcional sem necessidade comprovada.


## Expansão — Lote 15 — Auditoria final da expansão

### Resultado do Lote 15

- O estado foi reconstruído a partir do ZIP original com aplicação sequencial dos Lotes 11, 12, 13 e 14.
- Foram confirmados 32 módulos registrados, sendo 23 ferramentas oficiais e 9 módulos adicionais classificados exatamente uma vez.
- A auditoria detectou que o ZIP original carregava `bootstrap/cache/routes-v7.php` antigo, ocultando as rotas dos três módulos novos; o cache foi regenerado para compatibilidade do patch e a distribuição futura passou a remover/rejeitar caches PHP em `bootstrap/cache`.
- `OvertimeCalculator` e `DifalIcmsCalculator` receberam as suítes de casos dourados e gates de qualidade exigidos por `docs/TOOL_QUALITY.md`, sem alteração das fórmulas de domínio.
- Lint, arquitetura, Analytics, registro/inventário, rotas e smoke tests diretos da expansão foram aprovados neste ambiente.
- PHPUnit, Pint e `composer release:check` completo continuam bloqueados pela ausência de `dom`, `mbstring`, `pdo_sqlite`, `xml` e `xmlwriter` neste ambiente.
- `release_readiness` foi atualizado para `expansion_lot_15_audited`.
- Nenhuma nova promoção ao Core foi necessária.

### Continuidade após a expansão

- Preservar os 23 IDs oficiais, os 32 módulos atuais e os slugs públicos das três ferramentas adicionadas.
- Novos lotes devem reler o ZIP original e todos os patches da expansão quando a base consolidada não estiver disponível.
- A aprovação operacional de publicação continua condicionada ao `composer release:check` em CI compatível com `docs/INSTALLATION.md`.

# Ciclo cirúrgico de saneamento do catálogo

## Plano aprovado

| Lote | Escopo | Estado |
|---:|---|---|
| 1 | Inventário e governança das 32 ferramentas | Concluído |
| 2 | Home com exatamente 8 ferramentas mais recentes | Concluído |
| 3 | Migração segura da ferramenta combinada | Concluído |
| 4 | Substituição, limpeza final e regressão | Concluído |

## Resultado do Lote Cirúrgico 1

- O inventário executável foi consolidado em exatamente 32 ferramentas oficiais.
- A classificação paralela de módulos adicionais foi removida.
- Todas as 32 ferramentas devem permanecer visíveis na página Ferramentas.
- `ProLaboreProfitDistributionCalculator` continua ativa com `migration_pending`, sem remoção prematura.
- O contrato agora bloqueia módulos escondidos, omitidos ou classificados mais de uma vez.
- Nenhuma URL pública, rota ou módulo foi alterado neste lote.

## Continuidade obrigatória para o Lote Cirúrgico 2

- Reler o ZIP original e este patch.
- Preservar as 32 entradas oficiais e os slugs públicos.
- Corrigir a Home para retornar exatamente 8 ferramentas.
- Definir uma fonte inequívoca de recência, sem confundir `featured` ou `position` com data de publicação.
- Não iniciar a remoção da ferramenta combinada antes do lote dedicado.

## Resultado do Lote Cirúrgico 2

- O estado foi reconstruído a partir do ZIP original com aplicação do Lote Cirúrgico 1 antes das alterações.
- As 32 ferramentas receberam `release_order` único e completo no inventário executável.
- A ordem de publicação foi separada de `position`, que permanece exclusivamente editorial.
- `ToolCatalog::latest(8)` passou a usar `release_order`.
- A Home padrão e contextual agora retornam exatamente oito ferramentas, sempre as mais recentes.
- Destaques e contextos de aquisição não podem ampliar nem substituir a lista principal da Home.
- Nenhum slug, rota, módulo ou entrada oficial foi removido.

## Continuidade obrigatória para o Lote Cirúrgico 3

- Reler o ZIP original e reaplicar os Lotes Cirúrgicos 1 e 2 em ordem.
- Preservar os 32 `release_order` e a lista da Home limitada a oito.
- Auditar rotas, redirecionamentos, histórico, favoritos, Analytics e integrações da ferramenta combinada.
- Não apagar `ProLaboreProfitDistributionCalculator` sem estratégia de substituição que mantenha 32 ferramentas e compatibilidade pública.


## Resultado do Lote Cirúrgico 3

- A ferramenta combinada de Pró-Labore e Distribuição de Lucros deixou de oferecer um terceiro formulário público duplicado.
- A URL antiga tornou-se uma ponte explícita para `ProLaboreSimulator` e `ProfitDistributionCalculator`.
- O manifesto foi marcado como `Deprecated`; histórico e endpoints antigos permanecem temporariamente por compatibilidade.
- O inventário continua com 32 entradas e classifica o módulo como `compatibility_bridge`.
- A remoção física fica condicionada ao lote de substituição que preserve exatamente 32 ferramentas.


## Resultado do Lote Cirúrgico 4

- O estado foi reconstruído a partir do ZIP original e dos Lotes Cirúrgicos 1, 2 e 3, nesta ordem.
- A ponte temporária foi substituída por um escopo público distinto: `Planejador de Retirada de Sócios`.
- O slug histórico `calculadora-pro-labore-distribuicao-lucros` foi preservado para compatibilidade.
- A ferramenta consolidada voltou a aceitar execuções, mas o seu propósito é planejamento conjunto e comparação de cenários, não repetição dos calculadores especializados.
- As 32 ferramentas permanecem visíveis, implementadas e classificadas exatamente uma vez.
- A Home permanece limitada às 8 ferramentas de maior `release_order`.
- A revisão de sobreposição foi encerrada como `resolved_distinct_planning_scope`.
- Gates de inventário, manifesto, página pública e prontidão de release foram atualizados.

## Evolução do Analytics das Ferramentas — Lote 1 — contrato e infraestrutura

### Resultado

- O estado foi reconstruído a partir do ZIP original e conferido contra README, `CORE_CANDIDATES.md`, relatórios anteriores e inventário executável.
- Foram adicionados os eventos canónicos de jornada: início, mudança de etapa, campo concluído, erro de validação, execução, visualização de resultado, exportação, partilha e abandono.
- O endpoint público de ferramentas passou a aceitar o contrato v1 versionado, mantendo compatibilidade com `tool.calculation.started`, `tool.time.spent` e o campo legado `seconds`.
- Metadados passaram a usar uma lista fechada, validação de tipo e limites; valores de campos e chaves sensíveis não são persistidos.
- Foi criado no Core o contrato opcional `HasAnalyticsJourney`, com DTOs validados para formulários, etapas, campos e ações, além de um registry central.
- Nenhuma ferramenta foi instrumentada neste lote; a declaração e captura concreta começam no lote piloto, após estabilização deste contrato.
- Slugs, inventário, rotas públicas, cálculos e independência dos 32 módulos foram preservados.

### Continuidade obrigatória para o próximo lote de Analytics

- Reconstruir novamente o projeto usando o ZIP original e aplicar este patch antes de iniciar qualquer alteração.
- Ler o README, `CORE_CANDIDATES.md`, este documento e o relatório específico do lote.
- Criar a instrumentação transversal no frontend consumindo somente o contrato central.
- Não tratar qualquer formulário POST como cálculo; cada formulário mensurável deve ser identificado explicitamente.
- Não publicar valores digitados, resultados, documentos ou identificadores pessoais.

## Evolução do Analytics das Ferramentas — Lote 2 — capturador frontend compartilhado

### Resultado

- O estado foi reconstruído a partir do ZIP original com aplicação do Lote 1 antes de qualquer alteração.
- O listener genérico que classificava todo formulário `POST` como cálculo foi removido do layout.
- Foi criado um capturador frontend único no Core, carregado pela entrada global e inativo quando o módulo não declara `HasAnalyticsJourney`.
- O componente compartilhado de página expõe ao navegador somente a configuração validada da jornada correspondente ao slug atual.
- O capturador suporta múltiplos formulários declarados na mesma página, deduplicação por campo/etapa/erro, abandono, envio, resultado, exportação e partilha.
- Valores, ficheiros, resultados e `FormData` nunca são serializados; somente chaves semânticas e métricas agregadas são enviadas.
- O contrato foi ampliado com seletores opcionais de formulário, campo e resultado, mantendo as convenções por `data-analytics-*`.
- Nenhuma ferramenta foi ativada neste lote; a ativação concreta permanece reservada aos pilotos do Lote 3.

### Continuidade obrigatória para o Lote 3 de Analytics

- Reconstruir o projeto usando o ZIP original e aplicar, em ordem, os Lotes 1 e 2.
- Reler README, `CORE_CANDIDATES.md`, inventário, relatórios dos dois lotes e `docs/analytics/FRONTEND-COLLECTOR.md`.
- Escolher pilotos que cubram formulário simples, múltiplos formulários, upload/lote, exportação e jornada com resultado.
- Implementar `HasAnalyticsJourney` somente nos pilotos e adicionar marcadores explícitos às respetivas views.
- Tratar o piloto como gate: não expandir para as 32 ferramentas antes de validar eventos, privacidade e ausência de duplicidade.

## Lote 5 — Inteligência de produto das ferramentas

### Ajuste complementar aplicado

- O dashboard de ferramentas passou a consumir prioritariamente os eventos da jornada declarada: `tool.opened`, `tool.started`, `tool.calculation.executed`, `tool.result.viewed`, `tool.abandoned`, `tool.validation.error`, `tool.field.completed`, `tool.result.exported` e `tool.shared`.
- A taxa de conclusão agora segue o contrato do Lote 5: resultados visualizados divididos por aberturas.
- Foram adicionados comparação com o período anterior, tendência de aberturas e conclusão, ranking de erros, campos problemáticos, etapas de abandono e alertas automáticos.
- O tempo até cálculo passou a apresentar média, mediana e percentil 95, correlacionando eventos pelo `journey_id` e, na ausência dele, pelas identidades técnicas já existentes.
- A tela permite segmentar por ferramenta, categoria, origem e dispositivo; a camada de consulta também suporta navegador, país e idioma.
- Nenhum evento, slug, URL, cálculo de domínio, inventário ou dado digitado foi alterado.

### Continuidade

- Evoluções futuras podem expor a mesma consulta por endpoints JSON internos, caso um consumidor além do dashboard Blade seja criado.
- Alertas persistentes e notificações externas devem reutilizar o sistema de Insights existente, evitando uma segunda infraestrutura paralela.

## Exportação universal — Lote 1 — Core PDF e Excel por bibliotecas

### Resultado

- O estado foi reconstruído a partir do ZIP original e conferido contra README, `CORE_CANDIDATES.md`, inventário executável e relatórios existentes.
- Foram declaradas as dependências oficiais `dompdf/dompdf` e `phpoffice/phpspreadsheet`.
- Foram criados contratos compartilhados para PDF e Excel em `App\Core\Export\Contracts`.
- PDF passa a ser produzido no backend por Dompdf, sem `window.print()`, impressão do navegador ou captura da página.
- Excel passa a ser produzido como `.xlsx` real por PhpSpreadsheet, sem CSV renomeado, HTML ou OOXML artesanal.
- Os DTOs do Core transportam somente o conteúdo do resultado e opções do documento; layout, formulário, menu e demais partes da plataforma não fazem parte da API.
- Os serviços foram registrados no container Laravel por interfaces.
- O legado existente ainda não foi removido porque as ferramentas serão migradas nos lotes seguintes.

### Continuidade obrigatória para o Lote 2

- Reconstruir novamente o projeto usando o ZIP original e aplicar este Lote 1 antes de qualquer alteração.
- Reler README, `CORE_CANDIDATES.md`, este documento e o inventário executável.
- Instalar/atualizar as dependências Composer e gerar `composer.lock` no ambiente com acesso ao repositório de pacotes.
- Migrar somente o primeiro grupo de oito ferramentas para os contratos oficiais.
- Cada ferramenta deve exportar exatamente o mesmo objeto de resultado usado na tela.
- Não utilizar `BrowserPrintExporter`, `window.print()`, `TabularExportService::excel()` ou `TabularExportService::xlsx()` em novas migrações.


## Exportação universal — Lote 2

- Migradas as ferramentas oficiais 1 a 8 para PDF real e Excel real pelo Core.
- Próximo lote: ferramentas oficiais 9 a 16.

## Exportação universal — Lote 3

- O estado foi reconstruído do ZIP original com reaplicação dos Lotes 1 e 2.
- As ferramentas oficiais 9 a 16 receberam download de PDF real e Excel XLSX real.
- Resultados baseados em `ToolCalculationResult` reutilizam `ToolResultExportFactory`.
- Resultados estruturados de Margem/Markup e Rescisão reutilizam `StructuredResultExportFactory` no Core.
- Impressão direta foi retirada dos resultados atuais de Holerite e Admissão.
- Exportações históricas legadas permanecem somente até o lote final de limpeza.

## Exportação universal — Lote Final de Encerramento

- Confirmada a migração das 32 ferramentas oficiais para PDF e Excel por bibliotecas reais e contratos compartilhados.
- Removidos o exportador baseado em impressão, o DTO de impressão, a view de impressão e o script com `window.print()`.
- `TabularExportService` passou a atender exclusivamente CSV; XLS/XLSX são responsabilidade exclusiva de `SpreadsheetExporter`/PhpSpreadsheet.
- Históricos e endpoints de compatibilidade foram migrados sem alteração dos slugs e nomes públicos de rota.
- O exportador administrativo de Analytics também deixou de gerar SpreadsheetML e passou a entregar `.xlsx` real.
- A documentação do lote final e a lista explícita de arquivos removidos acompanham o ZIP incremental.

## Automação completa de qualidade — Lote 1 — Fundação e inventário E2E

### Resultado

- O estado foi reconstruído a partir do ZIP original e conferido contra README, `CORE_CANDIDATES.md`, inventário oficial e relatórios anteriores.
- Foi criado `config/e2e_quality.php`, espelhando exatamente as 32 ferramentas oficiais e acrescentando somente metadados de qualidade.
- Foram definidos os perfis smoke, regressão, completo e exploratório.
- As superfícies de formulário, resultado, download, histórico, upload, lote, geração documental e ações secundárias foram inventariadas.
- Um gate arquitetural impede omissões, duplicações, divergência de slug/módulo e metadados fora do contrato.
- Playwright, ambiente E2E, instrumentação e alterações nas views não foram antecipados.

### Continuidade obrigatória para o Lote 2 da automação E2E

- Reconstruir novamente o projeto com o ZIP original e aplicar este patch incremental.
- Reler README, `CORE_CANDIDATES.md`, este documento, `docs/quality/E2E-AUTOMATION-CONTRACT.md`, o relatório do Lote 1 e os inventários.
- Preservar exatamente as 32 ferramentas e seus slugs.
- Implementar o ambiente E2E isolado antes de instalar ou executar o navegador.
- Não usar dados, credenciais, banco, storage ou integrações reais.

## Automação completa de qualidade — Lote 2 — Ambiente E2E isolado

### Resultado

- O estado foi reconstruído a partir do ZIP original com reaplicação do Lote 1 antes das alterações.
- Foi criado `.env.e2e.example` sem segredos reais e com rede externa desativada por padrão.
- O ambiente utiliza exclusivamente `database/e2e.sqlite`, `storage/app/e2e`, mailer `array`, fila `sync`, cache e sessão `array`.
- Foram criados perfis determinísticos de usuário gratuito, Plus e administrador em um seeder protegido contra execução fora de `APP_ENV=e2e`.
- Os comandos `composer e2e:prepare`, `composer e2e:verify` e `composer e2e:clean` administram o ciclo de vida de forma idempotente e recusam caminhos inseguros.
- O `.gitignore` passou a proteger ambientes, banco, artefatos, dependências, caches e logs locais.
- Um gate arquitetural valida o contrato de isolamento.
- Playwright, browser, seletores visuais, instrumentação e cenários das ferramentas não foram antecipados.

### Continuidade obrigatória para o Lote 3 da automação E2E

- Reconstruir novamente o projeto usando o ZIP original e aplicar, em ordem, os Lotes 1 e 2.
- Reler README, `CORE_CANDIDATES.md`, este documento e os relatórios E2E dos lotes concluídos.
- Preservar exatamente as 32 ferramentas e os slugs públicos.
- Instalar e configurar Playwright sobre o ambiente E2E isolado.
- Não modificar as views com `data-testid` antes do Lote 4.

## Automação E2E — Lote 3 — Fundação Playwright

### Resultado

- O estado foi reconstruído do ZIP original com aplicação dos Lotes E2E 1 e 2.
- Playwright Test `1.62.1` foi registrado como runner oficial do navegador.
- Chromium desktop, servidor Laravel E2E, relatórios HTML/JSON e evidências de falha foram configurados.
- Um piloto abre a Home e `custo-funcionario-clt` sem antecipar cenários de domínio.
- A captura inicial reúne console, page errors, falhas de rede e respostas HTTP com erro.
- O próximo lote deve criar seletores `data-testid` estáveis antes do smoke universal.

## Automação E2E — Lote 4 — Contrato visual e seletores estáveis

### Resultado

- O estado foi reconstruído do ZIP original com aplicação dos Lotes E2E 1, 2 e 3.
- Foi criado um normalizador de identificadores no Core de Qualidade.
- Componentes compartilhados passaram a expor `data-testid` para página, formulário, resultado, validação, campos e downloads.
- O teste piloto Playwright deixou de depender da ordem do primeiro formulário e botão.
- Um gate arquitetural protege o contrato visual sem acoplar CSS, Analytics ou domínio aos seletores E2E.
- O próximo lote deve descobrir automaticamente as 32 ferramentas e executar o smoke universal.

## Resultado da Automação E2E — Lote 5

- O catálogo oficial passou a ser exportado para um manifesto temporário consumido pelo Playwright.
- A exportação confronta `config/product_tools.php` com `config/e2e_quality.php` e falha diante de contagem, slug, módulo ou contrato mínimo divergente.
- O Playwright passou a executar um smoke parametrizado para todas as 32 ferramentas, sem lista manual de slugs no TypeScript.
- Cada ferramenta tem rota, resposta HTTP, raiz visual e painel de formulário verificados, com diagnóstico e resumo JSON anexados.
- O Lote 6 deve criar o motor declarativo de cenários, sem duplicar a descoberta consolidada neste lote.

## Automação E2E — Lote 6 — Motor declarativo de cenários

### Resultado

- O estado foi reconstruído do ZIP original com aplicação sequencial dos Lotes E2E 1 a 5.
- Foi criado um DTO imutável de cenário no Core de Qualidade e um contrato opcional para declaração modular futura.
- `config/e2e_scenarios.php` registra os pilotos sem duplicar a descoberta oficial das ferramentas.
- Um exportador valida slugs, identificadores, ações e expectativas e produz manifesto temporário para o Playwright.
- O executor genérico suporta preenchimento, seleção, checkboxes, cliques, submit e expectativas reutilizáveis.
- A Calculadora de Custo de Funcionário CLT possui cenários piloto válido e inválido.
- O próximo lote deve implementar instrumentação, correlação e logs estruturados; downloads permanecem para o Lote 8.

## Automação E2E — Lote 7 — Instrumentação, correlação e logs

### Resultado

- Foi criada correlação por execução e cenário usando cabeçalhos estáveis enviados pelo Playwright.
- O middleware E2E adiciona os identificadores ao contexto de logging e aos cabeçalhos de resposta, permanecendo inerte fora do ambiente `e2e`.
- O canal `e2e` grava JSON Lines no storage isolado e reutiliza a sanitização de contexto existente.
- Requisições concluídas, exceções e queries lentas passam a produzir registros diagnósticos sem capturar payloads ou bindings sensíveis.
- Os testes de fundação, smoke e cenários anexaram os logs Laravel correspondentes ao relatório do Playwright.
- O arquivo de log é reiniciado no global setup e removido junto com os demais artefatos pelo comando de limpeza do ambiente.

### Continuidade para o Lote 8

- Reaplicar o ZIP original e os Lotes 1 a 7 em ordem antes de qualquer alteração.
- Reutilizar `X-E2E-Run-Id` e `X-E2E-Scenario-Id` na validação de downloads.
- Validar conteúdo real de PDF, XLSX, CSV, DOCX e ZIP sem considerar o clique isolado como sucesso.
- Preservar a política de não registrar payloads, tokens ou documentos completos nos logs.

## Automação E2E — Lote 8 — Validação profunda de downloads

### Resultado

- O estado foi reconstruído do ZIP original com aplicação sequencial dos Lotes E2E 1 a 7.
- O contrato declarativo passou a aceitar downloads tipados por cenário, sem condicionais específicas por ferramenta no Playwright.
- O runner valida nome, extensão, MIME type, tamanho mínimo, assinatura e estrutura interna do arquivo recebido.
- PDF exige cabeçalho e final válidos; XLSX e DOCX exigem estrutura OOXML; CSV exige conteúdo tabular; ZIP exige diretório central legível.
- HTML retornado como arquivo é rejeitado mesmo quando o endpoint responde com sucesso.
- Arquivos e resumos JSON são anexados às evidências e permanecem associados aos logs correlacionados do Lote 7.
- O piloto de `custo-funcionario-clt` cobre PDF e XLSX reais após um cálculo válido.

### Continuidade para o Lote 9

- Reaplicar o ZIP original e os Lotes 1 a 8 em ordem antes de qualquer alteração.
- Reutilizar cenário, observabilidade, storage e validadores existentes.
- Cobrir visitante, usuário gratuito, Plus e administrador sem alterar a filosofia pública de acesso descrita no README.
- Validar proteção de endpoints, persistência, histórico e demais fluxos transversais sem duplicar autenticação ou autorização no Core E2E.

## Automação de qualidade — resultado do Lote 9

- Foram adicionadas sessões Playwright reutilizáveis para os perfis gratuito, Plus e administrador.
- As sessões E2E passaram a ser persistidas exclusivamente em `storage/app/e2e/sessions`.
- Foi criado um manifesto validável de perfis e caminhos protegidos para evitar credenciais e URLs duplicadas nos testes.
- A suíte transversal cobre visitante, usuário gratuito, usuário Plus e administrador.
- Foram incluídas validações de conta autenticada, histórico protegido, bloqueio administrativo, persistência de sessão e rejeição CSRF.
- O comando `composer e2e:browser:access` executa o lote com as verificações acumuladas dos lotes anteriores.

## Automação de qualidade — resultado do Lote 10

- As 32 ferramentas oficiais possuem cenário válido e inválido no manifesto declarativo.
- O executor genérico preenche formulários e cria invalidação determinística sem listas duplicadas no Playwright.
- O gate arquitetural impede perda da cobertura mínima e preserva downloads específicos já existentes.

## Automação de qualidade — resultado do Lote 11

- O estado foi reconstruído do projeto-base com aplicação dos Lotes 9 e 10 antes das alterações.
- Commits executam gate de qualidade e smoke; pull requests e pré-releases executam E2E em quatro shards.
- Dependências e browsers usam caches separados e os artefatos são publicados mesmo quando há falha.
- Resultados Playwright são consolidados em JSON executivo e comparados por fingerprint.
- Falhas são classificadas como novas, conhecidas ou resolvidas; regressões novas bloqueiam a integração.
- O Lote 12 deve adicionar exploração controlada, múltiplos navegadores, responsividade e métricas sem misturar fuzzing ao gate determinístico.

---

# Evolução multi-nicho

Esta trilha evolutiva parte do projeto atual já estabilizado. Ela não substitui
os lotes históricos acima e deve obedecer às mesmas regras de reconstrução,
continuidade, compatibilidade e validação.

## Plano aprovado da evolução multi-nicho

| Lote | Escopo | Estado |
|---:|---|---|
| 1 | Constituição, contratos arquiteturais e regras Global x Vertical | Concluído |
| 2 | Fundação genérica de Vertical e VerticalContext | Concluído |
| 3 | Ferramentas e conteúdo contextual por vertical | Concluído |
| 4 | Home e experiência contextual | Concluído |
| 5 | Serviços globais conscientes de vertical | Concluído |
| 6 | Segunda vertical mínima como prova arquitetural | Concluído |

## Evolução multi-nicho — Resultado do Lote 1

- O `README.md` deixou de definir o Prazzu Tools como uma plataforma exclusiva de Contabilidade.
- Contabilidade foi definida como primeira vertical oficial e implementação de referência, sem alteração do comportamento público atual.
- Foram formalizados os conceitos arquiteturais de `Vertical` e `VerticalContext`.
- Foi definido `VerticalContext = null` como fallback conceitual da experiência geral da plataforma.
- `VerticalContext` e `AcquisitionContext` foram explicitamente separados; o segundo permanece preservado e poderá contribuir para resolver o primeiro em lote futuro.
- Infraestrutura global e dados específicos por vertical foram diferenciados formalmente.
- A regra de não duplicação passou a impedir infraestrutura paralela por nicho quando a diferença puder ser resolvida por contexto, configuração, associação de dados ou composição.
- `docs/ARCHITECTURE.md` foi alinhado à nova constituição.
- Foi adicionado um gate arquitetural básico para proteger essas decisões constitucionais.
- Nenhuma rota, controller, view, ferramenta, manifesto, migration, serviço de runtime ou comportamento público foi alterado neste lote.
- O relatório detalhado do lote está em `docs/MULTI-VERTICAL-LOT-1-CONSTITUTION.md`.

## Continuidade obrigatória para o Lote 2 multi-nicho

1. Reconstruir o estado a partir do ZIP original.
2. Aplicar integralmente o ZIP entregue pelo Lote 1 antes de qualquer nova alteração.
3. Reler `README.md`, `CORE_CANDIDATES.md`, este documento, `docs/ARCHITECTURE.md` e `docs/MULTI-VERTICAL-LOT-1-CONSTITUTION.md`.
4. Conferir novamente `config/product_tools.php` e o estado real dos 32 módulos.
5. Não alterar comportamento público de Contabilidade sem necessidade explícita e compatibilidade documentada.
6. Implementar a fundação genérica de `Vertical` e `VerticalContext`, sem lista fechada de nichos e sem criar infraestrutura paralela.
7. Preservar `AcquisitionContext` como conceito independente e integrá-lo somente como uma possível fonte de resolução.
8. Manter fallback para ausência de vertical válida.



## Evolução multi-nicho — Resultado do Lote 2

- O estado foi reconstruído do ZIP original com aplicação integral do Lote 1 antes das alterações.
- Foi criado `Vertical` como dado de negócio e `VerticalRegistry` como contrato aberto, sem enum ou lista fechada no Core.
- `config/verticals.php` registra `contabilidade` como primeira vertical e padrão atual, preservando a experiência existente.
- `VerticalContext` passou a existir como estado scoped por requisição e aceita `null` como fallback global.
- A resolução é composta por fontes ordenadas: sessão explícita, `AcquisitionContext` mapeado e vertical padrão.
- `AcquisitionContext` não foi alterado nem fundido com `VerticalContext`; ele atua somente como sinal opcional através de mapeamento configurável.
- Slugs inválidos persistidos em sessão são descartados com fallback seguro.
- O middleware transversal compartilha a vertical ativa sem filtrar Home, ferramentas, Blog, recursos, SEO ou Analytics neste lote.
- Nenhum dos 32 módulos, slugs, rotas, manifests ou regras de domínio foi modificado.
- O relatório detalhado está em `docs/MULTI-VERTICAL-LOT-2-FOUNDATION.md`.

## Continuidade obrigatória para o Lote 3 multi-nicho

1. Reconstruir o projeto a partir do ZIP original.
2. Aplicar integralmente, em ordem, os ZIPs dos Lotes 1 e 2.
3. Reler `README.md`, `CORE_CANDIDATES.md`, este documento, `docs/ARCHITECTURE.md` e os relatórios dos Lotes 1 e 2.
4. Conferir novamente `config/product_tools.php`, os 32 módulos e os contratos de catálogo antes de alterar manifests.
5. Tornar Contabilidade explícita nas ferramentas e conteúdos atuais antes de cadastrar uma segunda vertical real.
6. Não transformar categorias como Fiscal/Trabalhista em substitutos de Vertical; domínio e tipo/categoria continuam conceitos distintos.
7. Preservar URLs, slugs, rotas, Analytics, E2E, Auth, Blog engine e infraestrutura compartilhada.
8. Não contextualizar a Home além do necessário para o escopo do Lote 3; a experiência inicial pertence ao Lote 4.


## Evolução multi-nicho — Resultado do Lote 3

- O estado foi reconstruído do ZIP original e conferido contra os Lotes 1 e 2 antes das alterações.
- Os 32 `ToolManifest` passaram a declarar explicitamente `vertical = contabilidade`.
- `config/product_tools.php` permanece inventário executável e agora registra a mesma vertical das 32 ferramentas, protegida por gate arquitetural.
- `ToolCatalog` passou a respeitar `VerticalContext` em catálogo, busca, categorias, recomendações e listagens; `VerticalContext = null` continua exibindo o catálogo global.
- Os recursos existentes em `config/resources.php` passaram a declarar `vertical = contabilidade` e suas listagens respeitam o contexto ativo sem alterar URLs ou views.
- Blog posts e categorias ganharam `vertical_slug`; a migration retrocompatível associa os registros existentes a `contabilidade`, e consultas públicas de Blog respeitam a vertical ativa.
- Novos posts/categorias preservam a vertical já atribuída ou usam a vertical ativa/padrão, sem adicionar interface específica por nicho.
- Categorias de ferramenta continuam sendo taxonomia de apresentação/domínio e não foram transformadas em substitutos de Vertical.
- Home, Analytics, SEO, sitemap, breadcrumbs, Admin contextual e observabilidade não foram antecipados além do necessário para catalogar os dados.
- Nenhum slug público, rota de ferramenta, regra de domínio ou quantidade oficial de módulos foi alterado.
- O relatório detalhado está em `docs/MULTI-VERTICAL-LOT-3-CATALOGING.md`.

## Continuidade obrigatória para o Lote 4 multi-nicho

1. Reconstruir novamente o projeto a partir do ZIP original e reaplicar, em ordem, os Lotes 1, 2 e 3.
2. Reler README, `CORE_CANDIDATES.md`, este documento e os três relatórios multi-vertical.
3. Confirmar que os 32 manifests e o inventário continuam associados a `contabilidade`.
4. Implementar a Home contextual usando `VerticalContext` e as fontes já catalogadas, sem criar Home/controllers paralelos por nicho.
5. Preservar o fallback global quando `VerticalContext = null`.
6. Não antecipar a segunda vertical real nem duplicar Analytics, SEO, Blog, Auth, Admin ou E2E.

## Evolução multi-nicho — Resultado do Lote 4

- O estado foi reconstruído do ZIP original e recebeu integralmente as alterações do Lote 3 antes deste trabalho, preservando os resultados constitucionais e de fundação dos Lotes 1 e 2 já incorporados nesse pacote incremental.
- A rota `/`, `HomeController`, `welcome.blade.php` e o builder compartilhado permanecem únicos; não foram criadas Homes, controllers ou engines por nicho.
- `config/home.php` ganhou uma experiência global para `VerticalContext = null` e bases configuráveis por slug de vertical, mantendo o contrato histórico de Contabilidade por compatibilidade.
- Contabilidade continua apresentando o mesmo Hero, CTA, título e descrição anteriores porque permanece a vertical padrão atual.
- `BuildContextualHome` seleciona primeiro a base correspondente ao `VerticalContext`; uma vertical sem configuração própria cai no fallback global.
- `AcquisitionContext` continua independente e é aplicado somente depois da seleção da base por vertical, preservando personalizações de campanha já existentes.
- Ferramentas e categorias da Home continuam vindo do `ToolCatalog`, portanto herdam a filtragem por vertical implementada no Lote 3 e a regra das 8 maiores `release_order`.
- A view deixou de fixar metadados de Contabilidade e passa a renderizar título e descrição fornecidos pela base da Home, sem introduzir uma engine SEO paralela.
- Foram adicionados testes para o fallback `VerticalContext = null`, para vertical registrada sem Home específica e para impedir controllers de Home específicos por nicho.
- Nenhuma segunda vertical real, Analytics contextual, SEO engine contextual, sitemap, breadcrumbs, Admin ou observabilidade foi antecipada.
- O relatório detalhado está em `docs/MULTI-VERTICAL-LOT-4-CONTEXTUAL-HOME.md`.

## Continuidade obrigatória para o Lote 5 multi-nicho

1. Reconstruir novamente o projeto a partir do ZIP original e reaplicar, em ordem, os lotes multi-vertical já entregues até o Lote 4.
2. Reler README, `CORE_CANDIDATES.md`, este documento e os relatórios dos Lotes 1 a 4.
3. Confirmar que Contabilidade continua sendo a experiência pública padrão e que `VerticalContext = null` renderiza a Home global sem vazamento de conteúdo contábil.
4. Tornar Analytics, SEO, sitemap, breadcrumbs, busca, Admin e observabilidade conscientes de vertical apenas onde aplicável, mantendo uma única infraestrutura global.
5. Não criar serviços `AnalyticsRH`, `SEORH`, `AdminRH` ou equivalentes; vertical deve ser dimensão/contexto.
6. Não cadastrar a segunda vertical real antes do Lote 6, salvo fixtures/configurações estritamente necessárias para teste arquitetural.



## Evolução multi-nicho — Resultado do Lote 5

- O estado foi reconstruído do ZIP original e os pacotes incrementais dos Lotes 3 e 4 foram reaplicados dentro da raiz real `prazzu-tools/` antes das alterações.
- Analytics continua único e ganhou `vertical_slug` em sessões e eventos, resolvido pelo `VerticalContext` ativo antes da captura de Analytics.
- Relatórios estratégicos do Analytics podem filtrar e decompor métricas por vertical sem criar pipelines ou bancos separados.
- O SEO continua compartilhado: `VerticalSeoContext` seleciona defaults globais ou por vertical a partir de configuração, preservando overrides específicos das páginas.
- O sitemap de ferramentas continua derivado do `ToolCatalog`, que já respeita `VerticalContext`; o sitemap do Blog passou a aplicar o mesmo contexto aos posts publicados.
- Breadcrumbs compartilhados podem consultar a vertical ativa; os pontos centrais reutilizáveis de ferramentas, Blog e recursos passaram a expor essa dimensão sem criar componentes por nicho.
- O Admin do Blog continua único e agora permite filtrar postagens/categorias por vertical e selecionar explicitamente a vertical ao editar conteúdo.
- A busca não recebeu uma segunda implementação: ela continua usando `ToolCatalog::search()`, já vertical-aware desde o Lote 3.
- A observabilidade recebe a vertical ativa no contexto compartilhado de logs por requisição.
- Nenhuma segunda vertical real foi cadastrada e nenhum serviço `AnalyticsRH`, `SEORH`, `AdminRH` ou equivalente foi criado.
- O relatório detalhado está em `docs/MULTI-VERTICAL-LOT-5-GLOBAL-SERVICES.md`.

## Continuidade obrigatória para o Lote 6 multi-nicho

1. Reconstruir o projeto a partir do ZIP original e reaplicar, em ordem, todos os pacotes incrementais necessários até o Lote 5.
2. Reler README, `CORE_CANDIDATES.md`, este documento e os relatórios multi-vertical já concluídos.
3. Confirmar que `contabilidade` permanece funcionando e que Analytics, SEO, Blog, Admin, Auth, Billing, E2E e infraestrutura continuam únicos.
4. Registrar uma segunda vertical mínima (preferencialmente RH apenas como prova arquitetural), sem alterar o Core para conhecer seu nome.
5. Criar somente conteúdo/configuração e 1 ou 2 ferramentas mínimas necessárias para provar a expansão.
6. Validar que Home, catálogo, Blog, SEO, Analytics e filtros respeitam a segunda vertical sem vazamento de Contabilidade.
7. Se adicionar a segunda vertical exigir copiar infraestrutura, interromper a expansão funcional e corrigir a generalização antes de continuar.


## Evolução multi-nicho — Resultado do Lote 5

- Analytics, SEO, sitemap, breadcrumbs, busca, Admin e observabilidade permaneceram infraestruturas globais e passaram a carregar/consultar vertical quando aplicável.
- `vertical_slug` passou a integrar sessões/eventos e filtros do Analytics sem criar stacks por nicho.
- SEO e navegação passaram a receber defaults/contexto de vertical com fallback global.
- Blog/Admin e sitemaps passaram a respeitar a vertical ativa sem duplicar controllers ou engines.
- O relatório detalhado está em `docs/MULTI-VERTICAL-LOT-5-GLOBAL-SERVICES.md`.

## Evolução multi-nicho — Resultado do Lote 6

- `rh` foi registrada como segunda vertical por configuração, sem enum fechado ou alteração do Core de verticais.
- Foi criada uma única ferramenta mínima de RH, `calculadora-turnover`, registrada pelo mesmo `ToolRegistry`, catálogo, rotas, Analytics e E2E das ferramentas existentes.
- O inventário oficial passou explicitamente de 32 para 33 ferramentas; os 32 módulos históricos continuam associados a `contabilidade` e a nova ferramenta pertence a `rh`.
- Home e SEO ganharam conteúdo de RH via configuração compartilhada, sem `HomeRH`, `SEORH` ou infraestrutura paralela.
- Foi adicionado conteúdo mínimo de Blog de RH com dois artigos, incluindo conteúdo relacionado à ferramenta, usando a mesma engine e as mesmas tabelas existentes.
- A cobertura E2E declarativa passou a esperar 33 ferramentas e inclui a nova ferramenta por descoberta automática do inventário.
- Analytics permanece único; a ferramenta declara a jornada padrão e herda `vertical_slug = rh` pelo contexto compartilhado.
- O relatório detalhado está em `docs/MULTI-VERTICAL-LOT-6-RH-PROOF.md`.

## Continuidade após a prova multi-nicho

1. Reconstruir sempre o ZIP original e reaplicar os Lotes multi-vertical 3, 4, 5 e 6 em ordem antes de novas alterações.
2. Reler README e todos os relatórios multi-vertical antes de expandir RH ou criar outra vertical.
3. Não promover código específico de RH ao Core sem reutilização transversal comprovada.
4. Usar `rh` como prova de que novas verticais entram por dados, conteúdo e ferramentas, preservando a infraestrutura compartilhada.
5. Antes de qualquer nova expansão, auditar suposições históricas de Contabilidade e gates numéricos que representem o estado atual, sem reescrever documentos históricos.


## Evolução multi-nicho — Resultado do Lote 7 (Consolidação)

- O estado foi reconstruído do ZIP original com os deltas multi-vertical dos Lotes 3, 4, 5 e 6 reaplicados em ordem.
- Foi auditado o projeto inteiro em busca de suposições implícitas de Contabilidade, vazamento de conteúdo entre verticais, números atuais obsoletos e duplicação de infraestrutura.
- O Admin do Blog foi corrigido para montar categorias e ferramentas pela vertical da postagem, validar relações de ferramenta da mesma vertical e manter preview/relacionados isolados por vertical.
- Catálogo, Blog, Recursos e Sobre deixaram de apresentar Contabilidade como identidade global; Contabilidade continua preservada como conteúdo explícito da primeira vertical.
- A documentação arquitetural atual registra 33 ferramentas no total (32 de Contabilidade e 1 de RH), sem reescrever os trechos históricos dos lotes anteriores.
- Foi adicionado um gate arquitetural de consolidação para proteger as superfícies compartilhadas contra regressões multi-vertical.
- A partir deste ponto, novas verticais devem entrar por registro, configuração, conteúdo e ferramentas; não existe um Lote 8 obrigatório da trilha multi-nicho.


## Expansão fiscal pós-consolidação — IRPJ e CSLL no Lucro Presumido

- O inventário oficial é expandido de 33 para 34 ferramentas sem remoção, substituição ou mudança de slug dos módulos existentes.
- Foi criado `PresumedProfitIrpjCsllCalculator`, na vertical `contabilidade`, com cálculo trimestral de IRPJ, adicional de IRPJ e CSLL para o Lucro Presumido no escopo normativo de 2026.
- O módulo usa `Money`, `Percentage`, `IntegerRounding`, `NormativeRuleResolver`, contratos padronizados de cálculo, histórico e exportadores compartilhados do Core; não importa classes internas de outras ferramentas.
- A regra fiscal é versionada como `lucro_presumido.irpj_csll:2026.1.0`, com referências oficiais e casos dourados de cenário típico, fronteira, entrada inválida, arredondamento, não aplicabilidade, transição normativa e regressão.
- O Essencial entrega o cálculo do trimestre e memória fiscal; o Plus adiciona múltiplas atividades, ajuste dos limites acumulados, créditos/retenções confirmados, histórico e PDF/XLSX.
- Os gates numéricos atuais de inventário, Analytics e E2E passam a esperar 34 ferramentas; trechos históricos dos lotes anteriores permanecem como registro de evolução.

## Expansão fiscal — PIS e COFINS 2026

- O inventário oficial é expandido de 34 para 35 ferramentas, preservando todos os módulos e slugs existentes.
- Foi criado `PisCofinsCalculator`, na vertical `contabilidade`, com apuração mensal pelas alíquotas gerais dos regimes cumulativo e não cumulativo.
- O Essencial entrega escolha de regime, base tributável, base agregada de créditos elegíveis no não cumulativo, retenções/compensações confirmadas, saldos e memória normativa completa.
- O Plus adiciona operações adicionais, detalhamento de créditos por operação, comparação cumulativo × não cumulativo, histórico e PDF/XLSX, sem esconder qualquer fórmula necessária ao caso individual.
- A regra `pis_cofins.general_2026:2026.1.0` registra Leis 9.718/1998, 10.637/2002 e 10.833/2003, LC 214/2025 e a orientação oficial da RFB para a transição de 2026.
- Operações monofásicas, alíquota zero, suspensão, importação, benefícios e regimes setoriais permanecem explicitamente fora da inferência automática; o usuário informa a base previamente classificada.
- Os gates atuais de inventário, Analytics e E2E passam a esperar 35 ferramentas.


## Expansão fiscal — Calculadora de ICMS-ST

- O inventário oficial é expandido de 35 para 36 ferramentas.
- `IcmsStCalculator` entra na vertical `contabilidade`, categoria fiscal, release_order 36.
- Essencial: operação interna, MVA informada, ICMS próprio, base ST, ICMS-ST e memória.
- Plus: MVA ajustada, FCP-ST, interestadual, múltiplos itens, PDF/XLSX e histórico.
- O cálculo é paramétrico e exige confirmação de NCM/CEST, sujeição à ST, MVA, alíquotas e regras da UF.


## Expansão fiscal — Calculadora de Retenções na Nota Fiscal

- O inventário oficial é expandido de 36 para 37 ferramentas.
- `InvoiceWithholdingCalculator` entra na vertical `contabilidade`, categoria fiscal, `release_order` 37.
- O Essencial calcula uma nota individual com incidências e alíquotas explicitamente confirmadas.
- O Plus adiciona bases configuráveis, múltiplas notas/serviços, relatório, PDF/XLSX e histórico.
- O domínio é paramétrico e não infere automaticamente a incidência de IRRF, INSS, ISS, PIS/Pasep, Cofins ou CSLL.
- O E2E passa a exigir cobertura válida e inválida para 37 ferramentas e declara os downloads PDF/XLSX desta ferramenta.

## Expansão contábil — Calculadora de Depreciação de Ativos

- O inventário oficial é expandido de 37 para 38 ferramentas.
- `AssetDepreciationCalculator` entra na vertical `contabilidade`, categoria calculadoras, `release_order` 38.
- O Essencial recebe bem, valor e vida útil, usa o método linear e entrega depreciação mensal/anual, valor contábil, projeção anual e memória de cálculo.
- O Plus adiciona vários ativos no mesmo cálculo, métodos de saldos decrescentes e soma dos dígitos, projeção patrimonial consolidada e exportação PDF/XLSX.
- O módulo não cria cadastro patrimonial persistente nem assume função de ERP; a lista de ativos existe somente dentro da simulação atual.
- O cálculo usa `Money` e `IntegerRounding`, considera valor residual zero na versão 1.0.0 e não infere vida útil, enquadramento fiscal ou taxa normativa.
- Os gates atuais de inventário, Analytics e E2E passam a esperar 38 ferramentas, com downloads PDF/XLSX declarados para a nova ferramenta.


## Expansão contábil — Calculadora de Parcelamento Tributário

- O inventário oficial é expandido de 38 para 39 ferramentas.
- `TaxInstallmentCalculator` entra na vertical `contabilidade`, categoria calculadoras, `release_order` 39.
- O Essencial recebe dívida, quantidade de parcelas e taxa mensal de encargos informada pelo usuário e entrega parcela média aproximada, primeira/última parcela, encargos totais, custo final e memória de cálculo.
- O Plus adiciona entrada, comparação de cenários de prazo/encargos, evolução do saldo, cronograma completo e relatório/exportação PDF/XLSX.
- O cálculo é paramétrico por SAC e não embute regras de Receita Federal, PGFN, Simples Nacional, estados ou municípios; condições oficiais devem ser confirmadas pelo usuário.
- Não há cadastro persistente de débitos, negociação ou gestão fiscal; cenários existem somente na simulação atual.
- Os gates de inventário, Analytics e E2E passam a esperar 39 ferramentas, com downloads PDF/XLSX declarados para a nova ferramenta.


## Expansão — Lote 19 — Simulador MEI → Microempresa

### Resultado do Lote 19

- `MeiToMicroenterpriseSimulator` foi criado como módulo fiscal independente, elevando o inventário oficial para 40 ferramentas.
- O Essencial compara faturamento atual/projetado com o teto MEI de referência de 2026 e classifica a projeção em dentro do limite, excesso de até 20% ou excesso superior a 20%.
- O Plus adiciona projeção anual, alíquota efetiva informada pelo usuário, custos empresariais, peso sobre o faturamento, ponto de diluição de custos fixos e exportações PDF/XLSX.
- A ferramenta não presume CNAE, anexo, Fator R ou alíquota do Simples Nacional e não determina automaticamente a data jurídica do desenquadramento.
- Os limites de 2026, 2027 e 2028 foram versionados conforme fontes oficiais vigentes consultadas em 12/08/2026; anos posteriores exigem confirmação normativa.
- Nenhuma capacidade de ERP/persistência foi adicionada e as exportações reutilizam o Core compartilhado.


## Expansão — Lote 20 — ISS, Lucros com/sem Balanço e DAS Retroativo

- O inventário oficial passa de 40 para 43 ferramentas, sem remoção dos módulos existentes.
- `IssCalculator` (`release_order` 41) calcula ISS de forma parametrizada pela alíquota municipal informada; Plus acrescenta retenção, múltiplos serviços/tomadores, cenários municipais, consolidação e PDF/XLSX.
- `ProfitDistributionBalanceSimulator` (`release_order` 42) compara capacidade estimada com balanço (lucro contábil informado) e sem balanço (receita × percentual de referência informado − tributos), com pró-labore, acumulados, planejamento e relatório no Plus. O escopo permanece distinto de `ProfitDistributionCalculator`.
- `RetroactiveDasRegularizationCalculator` (`release_order` 43) reconstitui principal estimado por competência/faturamento/alíquota informada e reutiliza `LateDasRule` para mora. O Plus consolida competências e cria cronograma financeiro; o escopo permanece distinto de `LateDasCalculator`, que parte de um principal já conhecido.
- Analytics, rotas, catálogo, E2E e exportações continuam na infraestrutura compartilhada. Os gates atuais passam a esperar 43 ferramentas.

## Saneamento Prazzu Plus — Lote 1 — Fundação de governança

- O estado anterior foi preservado: nenhum slug, módulo ou benefício de domínio foi removido.
- O sistema de autorização central existente foi mantido como fonte única de decisão Plus.
- `ToolModuleValidator` passou a impedir features genéricas em ferramentas `active`.
- `PlusFeatureReadinessInspector` passou a integrar `tools:check-architecture` e protege novas features Plus contra implementação apenas declarativa.
- `config/plus_feature_governance.php` registra a dívida legada congelada; cada lote de domínio deve remover apenas os itens efetivamente corrigidos.
- `PlusFeatureAccessContractTest` protege transversalmente `Monetized + Free → bloqueado` e `Monetized + Plus → permitido` para todas as features Plus registradas.
- Continuidade: antes do Lote 2 de monetização, reler o ZIP original, este documento, `docs/PLUS-MONETIZATION-LOT-1-FOUNDATION.md`, os lotes entregues e o inventário executável; começar pelo Gerador de Contratos sem recriar a fundação.


## Prazzu Plus — Lote 2 — Gerador de Contratos

- Estado reconstruído a partir do ZIP original + Lote 1 antes das alterações.
- As seis features Plus do `ContractGenerator` foram materializadas: biblioteca ampliada, cláusulas inteligentes, favoritos, preenchimento por perfil empresarial, histórico e comparação de versões.
- Autorização usa apenas `ToolFeatureRequestAuthorizer`/middleware `tool.feature`; histórico, favoritos e perfis reutilizam o Core existente.
- As seis chaves `gerador-de-contratos:*` foram removidas da dívida legada de `plus_feature_governance`.
- Slug e modalidades essenciais foram preservados; versão do módulo passou a `0.6.0`.
- Antes do próximo lote, reconstruir o estado usando ZIP original + Lote 1 + Lote 2, em ordem.

## Prazzu Plus — Lote 3 — Financeiro e retirada de sócios

- Estado reconstruído a partir do ZIP original + Lote 1 + Lote 2 antes das alterações.
- `WorkingCapitalCalculator`: `projections` materializada como projeção de cenário sobre ativos e passivos circulantes, com gate Plus e contrato Free × Plus.
- `CashFlowCalculator`: removida `advanced_productivity`; criada `cash_flow_scenarios` com comparação base/conservador/otimista, gate Plus e contrato Free × Plus.
- `BreakEvenCalculator`: removida `advanced_productivity`; criada `scenario_comparison` para variações de preço e custos, gate Plus e contrato Free × Plus.
- `SalesCommissionCalculator`: removida `advanced_productivity`; criada `batch_sellers` para processamento de 2 a 50 vendedores, gate Plus e contrato Free × Plus.
- `ProLaboreSimulator`: `scenarios` materializada como comparação anual de 2 a 4 valores mensais de pró-labore, protegida pelo gate Plus.
- `ProLaboreProfitDistributionCalculator`: `scenario_planning` passou a proteger a simulação avançada; `history_exports` passou a proteger persistência, consulta de histórico e exportações. Usuário Free monetizado não recebe nem grava histórico Plus.
- As sete chaves corrigidas foram removidas de `config/plus_feature_governance.php::legacy_debt`.
- Nenhum slug público, fórmula Essencial ou módulo oficial foi removido.
- Antes do Lote 4, reconstruir o estado usando ZIP original + Lote 1 + Lote 2 + Lote 3, em ordem, e reler os documentos obrigatórios da raiz.

## Prazzu Plus — Lote 4 — Críticas restantes

- Estado reconstruído do ZIP original com Lotes 1, 2 e 3 reaplicados em ordem.
- As 11 críticas restantes foram saneadas: 10 módulos substituíram `advanced_productivity` por `spreadsheet_export`, usando a exportação compartilhada já existente e mantendo cálculo/PDF Essenciais.
- `TurnoverCalculator` substituiu `advanced_analysis` por `segmented_analysis`, comparação objetiva de 2 a 12 períodos/segmentos usando o mesmo `CalculateTool` Essencial.
- As 11 features genéricas anteriores saíram de `plus_feature_governance.legacy_debt` e agora cumprem o contrato estrito de implementação + gate central + teste Free × Plus.
- Antes do próximo lote, reconstruir obrigatoriamente: ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4.


## Prazzu Plus — Lote 5 — Ajustes parciais A

- Estado reconstruído obrigatoriamente a partir do ZIP original com Lotes 1, 2, 3 e 4 reaplicados em ordem.
- Foram saneadas 14 features em 10 ferramentas parciais: `portfolio_projection`; `multiple_scenarios`; `annual_projection` do Comparador Tributário; `partners`; `document_comparison`; `favorites`; `balance_evolution`; `report` do Parcelamento; `annual_projection`, `business_costs` e `migration_point` do MEI → Microempresa; `monthly_consolidation`; `planning`; e `regularization`.
- Comparador Tributário recebeu comparação concreta de cenários; Distribuição de Lucros recebeu múltiplos sócios; Conversor XML recebeu comparação real entre documentos. As demais correções deste lote ligam funcionalidades já existentes ao gate individual correto.
- As 14 chaves corrigidas saíram de `plus_feature_governance.legacy_debt`; features fora do escopo permanecem congeladas para lotes posteriores.
- Nenhum slug, fórmula Essencial, módulo oficial ou serviço transversal foi recriado.
- Antes do Lote 6, reconstruir obrigatoriamente: ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5.

## Prazzu Plus — Lote 6 — Ajustes parciais B

- Estado reconstruído obrigatoriamente a partir do ZIP original com Lotes 1, 2, 3, 4 e 5 reaplicados em ordem.
- Foram saneadas 23 features em 10 ferramentas parciais restantes: DIFAL/ICMS (`interstate_assist`, `double_base`, `fcp`, `export`); IRPJ/CSLL (`history`); PIS/COFINS (`memory`, `history`); ICMS-ST (`history`); Retenções NF (`memory`, `report`, `history`); Férias (`multiple_employees`); Custo CLT (`branded_report`, `projections`); Salário Líquido (`variable_earnings`, `custom_discounts`, `history`, `export`); Hora Extra (`night`, `dsr`, `reflexes`, `export`); Emissor de Recibos (`custom_branding`).
- Histórico nas ferramentas saneadas reutiliza `ToolRunHistory` e `persistence.auth`; nenhum storage paralelo foi criado.
- Emissor de Recibos materializou personalização real com identidade do escritório e rodapé; Custo CLT passou a proteger identidade do escritório e projeção de 12 meses já existentes.
- Cálculos Essenciais foram preservados: salário básico, hora extra básica, férias individuais e resumos fiscais continuam disponíveis sem depender do Plus.
- As 23 chaves corrigidas saíram de `plus_feature_governance.legacy_debt` e agora cumprem implementação + gate central + teste Free × Plus.
- Antes do próximo lote, reconstruir obrigatoriamente: ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5 → Lote 6.

## Prazzu Plus — Lote 7 — Proteção contra regressão

- Estado reconstruído obrigatoriamente a partir do ZIP original com Lotes 1, 2, 3, 4, 5 e 6 reaplicados em ordem.
- A matriz comercial global passou a fixar 43 ferramentas e 137 features Plus, validando `Monetized + Free → feature.plus_required` e `Monetized + Plus → feature.plus_plan` para cada contrato declarado.
- Foi adicionado contrato de governança para impedir duplicatas, dívida legada inexistente, crescimento silencioso da dívida e retorno de features saneadas ao legado.
- O snapshot consolidado possui 76 contratos legados e 61 contratos sob validação estrita de implementação + gate + testes.
- `PlusFeatureReadinessInspector` agora audita também a consistência global da governança; `tools:check-architecture` retorna 0 violações `tools.plus.*` no estado acumulado.
- O workflow de CI executa explicitamente os dois contratos Prazzu Plus antes do `composer release:check`.
- Antes do Lote 8, reconstruir obrigatoriamente: ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5 → Lote 6 → Lote 7.

## Prazzu Plus — Lote 8 — Auditoria final de monetização

- Estado reconstruído na ordem: ZIP original → Prazzu Plus Lotes 1, 2, 3, 4, 5, 6 e 7.
- Auditoria final confirmou 43 ferramentas, 137 features Plus, 61 contratos saneados e 76 contratos legados congelados.
- `plus_feature_governance.strict_contracts` passou a congelar o conjunto exato das 61 features saneadas; não basta mais preservar apenas a contagem mínima.
- `PlusFeatureReadinessInspector` e `PlusFeatureGovernanceContractTest` detectam desaparecimento, retorno ao legado ou troca silenciosa de qualquer contrato saneado.
- `release_readiness` foi atualizado para `plus_monetization_lot_8_audited`.
- O padrão operacional permanece `launch_free`; ativação de `monetized` deve ser feita no ambiente somente depois de `composer release:check` aprovado no CI oficial.
- Relatório detalhado: `docs/PLUS-MONETIZATION-LOT-8-FINAL-AUDIT.md`.

## Remediação Prazzu Plus — Lote 1 — Fundação de certificação funcional

- Estado reconstruído do ZIP original com reaplicação dos ajustes já entregues nesta sequência antes das alterações.
- Os 137 recursos Plus foram separados em três garantias: acesso comercial, prontidão estrutural e certificação funcional.
- Checksum do catálogo completo e checksum da dívida legada impedem substituições silenciosas que preservem apenas as contagens 137/76.
- `functional_contracts` começa vazio: nenhum benefício foi declarado funcionalmente certificado apenas por possuir rota, middleware ou teste de acesso.
- A dívida funcional inicial é de 137 contratos e não pode crescer; novos benefícios precisam nascer com certificação funcional explícita.
- `CoversPlusFeature` torna o vínculo entre teste comportamental e `slug:feature` explícito e auditável.
- Os próximos lotes devem implementar ou confirmar o benefício, criar teste comportamental marcado, remover a dívida estrutural quando aplicável e adicionar o contrato a `functional_contracts` somente ao final.
- Relatório detalhado: `docs/PLUS-REMEDIATION-LOT-1-FOUNDATION.md`.

## Remediação Prazzu Plus — Lote 2 — ferramentas críticas

- A base foi reconstruída do ZIP original e recebeu, em ordem, os ajustes direcionados anteriores e o Lote 1 de remediação.
- Foram auditados e certificados 19 recursos das cinco ferramentas críticas: Simples Nacional, Honorários Contábeis, Margem/Markup, Rescisão e Validador de CNPJ.
- Cada contrato certificado possui implementação funcional existente, gate Plus individual e teste comportamental marcado com `CoversPlusFeature`.
- A dívida legada caiu de 76 para 57; os contratos estritos subiram de 61 para 80; a certificação funcional passou de 0 para 19.
- O próximo lote deve reconstruir novamente o ZIP original e reaplicar todos os ZIPs anteriores, terminando por este Lote 2, antes de selecionar o próximo grupo.
- Relatório detalhado: `docs/PLUS-REMEDIATION-LOT-2-CRITICALS.md`.

## Remediação Prazzu Plus — Lote 3 — produtividade documental

- A base foi reconstruída na ordem ZIP original → correções direcionadas → Remediação Lote 1 → Remediação Lote 2.
- Foram certificados 13 contratos em Comparador Tributário, Conversor Fiscal XML, Emissor de Recibos, DARF/GPS, Distribuição de Lucros com Balanço e MEI → Microempresa.
- Testes comportamentais comprovam exportações, relatórios, histórico, processamento em lote e perfis salvos; os testes comerciais Free × Plus passaram a enumerar cada feature saneada.
- A dívida legada caiu de 57 para 44; os contratos estritos subiram de 80 para 93; a certificação funcional acumulada subiu de 19 para 32.
- Antes do Lote 4, reconstruir novamente a base e reaplicar este ZIP por último.
- Relatório detalhado: `docs/PLUS-REMEDIATION-LOT-3-DOCUMENTS.md`.

## Remediação Prazzu Plus — Lote 4 — Custo de Funcionário CLT

- O projeto foi reconstruído do ZIP original e recebeu todas as correções e os Lotes 1–3 na ordem obrigatória.
- Foram certificados os 11 contratos legados do módulo: lote, perfis de empresa e funcionário, importações CSV/XLSX, exportações CSV/XLSX, cenários, comparação CLT/PJ/Autônomo, histórico e relatório profissional.
- Os testes comportamentais executam fluxos reais; a matriz comercial enumera individualmente os 11 recursos.
- A dívida legada caiu de 44 para 33; os contratos estritos subiram de 93 para 104; a certificação funcional subiu de 32 para 43.
- Antes do Lote 5, reconstruir novamente a base e aplicar este ZIP por último.
- Relatório detalhado: `docs/PLUS-REMEDIATION-LOT-4-EMPLOYEE-COST.md`.

## Remediação Prazzu Plus — Lote 5 — Apurações fiscais

- Estado reconstruído do ZIP original com todos os ajustes direcionados e Lotes 1–4 reaplicados em ordem.
- Foram certificados 16 benefícios já materializados em três módulos: 6 de IRPJ/CSLL no Lucro Presumido, 5 de PIS/Cofins e 5 de ICMS-ST.
- Os testes comportamentais exercitam periodicidade, múltiplas atividades/operações/itens, cenários, créditos, MVA ajustada, FCP, interestadual e presença das exportações.
- Os contratos Free × Plus passaram a cobrir individualmente todas as chaves desses módulos; rotas de exportação mantêm `tool.feature` e os cálculos avançados mantêm `ToolFeatureRequestAuthorizer`.
- Estado acumulado: 59 contratos funcionais, 120 contratos estritos, 17 contratos legados e 78 contratos de dívida funcional.
- Antes do Lote 6, reconstruir obrigatoriamente: ZIP original → ajustes direcionados → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5.

## Remediação Prazzu Plus — Lote 6 — Encerramento do legado estrutural

- Estado reconstruído na ordem ZIP original → ajustes direcionados → Lotes 1–5.
- Foram auditados e certificados os 17 contratos restantes: 2 no DAS retroativo, 3 em depreciação, 3 em férias, 4 em ISS, 2 em parcelamento tributário e 3 em retenções de nota fiscal.
- Testes comportamentais comprovam consolidação de competências, métodos e múltiplos ativos, histórico/planejamento/exportações de férias, retenção e cenários municipais, comparação de parcelamentos, regras configuráveis e múltiplas notas.
- Todos os testes comerciais desses módulos enumeram cada chave Plus e validam Free bloqueado × Plus permitido em modo monetizado.
- Estado acumulado: 137 contratos estritos, 76 contratos funcionalmente certificados, zero dívida legada estrutural e 61 contratos de dívida funcional.
- Antes do Lote 7, reconstruir obrigatoriamente o ZIP original e reaplicar todos os ajustes e Lotes 1–6 em ordem. O próximo lote deve trabalhar apenas na certificação funcional dos contratos estritos ainda não certificados.
- Relatório detalhado: `docs/PLUS-REMEDIATION-LOT-6-LEGACY-CLOSURE.md`.

## Remediação Prazzu Plus — Lote 7 — Encerramento funcional

- Estado reconstruído obrigatoriamente na ordem ZIP original → ajustes direcionados → Lotes 1–6.
- Foram auditados os 61 contratos estritos ainda pendentes em 38 módulos, cruzando manifesto, implementação, gate central, teste comercial e testes de domínio/fluxo existentes.
- Cada contrato recebeu vínculo explícito com `CoversPlusFeature`; o conjunto de marcadores passa a coincidir exatamente com os 137 benefícios Plus declarados.
- A governança passa a exigir piso funcional de 137 e teto de dívida funcional igual a zero, preservando também 137 contratos estritos e dívida legada estrutural vazia.
- Nenhum controller, fórmula Essencial, rota, página, slug, inventário ou infraestrutura compartilhada foi alterado.
- Antes do Lote 8, reconstruir novamente o ZIP original e reaplicar todos os ajustes e Lotes 1–7 em ordem. O Lote 8 deve executar a auditoria final consolidada e não criar novos benefícios por conveniência.
- Relatório detalhado: `docs/PLUS-REMEDIATION-LOT-7-FUNCTIONAL-CLOSURE.md`.

## Remediação Prazzu Plus — Lote 8 — Auditoria final consolidada

- Estado reconstruído obrigatoriamente na ordem ZIP original → correções direcionadas → Lotes 1–7.
- A auditoria final confirmou 43 ferramentas oficiais e igualdade exata entre os 137 benefícios declarados, contratos estritos e contratos funcionalmente certificados.
- Dívidas estrutural e funcional permanecem zeradas; não há chaves genéricas, contratos duplicados, ausentes ou excedentes nos snapshots.
- A composição de `functional_contracts` recebeu checksum criptográfico próprio, validado tanto pelo inspector arquitetural quanto pelo teste de governança.
- O inventário oficial passou a registrar `plus_remediation_lot_8_audited`; o teste de prontidão foi alinhado à versão real do schema e ao relatório final.
- Nenhuma feature, fórmula, rota pública, página, slug, dependência ou abstração transversal foi adicionada.
- Relatório detalhado: `docs/PLUS-REMEDIATION-LOT-8-FINAL-AUDIT.md`.

## Remediação Prazzu Plus — Lote 9 — Higiene de distribuição

- O estado recebido após os Lotes 1–8 preservava corretamente os 137 contratos Plus, mas o ZIP completo continha `.env`, `.git`, dependências reconstruíveis e uma cópia Laravel aninhada desatualizada.
- `scripts/package-distribution.ps1` passa a excluir automaticamente qualquer raiz Laravel aninhada no primeiro nível.
- `scripts/verify-distribution.php` passa a rejeitar diretórios proibidos e arquivos de ambiente em qualquer profundidade, além de detectar projeto Laravel aninhado.
- `scripts/cleanup-project.ps1` remove a cópia duplicada exata e retira `.env` somente do índice do Git, preservando o arquivo local.
- O workflow de qualidade agora constrói e valida o pacote oficial após o `release:check`.
- Nenhuma ferramenta, contrato Plus, fórmula, rota, página, slug ou dependência foi alterada.
- Relatório detalhado: `docs/PLUS-REMEDIATION-LOT-9-DISTRIBUTION-HARDENING.md`.

## Remediação Prazzu Plus — Lote 10 — Correção de imports PHP

- O `composer release:check` real em Windows identificou 38 imports de `CoversPlusFeature` com barras duplicadas, sintaxe inválida em declarações `use` do PHP.
- Os 38 imports foram normalizados para `App\Core\Quality\Attributes\CoversPlusFeature`.
- Nenhum atributo, teste comportamental, contrato, feature ou regra de acesso foi removido.
- A matriz permanece com 137 marcadores únicos, 137 contratos estritos e 137 contratos funcionalmente certificados.
- O lote não altera código de produção, fórmulas, rotas, páginas, slugs ou dependências.
- Relatório detalhado: `docs/PLUS-REMEDIATION-LOT-10-PHP-IMPORTS.md`.

## Remediação Prazzu Plus — Lote 11 — Gate de qualidade e E2E

- O lint real passou em 1.845 arquivos PHP após o Lote 10.
- O gate seguinte revelou dívida de formatação Pint; ela não foi ocultada nem removida do release. `scripts/finalize-quality.ps1` aplica o formatador oficial e repete `composer release:check`.
- Foram restaurados `scripts/e2e-environment.php`, `scripts/e2e-browser.php` e `scripts/e2e-report-txt.mjs`, todos já referenciados pelo Composer, mas ausentes no estado recebido.
- `package.json` volta a expor `e2e:install` e `e2e:test`, necessários pelo fluxo Composer.
- O ambiente E2E valida isolamento, cria apenas banco/storage dedicados e mantém rede externa desativada.
- Nenhum teste, contrato Plus, fórmula, rota pública, página ou slug foi removido.
- Relatório detalhado: `docs/PLUS-REMEDIATION-LOT-11-QUALITY-E2E.md`.

## Remediação Prazzu Plus — Lote 12 — Timeout E2E e ambiente de distribuição

- A execução real confirmou ambiente isolado, Playwright e Chromium disponíveis, com 34 cenários aprovados antes de o Composer encerrar o processo aos 300 segundos.
- `e2e:browser:test` passa a desativar explicitamente o timeout de processo do Composer; os timeouts individuais do Playwright permanecem ativos.
- O empacotador passa a excluir `.env.e2e` e outros `.env.*` locais, preservando somente `.env.example` e `.env.e2e.example`.
- O validador de distribuição continua bloqueando qualquer ambiente local que escape do staging.
- O finalizador Pint do Lote 11 permanece obrigatório antes do release.
- Nenhum contrato Plus, teste, fórmula, rota, página, slug ou dependência foi alterado.
- Relatório detalhado: `docs/PLUS-REMEDIATION-LOT-12-E2E-TIMEOUT-DISTRIBUTION.md`.

## Crescimento e Retenção — Lote 1 — captura e continuidade pós-resultado

- Estado reconstruído a partir do ZIP original antes das alterações.
- Newsletter passou a persistir inscrições de forma idempotente e a existir também abaixo do breakpoint `xxl`.
- CTA pós-resultado deixou de priorizar venda de Plus e passou a orientar continuidade de acordo com autenticação e histórico real.
- Nenhum cálculo, slug, fórmula, inventário ou gate comercial foi alterado.
- Relatório detalhado: `docs/GROWTH-RETENTION-LOT-1.md`.

## Crescimento e Retenção — Lote 2 — Meu Prazzu

- Estado reconstruído obrigatoriamente na ordem ZIP original → Crescimento e Retenção Lote 1.
- `/minha-conta` passa a apresentar o hub `Meu Prazzu`, sem mudar a URL pública nem a autenticação existente.
- O hub agrega somente metadados de históricos pertencentes ao usuário: contagem de resultados, favoritos, ferramentas com histórico, continuidade e resumos por ferramenta.
- Nenhum `input_payload` ou `result_payload` é usado para renderizar o hub; dados sensíveis dos cálculos continuam privados dentro das superfícies responsáveis.
- O botão `Refazer cálculo` aparece somente quando a ferramenta já possui rota `history.repeat`; caso contrário, o hub oferece retorno seguro à ferramenta sem inventar um contrato de repetição.
- Favoritos reutilizam `tool_run_favorites`; nenhum segundo sistema de favoritos, histórico ou persistência foi criado.
- A consulta agregada permanece local ao `AccountController`; a extração para infraestrutura compartilhada só deve ser considerada se o Lote 3 reutilizar o mesmo contrato na Home.
- Antes do próximo lote de crescimento e retenção, reconstruir obrigatoriamente: ZIP original → Crescimento e Retenção Lote 1 → Crescimento e Retenção Lote 2, relendo README, `CORE_CANDIDATES.md`, este documento e o inventário oficial.
- Relatório detalhado: `docs/GROWTH-RETENTION-LOT-2.md`.

## Crescimento e Retenção — Lote 3 — Continuidade e descoberta

- Estado reconstruído obrigatoriamente na ordem ZIP original → Crescimento e Retenção Lote 1 → Crescimento e Retenção Lote 2.
- A Home autenticada ganhou uma seção separada de continuidade com até quatro ferramentas recentes da vertical ativa, sem tocar na regra das oito ferramentas mais recentes por `release_order`.
- O candidato de continuidade do Lote 2 foi promovido para `UserToolContinuityQuery`; `Meu Prazzu` e Home agora reutilizam a mesma camada segura, sem ler payloads para apresentação.
- Visitantes recebem apenas atalhos temporários da sessão, armazenando exclusivamente slugs em `sessionStorage`; nenhuma persistência anônima foi criada.
- `ToolCatalog::related()` passou a priorizar jornadas editoriais declaradas para as 43 ferramentas e usa a heurística histórica somente como fallback.
- Home contextual de aquisição mantém prioridade e não recebe a seção personalizada enquanto o contexto estiver ativo.
- Nenhum cálculo, slug, fórmula, contrato Plus, inventário ou migration foi alterado.
- Antes do próximo lote de crescimento e retenção, reconstruir obrigatoriamente: ZIP original → Lote 1 → Lote 2 → Lote 3 e reler README, `CORE_CANDIDATES.md`, este documento e os relatórios de crescimento.
- Relatório detalhado: `docs/GROWTH-RETENTION-LOT-3.md`.

## Crescimento e Retenção — Lote 4 — Aquisição, SEO e confiança

- Estado reconstruído obrigatoriamente na ordem ZIP original → Lote 1 → Lote 2 → Lote 3.
- SEO das ferramentas passou a usar a rota e o `ToolCatalog` como fonte compartilhada para canonical, descrição, palavras-chave e vertical, preservando overrides específicos das views.
- As 43 ferramentas oficiais publicam JSON-LD `WebApplication` + `BreadcrumbList` sem inventar avaliações, usuários, preços ou volume de catálogo.
- As 36 views padronizadas ganharam orientação curta de confiança por categoria; as sete views legadas preservaram suas explicações específicas e reutilizam apenas a marcação estruturada.
- O rodapé deixou de exibir `+120`, `+50k`, `100%` e `Sempre atualizado` e passou a comunicar princípios verificáveis do produto, sem prender o layout ao tamanho atual do catálogo.
- Nenhum cálculo, fórmula, slug, migration, feature Plus, `release_order` ou entrada do inventário foi alterado.
- Antes do próximo lote de crescimento e retenção, reconstruir obrigatoriamente: ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 e reler README, `CORE_CANDIDATES.md`, este documento e os quatro relatórios de crescimento.
- Relatório detalhado: `docs/GROWTH-RETENTION-LOT-4.md`.

## Crescimento e Retenção — Lote 5 — Integração, polimento e validação final

- Estado reconstruído obrigatoriamente na ordem ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4.
- Corrigida a Home autenticada para nunca usar rota POST de `history.repeat` dentro de `<a>`; continuidade abre somente uma superfície GET existente.
- Recentes temporários do visitante continuam apenas em `sessionStorage`, agora separados por vertical e sem qualquer payload de cálculo.
- Analytics ganhou `retention.continuity.used` e `retention.related-tool.opened`, com atribuição controlada e dois funis padrão para medir retorno e descoberta até um novo resultado.
- Cadastro iniciado pelo CTA pós-resultado preserva apenas a origem e o slug oficial no evento `account.created`, permitindo medir conversão sem capturar dados pessoais no Analytics.
- Meu Prazzu recebeu atribuição dos atalhos e estado vazio útil de favoritos; reativação de newsletter renova `subscribed_at`.
- Cabeçalhos de continuidade/recomendações e rodapé receberam ajustes responsivos em telas estreitas.
- Nenhum cálculo, fórmula, slug, migration, feature Plus, `release_order` ou item do inventário foi alterado.
- Esta frente de crescimento e retenção encerra seu ciclo no Lote 5. Qualquer continuação deve reconstruir ZIP original → Lotes 1–5 e partir de métricas reais antes de propor nova capacidade.
- Relatório detalhado: `docs/GROWTH-RETENTION-LOT-5.md`.
