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
| Certificado Digital | **Ausente** | Não há domínio, rota ou ferramenta dedicada | Criar analisador de certificado A1 `.pfx/.p12`, sem emissão ICP-Brasil e sem persistência de senha/chave privada. A3 e assinatura ficam fora da primeira versão. |
| ECAD | **Ausente** | Não existe domínio dedicado; ocorrências textuais encontradas não representam implementação | Criar simulador orientativo de direitos autorais/ECAD. A primeira versão deve ser paramétrica e transparente; tabelas externas só podem ser automatizadas quando houver fonte vigente e regra de atualização confiável. |

## Ferramentas novas aprovadas para este ciclo

O Lote 1 aprova escopo, não publicação. Os nomes técnicos abaixo são reservas de implementação e ainda não alteram `config/product_tools.php`:

4. `TaxReformSimulator` — Simulador da Reforma Tributária do Consumo.

Os slugs públicos só devem ser definidos no lote de publicação de cada ferramenta, após verificar colisões, páginas, SEO, rotas e `release_order`.

## Ajustes aprovados em ferramentas existentes


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
