# Detalhes de custo CLT salvo

- **Tipo:** Página de histórico autenticada
- **Implementação principal:** `app/Tools/EmployeeCostCalculator/Resources/views/history/show.blade.php`
- **Rota:** `tools.custo-funcionario-clt.history.show`
- **Robots:** `noindex,nofollow`
- **Status:** ativo

## Objetivo

Exibir uma execução individual ou em lote pertencente ao usuário e oferecer
ações pontuais de reutilização, impressão e exclusão.

## Funcionamento e conteúdos

Resultados individuais mostram resumo e memória. Resultados em lote mostram
totais e funcionários. Reutilizar apenas repõe as entradas no formulário; não
altera nem executa automaticamente um novo cálculo.

## Estados e dependências

A apresentação escolhe entre os schemas `single` e `batch`. Impressão é
oferecida para o resultado individual. O fluxo depende de
`ManageEmployeeCostHistory`, autorização por proprietário e histórico
criptografado do Core.

## Regras de manutenção

- Não recalcular resultados salvos na view.
- Manter ações mutáveis protegidas por CSRF.
- Preservar `noindex`, propriedade do registro e compatibilidade de schema.

## Validação mínima

Validar ambos os tipos de resultado, reutilização, impressão individual,
exclusão, registro inexistente e ID pertencente a outro usuário.
