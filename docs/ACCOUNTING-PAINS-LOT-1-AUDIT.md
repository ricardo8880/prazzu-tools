# Cobertura das dores contábeis — Lote 1 — Auditoria e arquitetura

## Objetivo

Este lote inicia um ciclo específico para fechar, com escopo real e auditável, as dores contábeis levantadas a partir da referência do produto: Nota Fiscal, CFOP, Certificado Digital, Simples Nacional, Lucro Presumido, Lucro Real, Fator R, Reforma Tributária, PIS/Cofins, SEFAZ, ICMS, ECAD e DIFAL.

O lote é deliberadamente de auditoria e arquitetura. Nenhuma ferramenta oficial é publicada, renomeada, removida ou parcialmente substituída aqui. O catálogo permanece com 43 módulos oficiais. As mudanças funcionais começam somente nos lotes seguintes, com testes, documentação de página, inventário e gates atualizados no mesmo lote de cada publicação.

## Base obrigatória reconstruída

A análise partiu do ZIP original recebido e respeitou a continuidade documentada no projeto. Antes de definir este ciclo foram relidos o `README.md` da raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, o inventário executável `config/product_tools.php`, `docs/PRODUCT-TOOLS-INVENTORY.md` e os relatórios de lotes existentes relevantes à arquitetura, catálogo, monetização, Analytics, multi-vertical, crescimento e retenção.

A tentativa de executar o gate de qualidade no ambiente de análise foi bloqueada antes da suíte por dependências do ambiente: o comando `composer` não está instalado e `scripts/check-platform.php` registrou ausência das extensões PHP `dom`, `mbstring`, `pdo_sqlite`, `xml` e `xmlwriter`. Nenhuma dependência do projeto foi alterada para contornar o ambiente.

## Critério de classificação

- **Completa**: existe ferramenta oficial cujo problema principal cobre o item de forma independente, com cálculo/validação, memória ou explicação e testes no módulo.
- **Parcial**: existe capacidade real no projeto, mas o item aparece apenas como parte de outra ferramenta ou cobre apenas uma subclasse importante do problema.
- **Ausente**: não existe ferramenta ou domínio dedicado que resolva o problema principal.

A classificação não usa simples ocorrência de texto como evidência de cobertura.

## Matriz auditada

