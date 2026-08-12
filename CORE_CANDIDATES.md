# Candidatos a Componentes Compartilhados do Core Técnico

Este arquivo registra implementações que **podem** se tornar componentes compartilhados do Prazzu Tools no futuro.

Ele não representa dívida técnica nem uma lista automática de tarefas. Seu objetivo é impedir que oportunidades reais de reutilização sejam esquecidas, sem antecipar abstrações antes de existir repetição comprovada.

> Antes de criar uma nova ferramenta, adicionar uma capacidade transversal ou promover código para o Core técnico, leia este arquivo e atualize os candidatos afetados.

## Regra de promoção

Um candidato só deve ser promovido para o Core técnico quando:

1. duas ou mais ferramentas possuírem uma necessidade realmente equivalente;
2. a implementação puder ser compartilhada sem condicionais específicas de cada domínio;
3. a extração reduzir duplicação concreta ou padronizar um comportamento transversal;
4. a API compartilhada puder ser definida a partir de casos reais já implementados;
5. a mudança preservar a independência dos módulos e as regras do README da raiz.

A existência de apenas uma ferramenta usuária não justifica, por si só, uma extração antecipada.

## Status possíveis

- **Aguardando segunda ferramenta**: existe potencial claro, mas somente uma ferramenta utiliza a capacidade.
- **Em observação**: há semelhança entre implementações, porém ainda não existe evidência suficiente de uma abstração comum.
- **Pronto para extrair**: duas ou mais ferramentas possuem repetição estrutural concreta e compatível.
- **Extraído para o Core**: o componente já pertence à infraestrutura compartilhada.
- **Manter no domínio**: a implementação continua específica de uma ferramenta e não deve ser generalizada.

## Candidatos atuais

| Candidato | Implementação atual | Ferramentas que utilizam | Status | Gatilho para reavaliação |
|---|---|---|---|---|
| Conversão de valores monetários por extenso | `App\Core\Money\BrazilianMoneyInWords` | Emissor de Recibos; Gerador de Contratos | **Extraído para o Core** | Reutilizar o componente compartilhado sempre que outra ferramenta precisar converter valores positivos em BRL por extenso. |
| Construção de entrada tipada a partir de formulário validado | `BuildCalculationInput` dentro do Emissor de Recibos | Emissor de Recibos | **Manter no domínio** | Reavaliar somente se outras ferramentas repetirem a mesma estrutura de transformação, e não apenas o conceito genérico de Request → validação → DTO. |
| Geração e download de modelos CSV | Modelo específico da importação em lote do Emissor de Recibos; leitura já usa o Core compartilhado | Emissor de Recibos | **Aguardando segunda ferramenta** | Reavaliar quando outra ferramenta também precisar disponibilizar um arquivo-modelo CSV com resposta, cabeçalhos e regras de download equivalentes. |
| Exportação de documentos Word/DOCX | `ContractDocxExporter` dentro do Gerador de Contratos | Gerador de Contratos | **Aguardando segunda ferramenta** | Reavaliar quando outra ferramenta também precisar gerar DOCX; até lá, manter a composição OpenXML específica no módulo e reutilizar apenas o empacotamento ZIP compartilhado. |
| Armazenamento temporário de payloads entre processamento e exportação | `App\Core\Temporary\Contracts\TemporaryPayloadStore` + implementação em cache | Validador de CNPJ/CPF/IE; Conversor Fiscal XML | **Extraído para o Core** | Reutilizar para resultados efêmeros que precisam sobreviver entre requisições sem transformar login/sessão autenticada em requisito. |

## Componentes já compartilhados relacionados

| Componente | Situação |
|---|---|
| Leitor de CSV | Já pertence ao Core técnico e deve ser reutilizado por importações em lote. |
| Exportação por impressão/PDF | Já utiliza a infraestrutura compartilhada do Core técnico. |
| Empacotamento ZIP simples | Extraído para `App\Core\Export\Services\SimpleZipArchiveBuilder` após uso concreto por Analytics e Gerador de Contratos. O wrapper obsoleto do Analytics foi removido no Lote 1; consumidores devem importar diretamente o serviço do Core. |
| Persistência e histórico | Devem utilizar os mecanismos compartilhados da plataforma, mantendo no módulo somente os dados e regras do domínio. |
| Payloads temporários entre requisições | Extraído para `App\Core\Temporary` e deve ser usado quando processamento/exportação precisam compartilhar dados efêmeros sem depender de autenticação. |
| Perfis auxiliares reutilizáveis de empresa e funcionário | Extraído para `App\Core\ToolProfiles`; os perfis servem somente para reutilizar entradas nas ferramentas e não implementam CRM, folha ou gestão operacional. |
| Validação de valores monetários em formulários | Extraída para `App\Core\Validation\BrazilianMoneyValidator`; as regras Laravel `brazilian_money` e `money_min` reutilizam `Money` e não usam ponto flutuante. |
| Validação de percentuais em formulários | Extraída para `App\Core\Validation\BrazilianPercentageValidator`; as regras Laravel `brazilian_percentage`, `percentage_min` e `percentage_max` reutilizam `Percentage`, aceitam ponto ou vírgula e rejeitam `float` e notação científica. |

