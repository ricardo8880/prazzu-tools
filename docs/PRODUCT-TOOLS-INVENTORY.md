# Inventário oficial das ferramentas

A fonte executável deste inventário é `config/product_tools.php`. O README da raiz continua sendo a regra máxima do projeto.

## Estado consolidado no Lote Cirúrgico 1

No estado atual, após a expansão com a Calculadora de Parcelamento Tributário, o projeto possui **39 módulos em `app/Tools`**, todos integrantes de um único inventário oficial. As expansões anteriores permanecem preservadas e `TurnoverCalculator` continua como a ferramenta da vertical `rh`; as demais 38 ferramentas pertencem à vertical `contabilidade`. Não existe mais a classificação de ferramentas “complementares” ou “adicionais” para fins de produto: todas devem permanecer registadas e visíveis na página **Ferramentas**.

Este lote não remove módulos nem altera slugs públicos. A ferramenta combinada `ProLaboreProfitDistributionCalculator` permanece temporariamente no inventário com o estado `implemented`, porque apresenta sobreposição funcional com `ProLaboreSimulator` e `ProfitDistributionCalculator`. A sua retirada só pode ocorrer num lote de migração dedicado, com compatibilidade de rotas, histórico e métricas e sem reduzir a quantidade oficial vigente declarada no inventário.

## Regras obrigatórias do inventário

- O diretório `app/Tools` deve conter a quantidade declarada por `expected_module_count`. O Lote 6 elevou essa quantidade de 32 para 33; a expansão fiscal de IRPJ/CSLL é o lote explícito que eleva a quantidade para 34; a expansão de PIS/COFINS eleva a quantidade para 35; a expansão de ICMS-ST eleva a quantidade para 36; a Calculadora de Retenções na Nota Fiscal eleva a quantidade para 37; a Calculadora de Depreciação de Ativos eleva a quantidade para 38; a Calculadora de Parcelamento Tributário eleva a quantidade para 39; o Simulador MEI → Microempresa eleva a quantidade para 40; Calculadora de ISS, Simulador de Distribuição de Lucros com Balanço × sem Balanço e DAS Retroativo + Regularização do Simples elevaram o catálogo a 43; o ciclo de cobertura das dores contábeis publica os módulos 44 a 50 e leva a quantidade atual para 50.
- Cada módulo deve aparecer exatamente uma vez em `config/product_tools.php`.
- Cada entrada oficial deve declarar uma `vertical` registrada e coincidir com a vertical do respectivo `ToolManifest`.
- IDs, chaves, nomes e slugs do inventário devem ser únicos.
- Cada módulo deve possuir `Tool.php` e estar registado em `config/tools/modules.php`.
- Todas as ferramentas da vertical ativa devem estar visíveis na página de ferramentas; no fallback global, o catálogo pode exibir todas as ferramentas oficiais.
- Nenhum módulo pode ficar escondido por classificação documental paralela.
- Nenhuma ferramenta pode ser apagada apenas por semelhança de nome; remoções exigem auditoria funcional e estratégia de compatibilidade.
- A Home é tratada separadamente: deve mostrar exatamente as 8 ferramentas mais recentes, sem alterar a visibilidade do catálogo completo.

## Estado de implementação

- `implemented`: ferramenta ativa e incluída no inventário consolidado.
- `migration_pending`: ferramenta ativa, mas com sobreposição funcional formalmente identificada e migração pendente.

## Sobreposição funcional controlada

`ProLaboreProfitDistributionCalculator` reúne cálculo de pró-labore e distribuição de lucros, capacidades que também existem nos módulos independentes `ProLaboreSimulator` e `ProfitDistributionCalculator`.

Após o Lote Cirúrgico 3, a experiência pública foi convertida numa ponte para as duas ferramentas independentes. Ela **ainda não é apagada**, porque isso deixaria o projeto com 31 ferramentas e poderia quebrar URL, histórico, favoritos, Analytics ou integrações. O lote de migração deverá decidir e implementar uma substituição realmente distinta antes da remoção definitiva.

## Alteração do catálogo

Toda alteração deve atualizar conjuntamente:

1. `config/product_tools.php`;
2. este documento;
3. `docs/IMPLEMENTATION-LOTS.md`;
4. testes arquiteturais do inventário;
5. qualquer estratégia de redirecionamento ou migração aplicável.


## Resolução da sobreposição funcional — Lote Cirúrgico 4

O módulo `ProLaboreProfitDistributionCalculator` foi mantido com o slug histórico, porém reposicionado como **Planejador de Retirada de Sócios**. O escopo público agora é a composição consolidada da retirada e a comparação de cenários; os módulos `ProLaboreSimulator` e `ProfitDistributionCalculator` continuam responsáveis pelos cálculos especializados isolados. Assim, as 32 entradas históricas permanecem preservadas sem uma ferramenta escondida ou um terceiro formulário com propósito idêntico; o Lote 6 adiciona a 33ª entrada em RH.


## Expansão fiscal — IRPJ e CSLL no Lucro Presumido

