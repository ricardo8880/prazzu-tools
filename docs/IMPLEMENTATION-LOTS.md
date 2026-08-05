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