| Dor | Estado | Evidência atual | Decisão de produto |
|---|---|---|---|
| Nota Fiscal | **Completa por composição** | `InvoiceWithholdingCalculator` resolve retenções de NF de serviço; `FiscalXmlConverter` lê NF-e/NFC-e 55/65, itens, tributos e totalizadores | Não criar um emissor/ERP. Manter os dois módulos independentes e melhorar validações fiscais nos lotes de CFOP/SEFAZ quando houver ganho concreto. |
| CFOP | **Parcial** | `FiscalXmlConverter` extrai `CFOP` do XML, mas não possui catálogo normativo, busca, explicação, sugestão ou validação semântica | Criar ferramenta dedicada de consulta/validação de CFOP. O XML poderá futuramente consumir um catálogo compartilhado somente quando a reutilização estiver concreta. |
| Certificado Digital | **Ausente** | Não há domínio, rota ou ferramenta dedicada | Criar analisador de certificado A1 `.pfx/.p12`, sem emissão ICP-Brasil e sem persistência de senha/chave privada. A3 e assinatura ficam fora da primeira versão. |
| Simples Nacional | **Completa** | `SimplesNacionalCalculator` é módulo oficial e `FactorRSimulator`/`RetroactiveDasRegularizationCalculator` permanecem independentes por escopo | Preservar. Revisar apenas regressões ou integração normativa necessária pelos novos módulos. |
| Lucro Presumido | **Completa** | `PresumedProfitIrpjCsllCalculator` é ferramenta oficial dedicada; o comparador também possui estimativa própria desacoplada | Preservar a ferramenta dedicada e evitar duplicar regras em novos módulos. |
| Lucro Real | **Parcial** | `TaxRegimeComparator` possui `ActualProfitTaxEstimateProvider` e `ActualProfitTaxRule`, mas apenas como estimativa de comparação e sem apuração dedicada | Criar calculadora dedicada de Lucro Real com escopo de estimativa/apuração assistida bem delimitado; extrair regra normativa somente quando a segunda utilização tornar a reutilização concreta. |
| Fator R | **Completa** | `FactorRSimulator` possui domínio próprio e usa regra normativa versionada do Core | Preservar. |
| Reforma Tributária | **Muito parcial** | Há referências à transição CBS/IBS em `PisCofinsRule` e alertas do `TaxRegimeComparator`, mas não existe ferramenta dedicada | Criar simulador de transição/impacto da Reforma Tributária por competência, com regras versionadas e fontes oficiais. Não embutir toda a reforma dentro de PIS/Cofins. |
| PIS/Cofins | **Completa no escopo atual** | `PisCofinsCalculator` cobre cumulativo/não cumulativo e possui regra normativa 2026 | Preservar cálculo histórico/atual e deixar CBS/IBS para a ferramenta de Reforma Tributária, evitando misturar regimes em uma única calculadora. |
| SEFAZ | **Parcial** | `FiscalXmlConverter` trata XML autorizado e alerta que não substitui conferência oficial; módulos de ICMS referenciam fontes estaduais, mas não há utilitário SEFAZ | Criar utilitário fiscal de diagnóstico/validação documental com camada offline primeiro. Integração on-line com webservices deve ser opcional e só entrar quando houver contrato técnico estável e segurança de certificado resolvida. |
| ICMS | **Parcial** | `IcmsStCalculator` cobre ICMS-ST e `DifalIcmsCalculator` cobre DIFAL/FCP; não há calculadora independente de ICMS próprio para operação comum | Criar calculadora paramétrica de ICMS próprio, mantendo ST e DIFAL como módulos especializados. |
| ECAD | **Ausente** | Não existe domínio dedicado; ocorrências textuais encontradas não representam implementação | Criar simulador orientativo de direitos autorais/ECAD. A primeira versão deve ser paramétrica e transparente; tabelas externas só podem ser automatizadas quando houver fonte vigente e regra de atualização confiável. |
| DIFAL | **Completa** | `DifalIcmsCalculator` é ferramenta oficial dedicada, com FCP, base simples/dupla, memória e exportações | Preservar. |

## Ferramentas novas aprovadas para este ciclo

O Lote 1 aprova escopo, não publicação. Os nomes técnicos abaixo são reservas de implementação e ainda não alteram `config/product_tools.php`:

1. `DigitalCertificateAnalyzer` — Analisador de Certificado Digital A1.
2. `CfopAdvisor` — Consultor e Validador de CFOP.
3. `ActualProfitCalculator` — Calculadora de Lucro Real.
4. `TaxReformSimulator` — Simulador da Reforma Tributária do Consumo.
5. `SefazFiscalValidator` — Validador/Diagnóstico Fiscal SEFAZ, começando por validação offline.
6. `IcmsCalculator` — Calculadora de ICMS Próprio.
7. `EcadRoyaltySimulator` — Simulador Orientativo de ECAD/Direitos Autorais.

Os slugs públicos só devem ser definidos no lote de publicação de cada ferramenta, após verificar colisões, páginas, SEO, rotas e `release_order`.

## Ajustes aprovados em ferramentas existentes

- `FiscalXmlConverter`: preservar o parser seguro atual e, após existir um catálogo de CFOP confiável, adicionar validação semântica opcional sem transformar o módulo em emissor de nota ou cliente SEFAZ.
- `TaxRegimeComparator`: continuar sendo comparador. A nova calculadora de Lucro Real não pode importar classes internas do comparador; a regra normativa equivalente deverá migrar para o Core apenas quando a segunda implementação confirmar equivalência.
- `PisCofinsCalculator`: manter PIS/Cofins como domínio próprio e versionado. Não incorporar um motor completo de CBS/IBS apenas para “cobrir” Reforma Tributária.
- `IcmsStCalculator` e `DifalIcmsCalculator`: manter escopos especializados. A futura `IcmsCalculator` resolve ICMS próprio sem absorver ST ou DIFAL.

