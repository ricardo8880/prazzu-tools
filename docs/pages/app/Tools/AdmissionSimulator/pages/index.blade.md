# Simulador de Admissão

## Objetivo

Simular o custo financeiro inicial e recorrente de uma admissão individual e
fornecer um checklist básico dos documentos e providências normalmente
associados à contratação. A página não substitui a conferência da convenção
coletiva, das exigências de SST ou do processo oficial no eSocial.

## Funcionamento

- `GET /ferramentas/simulador-admissao` abre o formulário público.
- `POST /ferramentas/simulador-admissao` valida os dados e executa a simulação.
- O usuário informa salário, benefícios, percentual mensal de encargos e
  provisões, exame admissional, recrutamento, equipamentos e treinamento.
- O resultado separa custo do primeiro mês, custo mensal recorrente, custos
  únicos e projeção do primeiro ano.
- O checklist pode ser marcado no navegador e o resultado pode ser impresso ou
  salvo em PDF pelo recurso de impressão do browser.

## Implementação principal

- **View:** `app/Tools/AdmissionSimulator/Resources/views/index.blade.php`
- **Rotas:** `tools.simulador-admissao.index` e
  `tools.simulador-admissao.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Orquestração:** `Application/Actions/ShowToolPage` e
  `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A view estende `layouts.app`, define título, descrição SEO e URL canônica da
rota principal, e usa `<x-tools.page>` para manter breadcrumb, apresentação,
tiers e validação consistentes com a plataforma.

## Conteúdos

- apresentação e classificação Essencial/Plus;
- formulário responsivo de custos da contratação;
- resumo com quatro métricas;
- memória dos valores considerados;
- checklist admissional orientativo;
- ação de impressão.

## Estados

- **Inicial:** formulário vazio, com valores opcionais iniciados em zero.
- **Validação inválida:** resumo compartilhado de erros e preservação dos dados.
- **Calculado:** métricas, memória, checklist e impressão ficam visíveis.
- **Impressão:** o navegador aplica a experiência compartilhada de impressão.

## Dependências

Depende de `Money`, `Percentage`, contratos compartilhados de cálculo,
componentes Blade de ferramentas, Bootstrap e do manifesto do próprio módulo.
Não depende de outra ferramenta.

## Manutenção

O percentual de encargos é uma premissa fornecida pelo usuário e não deve ser
apresentado como alíquota legal automática. O checklist deve ser revisto quando
mudarem exigências de eSocial, SST, documentos ou práticas trabalhistas. A
feature Plus `advanced_productivity` está declarada no manifesto, mas ainda não
possui fluxo próprio nesta página; não documentar geração automática,
armazenamento, assinatura ou histórico como disponíveis antes da implementação.

## Validação mínima

- confirmar GET público com HTML completo, layout, metadados e canonical;
- testar cálculo válido e memória com custos únicos iguais a zero e positivos;
- rejeitar salário nulo e percentuais fora do intervalo permitido;
- conferir primeiro mês e primeiro ano contra cálculo manual;
- validar responsividade, checklist e impressão sem perda de conteúdo.
