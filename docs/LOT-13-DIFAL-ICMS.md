# Expansão — Lote 13 — DIFAL / ICMS Interestadual + FCP

Implementação do módulo `DifalIcmsCalculator`, construída a partir do ZIP original com aplicação sequencial dos Lotes 11 e 12.

## Entregue

- DIFAL por base única;
- assistência da alíquota interestadual 7%/12% e 4% quando o usuário confirma a hipótese de importado;
- cenário de base dupla/por dentro quando aplicável;
- FCP parametrizado;
- indicação de responsabilidade conforme destinatário contribuinte/não contribuinte;
- memória normativa, histórico autenticado e exportação compartilhada;
- manifesto, risco, documentação e testes.

## Limites deliberados

A ferramenta não adivinha alíquota interna, FCP, NCM, benefício fiscal, regime especial ou método de base. Esses itens variam por UF, mercadoria/serviço, operação e vigência e precisam ser confirmados na legislação aplicável.

## Fontes normativas

EC 87/2015, LC 190/2022/LC 87/1996, Resolução Senado 22/1989 e Resolução Senado 13/2012, verificadas em 27/07/2026.

## Continuidade

O próximo lote parte de nova extração do ZIP original e aplicação sequencial dos Lotes 11, 12 e 13.

## Verificações executadas

- `php scripts/lint-php.php`: 1.497 arquivos PHP sem erro de sintaxe.
- `php artisan tools:check-architecture`: arquitetura validada sem violações.
- smoke test: SP → BA, base R$ 1.000,00, alíquota interna 18%, FCP 2% => interestadual 7%, DIFAL R$ 110,00, FCP R$ 20,00 e total destino R$ 130,00.
- regressão Lote 11: salário bruto R$ 5.000,00 => líquido R$ 4.498,49 preservado.
- regressão Lote 12: R$ 2.200,00 / divisor 220 / 10h a 50% => hora R$ 10,00, horas extras R$ 150,00 e total R$ 150,00 preservados.
- manifesto do novo módulo instanciado com sucesso, com seis features declaradas.
- perfil de risco fiscal instanciado com sucesso.
- PHPUnit foi invocado, porém o PHP deste ambiente não possui `dom`, `mbstring` e `xmlwriter`; a suíte não pôde iniciar.
- `artisan route:list` também não pôde renderizar a saída neste ambiente por ausência de `DOMDocument`; o arquivo de rotas e o registro do módulo foram validados por lint/arquitetura.
