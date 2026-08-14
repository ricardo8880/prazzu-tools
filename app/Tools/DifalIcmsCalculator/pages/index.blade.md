# Calculadora DIFAL / ICMS Interestadual + FCP

## Objetivo

Simular o diferencial de alíquotas em operação interestadual destinada a consumidor final, com parâmetros confirmados pelo profissional e sem inferir automaticamente tratamento estadual, mercadoria ou benefício fiscal.

## Funcionamento

O usuário informa base, UFs, alíquota interestadual, alíquota interna, FCP quando aplicável, método de base e situação do destinatário. O domínio calcula ICMS interestadual, base de destino, DIFAL, FCP e total conforme o cenário selecionado.

## Implementação principal

- View: `app/Tools/DifalIcmsCalculator/Resources/views/index.blade.php`.
- Request: `Presentation/Requests/ExecuteToolRequest.php`.
- Domínio: `Domain/Services/Calculator.php`.
- Rotas: `Routes/web.php`.

## Conteúdos e estados

A página apresenta cenário, responsabilidade, valores de ICMS/DIFAL/FCP, memória fiscal, fontes e alertas. Origem e destino devem ser distintos; alíquotas e método precisam ser confirmados conforme a operação real.

## Dependências

Usa `Money`, `Percentage`, `IntegerRounding`, memória, histórico e exportação compartilhados. Não substitui GNRE/DARE, Portal Nacional do DIFAL nem consulta à legislação estadual.

## Prazzu Plus

Assistência da alíquota interestadual, base dupla/por dentro, FCP, histórico e exportações ampliam conveniência e cenários. O cálculo básico por parâmetros confirmados permanece Essencial.

## Regras de manutenção

Regras estaduais, FCP, benefícios, NCM e método de base não podem ser presumidos sem fonte versionada. Mudanças legais devem atualizar documentação, fontes e casos dourados.

## Validação mínima

Validar origem/destino, base simples, base dupla, FCP, destinatário contribuinte/não contribuinte, alíquota assistida, entradas inválidas, memória, manifesto, catálogo, E2E e governança Plus.
