# Calculadora de INSS Patronal

## Objetivo

Estimar CPP, RAT ajustado e contribuições a terceiros incidentes sobre uma base
mensal de folha, exibindo os percentuais efetivamente utilizados.

## Funcionamento

- `GET /ferramentas/inss-patronal` apresenta o formulário público.
- `POST /ferramentas/inss-patronal` valida e executa o cálculo.
- O usuário informa a base da folha, o enquadramento, o RAT ajustado pelo FAP e
  a alíquota de terceiros.
- O domínio trata regime geral, Simples Nacional Anexo IV e demais anexos.
- O resultado separa CPP, RAT, terceiros e total patronal estimado.

## Implementação principal

- **View:** `app/Tools/EmployerInssCalculator/Resources/views/index.blade.php`
- **Rotas:** `tools.inss-patronal.index` e
  `tools.inss-patronal.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A view estende `layouts.app`, define título, meta description e canonical e
utiliza componentes compartilhados de página, formulário e resultado.

## Conteúdos

- base monetária da folha;
- seleção de enquadramento;
- entradas explícitas de RAT/FAP e terceiros;
- quatro métricas patronais;
- memória de cálculo com alíquotas;
- alerta sobre FPAS, CNAE, desoneração e decisões judiciais.

## Estados

- **Inicial:** regime geral, RAT de 1% e terceiros de 5,8% como valores visíveis
  e revisáveis.
- **Inválido:** erros de folha, regime ou percentuais fora dos limites.
- **Calculado:** componentes e memória por rubrica.
- **Folha zerada:** resultado permitido, com rubricas zeradas.

## Dependências

Depende de `Money`, `Percentage`, contratos compartilhados de cálculo,
componentes Blade, Bootstrap e regra interna versionada. Referência registrada:
Lei nº 8.212/1991, art. 22, e orientações oficiais consultadas em 25/07/2026.

## Manutenção

RAT/FAP, FPAS, terceiros, CNAE e desoneração não podem ser inferidos sem dados
adequados; manter as premissas explícitas e o alerta. Revisões normativas devem
atualizar constante de versão, testes e documentação. A feature Plus
`advanced_productivity` ainda não possui rota ou interface; folha completa,
cadastro por empresa, comparação anual, histórico e relatórios não estão
disponíveis.

## Validação mínima

- verificar GET público, layout, metadados e canonical;
- testar os três enquadramentos e conferir alíquotas exibidas;
- testar folha igual a zero e percentuais-limite;
- rejeitar regime desconhecido e percentuais negativos ou excessivos;
- conferir memória, aviso normativo e responsividade.