## Procedimento obrigatório para assistentes de IA

Ao iniciar qualquer lote ou tarefa:

1. leia o README da raiz;
2. leia este `CORE_CANDIDATES.md`;
3. verifique se a tarefa atual ativa o gatilho de algum candidato;
4. procure repetição concreta no projeto antes de criar uma nova abstração;
5. se um candidato estiver pronto para extração, implemente a promoção somente quando ela fizer parte do escopo ou for necessária para evitar duplicação no trabalho atual;
6. atualize este arquivo sempre que um candidato surgir, mudar de status, for descartado ou for extraído para o Core técnico.

Quando uma nova oportunidade ainda não justificar extração, registre-a aqui em vez de criar antecipadamente um componente genérico.

## Lote 2 — Core normativo e memória de cálculo

Promovidos e consolidados no Core técnico:

- `App\Core\Normative\Contracts\NormativeRuleCatalog` para acesso desacoplado a regras versionadas;
- `App\Core\Normative\Services\InMemoryNormativeRuleCatalog` como implementação inicial validada;
- `App\Core\Normative\NormativeRuleSnapshot` para reprodução histórica;
- `App\Core\Tools\Calculation\Data\CalculationMemory` e `CalculationMemoryStep` para memória de cálculo transversal.

A promoção não inclui alíquotas ou tabelas concretas. Elas continuam pertencendo aos domínios responsáveis e só podem ser compartilhadas quando houver reutilização comprovada, fonte oficial e casos dourados.

## Lote 6 — capacidade tributária normativa confirmada

As regras `FactorRRule`, `LateDasRule` e `TaxRegimeComparisonRule` foram promovidas para `app/Core/Tax/Normative` porque representam metadados normativos e parâmetros reutilizáveis, sem transportar orquestração ou domínio de qualquer ferramenta. Fator R, DAS em Atraso, Comparador de Regimes e o módulo complementar do Simples permanecem independentes.


## Lote 7 — fórmulas financeiras mantidas nos domínios

Capital de Giro, Fluxo de Caixa e Ponto de Equilíbrio usam a mesma infraestrutura transversal de `Money` e `CalculationMemory`, mas suas fórmulas, classificações e premissas não formam uma capacidade de domínio equivalente. Por isso, não foi criado um serviço financeiro genérico no Core. Reavaliar somente quando surgir repetição estrutural real em novas ferramentas, sem condicionais específicas por cálculo.


## Lote 8 — Precificação e comissão

O lote confirmou reutilização real apenas de `Money`, `Percentage`, `IntegerRounding` e `CalculationMemory`. As regras de formação de preço e comissão permaneceram nos seus domínios, pois margem/markup e base comissionável/estornos são políticas comerciais distintas e não justificam promoção ao Core.


## Candidatos observados no Lote 9

- `GeneratedDocumentNotice`: promovido ao Core técnico por uso real nos dois geradores documentais e sem conhecimento de domínio fiscal ou laboral.
- Assinatura digital, verificação externa e selo de autenticidade permanecem fora do Core até existir integração concreta e política de confiança auditável.


## Lote 10 — encerramento do ciclo

A auditoria final não identificou nova duplicação transversal que justificasse extração. Rotas, histórico, exportação e privacidade já possuem infraestrutura ou políticas compartilhadas. A migração do módulo legado combinado é trabalho de compatibilidade e telemetria, não um novo componente de Core.

## Expansão — Lote 11 — IRRF mensal compartilhado

A criação da Calculadora de Salário Líquido confirmou uma segunda necessidade realmente equivalente para a tabela, deduções e redução mensal do IRRF já usadas pelo Simulador de Pró-Labore. A regra foi promovida para `App\Core\Tax\Normative\MonthlyPersonalIncomeTaxRule`, e os dois módulos passaram a consumi-la sem dependência entre ferramentas.

O cálculo previdenciário não foi generalizado: empregado CLT usa faixas progressivas, enquanto pró-labore usa regra de contribuinte individual. Essas regras permanecem em seus domínios até surgir reutilização realmente equivalente.

## Expansão — Lote 12 — Hora Extra, Adicional Noturno e DSR

A análise não identificou capacidade equivalente em uma segunda ferramenta que justifique nova promoção ao Core. `Money`, `Percentage`, `IntegerRounding`, `CalculationMemory`, histórico e exportação já são capacidades compartilhadas e foram reutilizadas. Regras de jornada, adicional noturno e DSR permanecem no domínio `OvertimeCalculator`; reavaliar apenas se outro módulo passar a executar exatamente as mesmas regras.


## Expansão — Lote 13 — DIFAL / ICMS / FCP

