# Gerador de Declaração de Rendimentos

## Objetivo

Montar uma declaração anual de rendimentos pronta para revisão, impressão e
salvamento em PDF pelo navegador, usando dados informados pelo usuário.

## Funcionamento

- `GET /ferramentas/declaracao-rendimentos` abre o formulário público.
- `POST /ferramentas/declaracao-rendimentos` valida e gera o documento.
- São informados beneficiário, documento, fonte pagadora, ano-calendário,
  rendimentos brutos, INSS, IRRF e outras deduções.
- O domínio soma as deduções, impede que ultrapassem o bruto e calcula o líquido.
- A declaração renderizada pode ser impressa pelo botão compartilhado.

## Implementação principal

- **View:** `app/Tools/IncomeStatementGenerator/Resources/views/index.blade.php`
- **Rotas:** `tools.declaracao-rendimentos.index` e
  `tools.declaracao-rendimentos.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A view estende `layouts.app`, define título, meta description e canonical e
utiliza `<x-tools.page>`, painel de formulário, painel de resultado e impressão.

## Conteúdos

- identificação do beneficiário e da fonte pagadora;
- ano-calendário;
- rendimentos e deduções discriminados;
- resumo de bruto, deduções e líquido;
- declaração formatada em card;
- ação de impressão/PDF pelo navegador.

## Estados

- **Inicial:** formulário com ano anterior sugerido e deduções zeradas.
- **Inválido:** erros de identificação, ano ou valores.
- **Calculado:** resumo e documento completo.
- **Deduções acima do bruto:** o domínio rejeita a geração. Atualmente o
  `FormRequest` não antecipa essa regra, portanto o fluxo ainda precisa mapear a
  exceção para erro de formulário.
- **Impressão:** documento gerado é enviado à impressão do browser.

## Dependências

Depende de `Money`, contratos compartilhados de cálculo, política de dados
sensíveis do manifesto, componentes Blade, Bootstrap e impressão do navegador.
Os valores devem coincidir com folha, contabilidade e obrigações oficiais.

## Manutenção

Não calcular automaticamente dados fiscais sem base normativa e entradas
adequadas. Nome e documento são dados sensíveis e devem continuar protegidos em
qualquer persistência futura. A feature Plus `advanced_productivity` não possui
fluxo: biblioteca de modelos, assinatura, armazenamento, emissão em lote e
histórico ainda não estão disponíveis.

## Validação mínima

- validar GET público, HTML completo, SEO e canonical;
- gerar declaração com e sem deduções;
- rejeitar deduções superiores ao rendimento como erro controlado;
- testar ano e campos obrigatórios nos limites;
- conferir escape de textos, valores, impressão e responsividade.