## Ordem dos próximos lotes

| Lote | Escopo | Estado |
|---:|---|---|
| 1 | Auditoria definitiva, classificação e arquitetura | **Concluído** |
| 2 | Certificado Digital A1 | Planejado |
| 3 | CFOP + SEFAZ + ICMS próprio, com integração fiscal sem duplicação | Planejado |
| 4 | Lucro Real + Reforma Tributária | Planejado |
| 5 | ECAD + saneamento dirigido das ferramentas fiscais existentes | Planejado |
| 6 | Regressão consolidada, catálogo, documentação e auditoria final das dores | Planejado |

O escopo do Lote 3 poderá publicar até três ferramentas, mas cada uma continuará sendo módulo independente. Compartilhamento só será promovido ao Core quando houver reutilização concreta conforme `CORE_CANDIDATES.md`.

## Restrições obrigatórias para os próximos lotes

1. Antes de cada lote, reconstruir o estado na ordem: **ZIP original → todos os lotes deste ciclo já concluídos**.
2. Reler `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, este relatório e os relatórios deste ciclo já concluídos.
3. Conferir `config/product_tools.php` e o código real; nunca inferir continuidade apenas por documentação.
4. Não criar ERP, CRM, cadastro operacional de clientes, agenda de obrigações ou gestão de certificados. As ferramentas devem continuar pontuais.
5. Não armazenar senha ou chave privada de certificado como conveniência. Qualquer persistência futura exige desenho de segurança explícito e lote próprio.
6. Não integrar webservice estadual por scraping. Integrações externas devem possuir contrato técnico documentado, timeout, falha segura e nenhum vazamento entre verticais.
7. Toda nova página deve nascer com documentação em `docs/pages` e todo lote funcional deve atualizar inventário, módulos, jornadas, SEO/E2E e gates exigidos pelo estado real do projeto.
8. Novas regras tributárias devem ser versionadas por competência, possuir fonte oficial e casos dourados antes da publicação.
9. Bootstrap continua sendo a base visual; CSS específico só quando necessário e em `resources/css/app.css`.
10. O ZIP de entrega de cada lote deve conter somente arquivos criados ou alterados naquele lote, preservando caminhos relativos à raiz do projeto.

## Dependências externas e limites definidos no Lote 1

- **Certificado Digital A1**: a análise do arquivo pode ser implementada com capacidades criptográficas locais do ecossistema PHP/OpenSSL; emissão ICP-Brasil não faz parte do Prazzu Tools.
- **SEFAZ**: a primeira entrega deve resolver valor sem depender de disponibilidade de terceiros. Consulta on-line, autorização ou status em webservice ficam como integração opcional posterior.
- **Reforma Tributária**: regras mudam por competência e devem ser tratadas como normativas versionadas, nunca como constantes eternas.
- **ECAD**: qualquer automatização de tabela precisa de fonte vigente e processo de atualização; sem isso, a ferramenta permanece paramétrica em vez de inventar tarifa.

## Gate de encerramento deste lote

- catálogo oficial: **inalterado em 43 ferramentas**;
- slugs públicos: **inalterados**;
- código de produção: **inalterado**;
- fórmulas existentes: **inalteradas**;
- novas dependências: **nenhuma**;
- relatório de continuidade: **criado**;
- candidatos ao Core: **reavaliados e registrados**;
- próximos lotes: **escopo fechado sem publicação prematura**.

## Continuidade obrigatória para o Lote 2

O Lote 2 deve reconstruir obrigatoriamente o projeto a partir do ZIP original e reaplicar este Lote 1 antes de qualquer alteração. Depois deve reler os documentos obrigatórios da raiz e verificar novamente o inventário real. O trabalho deve se limitar ao `DigitalCertificateAnalyzer` e às capacidades estritamente necessárias à primeira versão A1; assinatura digital, A3, emissão e gestão operacional permanecem fora do escopo.