A nova ferramenta reutiliza `Money`, `Percentage`, `IntegerRounding`, `CalculationMemory`, histórico e exportação já compartilhados. A regra de alíquota interestadual 7%/12%/4% permanece no domínio `DifalIcmsCalculator`: apesar de ser normativa, nenhuma segunda ferramenta executa hoje a mesma determinação por UF/origem da mercadoria. Alíquotas internas, FCP, benefícios e NCM não foram promovidos nem centralizados porque dependem da operação e da legislação estadual. Reavaliar a regra interestadual apenas quando uma segunda ferramenta precisar exatamente da mesma capacidade.


## Expansão — Lote 14 — integração de catálogo

A promoção das três ferramentas para o catálogo oficial não revelou nova duplicação de domínio ou capacidade transversal. Registro, busca, categorias, relacionados e sitemap já são derivados de `ToolRegistry`/`ToolCatalog`, portanto foram reutilizados sem criar abstrações ou serviços paralelos. Os candidatos dos Lotes 11–13 permanecem com o mesmo status.

## Expansão — Lote 15 — auditoria final

A auditoria final não identificou nova duplicação de domínio que justifique promoção ao Core. O achado de cache de rotas é responsabilidade de empacotamento/distribuição, não uma capacidade de ferramenta. Os casos dourados adicionados a `OvertimeCalculator` e `DifalIcmsCalculator` reutilizam corretamente `ToolRiskClassifier` e `GoldenCaseSuiteValidator`, já compartilhados no Core, sem criar nova abstração.

## Evolução do Analytics das Ferramentas — Lote 1

A telemetria detalhada foi promovida diretamente ao Core técnico por ser uma capacidade transversal aplicável às 32 ferramentas oficiais. O contrato `App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney` permite que cada módulo declare formulários, etapas, campos e ações sem implementar captura ou persistência próprias. `ToolAnalyticsJourneyRegistry` centraliza a descoberta dessas declarações.

O payload público é protegido por `ToolAnalyticsMetadata`, que mantém uma lista fechada de metadados sem valores digitados ou dados pessoais. Os módulos continuam responsáveis apenas pelos identificadores semânticos de sua jornada; captura, validação, normalização, privacidade e armazenamento permanecem no Core.

## Evolução do Analytics — Lote 2 — capturador frontend compartilhado

A captura de jornada foi consolidada em `resources/js/analytics/tool-journey.js`, consumindo exclusivamente o contrato `HasAnalyticsJourney`. A implementação pertence ao Core porque resolve uma necessidade transversal e não contém regras específicas de ferramenta.

O capturador fica inativo para módulos sem declaração e usa seletores/atributos explícitos, evitando a abstração incorreta de que todo formulário `POST` representa um cálculo. Não surgiu novo candidato: adaptações específicas de campos dinâmicos devem ser observadas nos pilotos e só poderão gerar extensão do contrato após repetição concreta.

## Exportação oficial por bibliotecas — Lote 1

A necessidade de PDF e Excel em todas as 32 ferramentas confirmou a promoção definitiva das duas capacidades para o Core técnico:

- `App\Core\Export\Contracts\PdfExporter`, implementado por `DompdfPdfExporter` com `dompdf/dompdf`;
- `App\Core\Export\Contracts\SpreadsheetExporter`, implementado por `PhpSpreadsheetExporter` com `phpoffice/phpspreadsheet`;
- DTOs compartilhados `PdfDocument`, `SpreadsheetDocument` e `SpreadsheetSheet` definem apenas conteúdo, arquivo e opções de formato.

As implementações antigas baseadas em impressão do navegador e construção manual de OOXML permanecem temporariamente apenas para compatibilidade durante a migração. Nenhuma ferramenta nova deve utilizá-las. A remoção ocorrerá no lote final após todas as ferramentas consumirem os contratos oficiais.

## Expansão — PIS e COFINS 2026

A nova `PisCofinsCalculator` confirma reutilização transversal de `Money`, `Percentage`, `CalculationMemory`, histórico e exportação. As alíquotas gerais de PIS/Cofins também aparecem em estimativas do `TaxRegimeComparator`, mas os contextos de domínio ainda são diferentes: o comparador estima regimes empresariais, enquanto a nova ferramenta apura contribuições a partir de bases já classificadas. Não foi criada uma abstração tributária genérica adicional. Reavaliar uma regra normativa compartilhada específica de PIS/Cofins quando outra ferramenta precisar consumir exatamente a mesma seleção temporal, alíquotas e metadados sem condicionais de domínio.


## Lote 20 — reutilização fiscal sem nova abstração

A Calculadora de DAS Retroativo confirmou o uso equivalente de `App\Core\Tax\Normative\LateDasRule` fora da Calculadora de DAS em Atraso, validando a promoção já existente sem exigir novo componente. ISS e comparação de lucros reutilizam `Money`, `Percentage`, `CalculationMemory` e exportação compartilhada; suas fórmulas permanecem específicas de domínio.