- ID oficial: `34`.
- Módulo: `PresumedProfitIrpjCsllCalculator`.
- Slug: `calculadora-irpj-csll-lucro-presumido`.
- Vertical: `contabilidade`.
- Estado: `implemented` / manifesto `beta`.
- Ordem de lançamento: `34`.
- Escopo normativo inicial: ano-calendário de 2026, com regras versionadas e fontes oficiais registradas no próprio módulo.


## Expansão contábil — Parcelamento Tributário

- `TaxInstallmentCalculator` — `calculadora-parcelamento-tributario` — vertical `contabilidade` — implementada — release 39.

## Expansão contábil — MEI → Microempresa

- `MeiToMicroenterpriseSimulator` — `simulador-mei-microempresa` — vertical `contabilidade` — implementada — release 40.


## Expansão fiscal — Ferramentas 41 a 43

| ID | Ferramenta | Slug | Módulo | Vertical | Release |
|---:|---|---|---|---|---:|
| 41 | Calculadora de ISS | `calculadora-iss` | `IssCalculator` | contabilidade | 41 |
| 42 | Simulador de Distribuição de Lucros com Balanço × sem Balanço | `simulador-distribuicao-lucros-balanco` | `ProfitDistributionBalanceSimulator` | contabilidade | 42 |
| 43 | Calculadora de DAS Retroativo + Regularização do Simples | `calculadora-das-retroativo-regularizacao-simples` | `RetroactiveDasRegularizationCalculator` | contabilidade | 43 |

As sobreposições com `ProfitDistributionCalculator` e `LateDasCalculator` foram resolvidas por escopo distinto no inventário executável.

## Cobertura das dores contábeis — Ferramenta 44

| ID | Ferramenta | Slug | Módulo | Vertical | Release |
|---:|---|---|---|---|---:|
| 44 | Analisador de Certificado Digital A1 | `analisador-certificado-digital-a1` | `DigitalCertificateAnalyzer` | contabilidade | 44 |

O inventário oficial passou para **44 ferramentas** no Lote 2. O módulo analisa pontualmente PKCS#12 A1 e não cria gestão, emissão ou persistência de certificados.


## Cobertura das dores contábeis — Ferramentas 45 a 47

| ID | Ferramenta | Slug | Módulo | Vertical | Release |
|---:|---|---|---|---|---:|
| 45 | Consultor e Validador de CFOP | `consultor-validador-cfop` | `CfopAdvisor` | contabilidade | 45 |
| 46 | Validador Fiscal SEFAZ | `validador-fiscal-sefaz` | `SefazFiscalValidator` | contabilidade | 46 |
| 47 | Calculadora de ICMS Próprio | `calculadora-icms-proprio` | `IcmsCalculator` | contabilidade | 47 |

O inventário oficial passa para **47 ferramentas**: 46 em `contabilidade` e 1 em `rh`. CFOP, SEFAZ e ICMS próprio permanecem módulos independentes; o compartilhamento ocorre apenas na referência neutra de CFOP usada também pelo Conversor Fiscal de XML.

## Cobertura das dores contábeis — Ferramentas 48 e 49

| ID | Ferramenta | Slug | Módulo | Vertical | Release |
|---:|---|---|---|---|---:|
| 48 | Calculadora de Lucro Real | `calculadora-lucro-real` | `ActualProfitCalculator` | contabilidade | 48 |
| 49 | Simulador da Reforma Tributária do Consumo | `simulador-reforma-tributaria-consumo` | `TaxReformSimulator` | contabilidade | 49 |

O inventário passa a **49 ferramentas**: 48 em Contabilidade e 1 em RH.


## Cobertura das dores contábeis — Ferramenta 50

| ID | Ferramenta | Slug | Módulo | Vertical | Release |
|---:|---|---|---|---|---:|
| 50 | Simulador Orientativo de ECAD e Direitos Autorais | `simulador-ecad-direitos-autorais` | `EcadRoyaltySimulator` | contabilidade | 50 |

O inventário passa a **50 ferramentas**: 49 em `contabilidade` e 1 em `rh`. A ferramenta ECAD permanece paramétrica: automatiza a matemática do parâmetro confirmado pelo usuário, exibe a UDA oficial de 2026 como referência e não inventa enquadramento, descontos, mínimos, região ou licença.

## Auditoria final das dores contábeis — Lote 6

O ciclo iniciado em `docs/ACCOUNTING-PAINS-LOT-1-AUDIT.md` foi encerrado sem alterar o inventário de 50 ferramentas consolidado no Lote 5. As 13 dores agrupadas estão cobertas por ferramentas oficiais ou, no caso de Nota Fiscal, por composição explícita entre Retenções na Nota Fiscal e Conversor Fiscal de XML.

O gate leve `php scripts/check-accounting-pains.php` verifica o mapa de cobertura, presença dos artefatos mínimos de cada módulo, registro no catálogo, distribuição por vertical, sequência de `release_order`, reutilizações de Core consolidadas e governança Plus. Ele é um gate de auditoria deste compromisso de produto e não substitui `tools:check-architecture`, Analytics, E2E ou a suíte PHPUnit.
