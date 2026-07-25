# Calculadora de Custo de Funcionário CLT

## Objetivo

Calcular o custo gerencial individual de um funcionário CLT e concentrar, no
mesmo `index`, os fluxos Plus já expostos para lote, importação, comparação de
modalidades e perfis reutilizáveis. O resultado é estimativo e depende das
incidências, CCT, FPAS, RAT/FAP e enquadramento informados.

## Funcionamento

- `GET /ferramentas/custo-funcionario-clt` abre a página pública.
- A simulação individual recebe salário, média variável, benefícios, regime,
  RAT, terceiros e jornada mensal; nome, departamento, cenário e empresa são
  opcionais.
- O cálculo detalha provisões de 13º, férias e terço, FGTS, CPP, RAT,
  terceiros, custo mensal, anual e por hora.
- O resultado individual expõe ações de impressão/PDF, CSV e XLSX, geradas pela
  infraestrutura compartilhada do Core.
- O lote atual apresenta dois blocos manuais de funcionário e aceita até 500
  registros no request; depois exibe consolidado por departamento e projeção
  constante de doze meses.
- A importação permite baixar modelos CSV/XLSX, pré-visualizar até 500 linhas,
  mapear colunas, revisar rejeições e encaminhar as linhas válidas ao lote.
  Prévia e processamento preservam as rotas unificadas, enquanto o middleware
  identifica o formato real e aplica `csv_import` ou `xlsx_import`
  independentemente.
- A comparação CLT × PJ × Autônomo usa premissas explícitas e apresenta líquido
  e custo empresarial.
- A comparação de cenários recebe duas ou mais estruturas, calcula cada lote
  pelo mesmo motor e evidencia o menor custo anual e as diferenças.
- Perfis e histórico dependem de autenticação porque exigem persistência.

## Implementação principal

- **View principal:** `app/Tools/EmployeeCostCalculator/Resources/views/index.blade.php`
- **Grupo de rotas:** prefixo `ferramentas/custo-funcionario-clt`, nomes sob
  `tools.custo-funcionario-clt.*`
- **Controller:** `Presentation/Controllers/ToolController`
- **Form Requests:** `ExecuteToolRequest`, `CalculateBatchRequest`,
  `CompareScenariosRequest`, `CompareEmploymentModelsRequest`,
  `PreviewEmployeeImportRequest`, `ProcessEmployeeImportRequest` e requests de
  perfis
- **Actions:** cálculo individual e em lote, cenários, comparação de modelos,
  importação e histórico em `Application/Actions`
- **Domínio:** `Domain/Services/Calculator`

O `index` estende `layouts.app`, define título, descrição SEO e canonical e usa
`<x-tools.page>`. As rotas Essenciais e Plus utilizam o gate central
`tool.feature`; a importação unificada usa `tool.import-feature` para resolver o
formato antes de delegar ao mesmo gate, e perfis e histórico também usam
`persistence.auth`.

## Conteúdos

- aviso de natureza gerencial;
- formulário individual e memória completa;
- botões de impressão, CSV e XLSX após o cálculo;
- acordeões Plus de lote, importação, cenários, comparação de modalidades e
  perfis;
- estados de resultado em lote, projeção, comparação e importação;
- formulários autenticados de empresa e funcionário;
- FAQ sobre finalidade, PDF e enquadramento.

## Estados

- **Inicial visitante:** cálculo individual e recursos Plus públicos, sem dados
  persistidos.
- **Inicial autenticado:** inclui empresas, funcionários e acesso ao histórico.
- **Resultado individual:** resumo, memória e ações de saída.
- **Resultado em lote:** totais, departamentos, projeção e exportações.
- **Comparação de modalidades:** tabela financeira e ressalva trabalhista.
- **Comparação de cenários:** totais mensal/anual e diferença para o cenário de
  menor custo.
- **Prévia de importação:** cabeçalhos, amostra e mapeamento.
- **Importação processada:** quantidade válida, rejeições e ação para calcular.
- **Mensagens persistentes:** confirmação de perfil, histórico ou exclusão.

## Subfluxos complementares

- `report.blade.php` apresenta impressão/PDF individual e segunda via;
- `report-batch.blade.php` apresenta o relatório consolidado;
- `history/index.blade.php` lista execuções pertencentes à conta;
- `history/show.blade.php` detalha um cálculo individual ou em lote.

Cada subfluxo possui documentação própria em `docs/pages` e reutiliza os
contratos compartilhados de impressão ou histórico.

## Dependências

Usa `Money`, `Percentage`, contratos de cálculo, gate de features, histórico,
exportação tabular, impressão, leitores e armazenamento temporário de
importação, perfis auxiliares do Core, componentes Blade e Bootstrap. A
migration dos perfis precisa estar aplicada para o estado autenticado.

## Manutenção

Não ocultar memória ou parte necessária do cálculo no Plus. Alterações no
resultado persistido devem atualizar `schemaVersion`, política de histórico e
compatibilidade. Importações devem preservar limite, revisão, vínculo do token
ao proprietário, autorização independente de CSV/XLSX e descarte do conteúdo
temporário. Perfis são somente atalhos de entrada, não cadastro operacional de
folha. Alterações nas views de relatório ou histórico devem manter seus
documentos de página e testes sincronizados.

## Validação mínima

- testar GET de visitante e autenticado com layout, SEO e canonical;
- validar cálculo individual nos três regimes, custo por hora e adicionais;
- testar impressão individual e em lote, CSV e XLSX;
- validar lote, totais por departamento e projeção de doze meses;
- testar CSV e XLSX válidos, mapeamento adulterado, linhas inválidas e expiração;
- verificar gate Plus em modo monetizado e acesso integral no lançamento;
- testar propriedade/IDOR de perfis e histórico;
- validar comparação visual de cenários, relatório e páginas de histórico.
