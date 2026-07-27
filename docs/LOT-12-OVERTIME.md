# Expansão — Lote 12 — Hora Extra, Adicional Noturno e DSR

Implementação completa do módulo `OvertimeCalculator`, construída sobre o ZIP original + Lote 11.

## Entregue

- hora normal e horas extras 50%, 100% e personalizadas;
- adicional noturno urbano com conversão da hora reduzida;
- hora extra noturna;
- DSR parametrizado por dias úteis e repousos/feriados;
- projeções Plus de 13º, férias + 1/3 e FGTS;
- memória normativa, histórico autenticado e exportação compartilhada;
- manifesto, risco, documentação e testes.

## Limites

Não decide CCT/ACT, banco de horas, 12x36, habitualidade, categorias especiais, trabalho rural/doméstico ou calendário aplicável. Esses elementos devem ser previamente determinados pelo profissional.

## Verificações executadas

- `php scripts/lint-php.php`: 1.478 arquivos PHP sem erro de sintaxe.
- `php artisan tools:check-architecture`: sem violações.
- smoke test: salário R$ 2.200,00 / divisor 220 / 10h a 50% => hora normal R$ 10,00 e horas extras R$ 150,00.
- regressão direta do Lote 11: salário R$ 5.000,00 => líquido R$ 4.498,49 preservado.
- manifesto do novo módulo instanciado com sucesso e 6 features declaradas.
- PHPUnit não executado porque o PHP disponível não possui `dom`, `mbstring` e `xmlwriter`.

## Continuidade

O próximo lote deve ser iniciado a partir de nova extração do ZIP original, com aplicação sequencial do Lote 11 e deste Lote 12 antes de qualquer alteração.
