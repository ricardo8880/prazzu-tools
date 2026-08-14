# Simulador Orientativo de ECAD e Direitos Autorais

## Objetivo
Permitir que o usuário confira a matemática de um parâmetro de cobrança de execução pública musical já identificado no Regulamento/Tabela de Preços vigente do Ecad, sem transformar o Prazzu Tools em emissor de licença ou mecanismo de enquadramento automático.

## Funcionamento
A página aceita três critérios: quantidade de UDA, UDA por metro quadrado e percentual sobre base monetária. A referência de UDA 2026 é exibida como R$ 107,31, mas o campo permanece explícito para que outra competência não reutilize silenciosamente um valor vencido.

## Implementação principal
- View: `app/Tools/EcadRoyaltySimulator/Resources/views/index.blade.php`.
- Request: `Presentation/Requests/ExecuteToolRequest.php`.
- Domínio: `Domain/Services/Calculator.php`.
- Rotas: `Routes/web.php`.

## Conteúdos e estados
A página possui estado inicial, erros de validação e resultado com memória de cálculo. O resultado sempre informa que o enquadramento, descontos, mínimos, região e autorização final devem ser confirmados no Ecad.

## Dependências
Não há integração externa em tempo de execução nem persistência. O cálculo reutiliza `Money`, `Percentage`, `IntegerRounding` e a memória de cálculo do Core.

## Regras de manutenção
A UDA e a documentação normativa devem ser revistas quando o Ecad publicar nova vigência. Não automatizar tabela ou categoria sem fonte oficial vigente e casos dourados correspondentes.

## Validação mínima
Cobertura unitária dos três critérios, casos dourados de risco, cenário E2E válido/inválido, catálogo, Analytics, governança Plus e gates arquiteturais.
