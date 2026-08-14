# Cobertura das dores contábeis — Lote 5 — ECAD + saneamento fiscal dirigido

## Estado reconstruído

O trabalho partiu novamente do ZIP original e reaplicou, na ordem, os Lotes 1, 2, 3 e 4 deste ciclo. Antes da alteração foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos quatro lotes anteriores, `config/product_tools.php`, `config/tools/modules.php` e o inventário documental.

## ECAD

Foi criado `EcadRoyaltySimulator`, slug público `simulador-ecad-direitos-autorais`, release 50. O módulo resolve a conferência matemática de um parâmetro já identificado pelo usuário no Regulamento/Tabela de Preços do Ecad.

A primeira versão aceita:

- quantidade de UDA × valor da UDA;
- área × UDA por m² × valor da UDA;
- percentual × base monetária informada.

A referência oficial corrente no lote é UDA de R$ 107,31, vigente até dezembro de 2026, conforme Regulamento de Arrecadação do Ecad revisado em 12/01/2026. O valor aparece explicitamente no formulário, e não como constante invisível, para evitar que uma competência futura reutilize uma referência vencida sem percepção do usuário.

O módulo não escolhe categoria, região socioeconômica, grau de utilização musical, forma de utilização, desconto, garantia mínima ou outra condição de enquadramento. Também não emite licença, boleto, autorização e não chama serviços externos. O resultado é orientativo e obriga conferência final com o Ecad.

## Prazzu Plus

`period_projection` projeta o mesmo valor por até 60 períodos. Essa conveniência não altera o cálculo principal e não presume reajustes ou mudança de enquadramento. O contrato possui gate central e teste comportamental marcado.

## Saneamento fiscal dirigido

A reconstrução revelou uma inconsistência objetiva deixada após o Lote 4: `config/product_tools.php` continha as ferramentas 48 e 49, mas `expected_module_count` permanecia em 47 e `release_readiness` ainda apontava para o Lote 3. Vários testes arquiteturais de estado corrente também estavam fixados em 47, enquanto o E2E já declarava 49.

O lote saneia esses contratos para o estado atual de 50 ferramentas, 49 de Contabilidade e 1 de RH. A ordem de lançamento agora é completa de 1 a 50 e as oito ferramentas mais recentes passam a começar por ECAD, Reforma Tributária e Lucro Real.

As ferramentas já classificadas como completas no Lote 1 — Nota Fiscal por composição, Simples Nacional, Lucro Presumido, Fator R, PIS/Cofins e DIFAL — foram preservadas. Não houve alteração de fórmula sem regressão comprovada nem fusão de módulos especializados.

## Qualidade e atualização normativa

O ECAD possui perfil de risco financeiro/normativo alto, casos dourados de cenário comum, fronteira, entrada inválida, arredondamento, não aplicação, transição normativa e regressão, além de documentação normativa própria. A atualização futura da UDA deve ocorrer em lote explícito com atualização simultânea da interface, documentação e casos dourados.

## Continuidade para o Lote 6

Reconstruir obrigatoriamente ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5. O Lote 6 deverá executar regressão consolidada, revisar as 13 dores agrupadas e confirmar catálogo, documentação, Analytics, SEO/E2E, governança Plus, rotas e arquitetura. Não ampliar escopo funcional sem evidência de lacuna.

## Gates executados neste ambiente

- `php scripts/lint-php.php`: 1.966 arquivos PHP sem erro de sintaxe.
- `php artisan tools:check-architecture`: sem violações.
- `php artisan analytics:check`: catálogo de Analytics válido.
- `php scripts/e2e-tool-scenarios.php check`: 100 cenários válidos cobrindo 50 ferramentas, com cenário válido e inválido para cada uma.
- Catálogo executável: 50 oficiais, `expected_module_count = 50`, 50 diretórios em `app/Tools` e `release_order` completo de 1 a 50.
- Governança Plus: 144 contratos estritos e 144 funcionais; checksums consistentes e dívida zero.
- Casos dourados do ECAD: suíte validada pelo classificador de risco.
- Smoke direto do domínio: 3 UDA = R$ 321,93; 100 m² × 0,012 UDA/m² = R$ 128,77; 2,5% de R$ 10.000 = R$ 250,00 e projeção de 12 períodos = R$ 3.000,00.

O pipeline completo continua impedido por limitações do PHP disponível neste ambiente: faltam `dom`, `mbstring`, `pdo_sqlite`, `xml` e `xmlwriter`. Nenhuma dependência, gate ou regra do projeto foi afrouxada para contornar essa limitação.
