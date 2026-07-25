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
