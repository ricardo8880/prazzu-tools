# Calculadora de Retenções na Nota Fiscal

## Objetivo

Estimar retenções incidentes em uma nota fiscal de serviços a partir dos parâmetros confirmados pelo profissional, sem inferir automaticamente natureza do serviço, município, regime tributário ou hipótese legal.

## Funcionamento

O usuário informa competência, valor bruto, descrição e quais tributos devem ser considerados. Para cada retenção selecionada, a ferramenta aplica base percentual e alíquota informadas e consolida total retido e líquido estimado.

## Implementação principal

- View: `app/Tools/InvoiceWithholdingCalculator/Resources/views/index.blade.php`.
- Request: `Presentation/Requests/ExecuteToolRequest.php`.
- Domínio: `Domain/Services/Calculator.php`.
- Rotas: `Routes/web.php`.

## Conteúdos e estados

A página apresenta formulário, validação, retenções individualizadas, total retido, líquido, memória de cálculo, premissas e alertas. O resultado depende das incidências e alíquotas confirmadas pelo usuário.

## Dependências

Reutiliza `Money`, `Percentage`, memória de cálculo, histórico e exportação compartilhados. Não consulta prefeitura, Receita Federal ou emissor de nota em tempo de execução.

## Prazzu Plus

Bases e alíquotas personalizadas, múltiplas notas, relatório, exportações e histórico acrescentam produtividade. Uma nota individual permanece resolvida no Essencial.

## Regras de manutenção

Não transformar a ferramenta em emissor de nota fiscal. Exceções por serviço, município, tomador, prestador, regime ou retenção devem continuar explícitas; mudança normativa exige fonte e regressão.

## Validação mínima

Validar cenário sem retenções, uma e múltiplas retenções, bases percentuais, arredondamento, entradas inválidas, histórico/exportação, manifesto, catálogo, Analytics, E2E e governança Plus.
