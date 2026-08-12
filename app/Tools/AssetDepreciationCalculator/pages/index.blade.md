# Calculadora de Depreciação de Ativos

## Objetivo

Permitir que o usuário calcule a depreciação de um ativo e acompanhe seu valor contábil, com recursos Plus para vários ativos, métodos alternativos, projeção consolidada e exportação.

## Implementação principal

- View: `app/Tools/AssetDepreciationCalculator/Resources/views/index.blade.php`
- Rotas: `app/Tools/AssetDepreciationCalculator/Routes/web.php`
- Controller: `Presentation/Controllers/ToolController.php`
- Domínio: `Domain/Services/Calculator.php`

## Funcionamento

1. O usuário informa bem, valor e vida útil.
2. O método linear é a experiência Essencial padrão.
3. No bloco Plus, pode trocar o método e adicionar outros ativos à simulação.
4. O resultado exibe métricas do bem principal, projeção anual, memória e, quando houver vários ativos, projeção patrimonial consolidada.
5. PDF e XLSX usam os exportadores compartilhados do Core.

## Conteúdos e estados

- estado inicial com formulário;
- erros de validação junto aos campos;
- resultado individual após cálculo válido;
- tabelas Plus quando existirem múltiplos ativos;
- avisos sobre valor residual e escolha de vida útil/método.

## Dependências

- layout e componentes `x-tools.*`;
- Bootstrap;
- `Money`, `IntegerRounding` e infraestrutura compartilhada de exportação.

## Regras de manutenção

- o JavaScript específico deve permanecer limitado a `[data-tool="calculadora-depreciacao-ativos"]`;
- não criar cadastro patrimonial persistente dentro da ferramenta;
- não usar `float` para cálculos monetários;
- não duplicar exportadores;
- preservar o Essencial completo com método linear.

## Validação mínima

- GET da página retorna 200;
- cálculo linear de R$ 12.000 em 5 anos retorna R$ 200/mês e R$ 2.400/ano;
- métodos Plus preservam valor contábil não negativo e encerram em zero;
- múltiplos ativos geram projeção consolidada;
- PDF e XLSX permanecem acessíveis pelas rotas da ferramenta.
