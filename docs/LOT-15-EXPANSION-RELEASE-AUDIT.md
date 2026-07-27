# Lote 15 — Auditoria final da expansão

## Objetivo

Encerrar a expansão iniciada no Lote 11 com auditoria de regressão, qualidade, catálogo, rotas e distribuição, sem ampliar o escopo funcional das três novas ferramentas.

## Estado de entrada

O lote foi reconstruído obrigatoriamente a partir de:

1. ZIP original;
2. Lote 11 — Salário Líquido;
3. Lote 12 — Hora Extra, Adicional Noturno e DSR;
4. Lote 13 — DIFAL / ICMS / FCP;
5. Lote 14 — integração no catálogo oficial.

O estado esperado e confirmado contém 32 módulos registrados, sendo 23 ferramentas oficiais e 9 módulos adicionais.

## Achados e correções

### 1. Cache de rotas antigo no ZIP original

O ZIP original continha `bootstrap/cache/routes-v7.php` gerado antes da expansão. Com esse arquivo presente, o Laravel carregava a coleção antiga e as rotas dos três novos módulos não apareciam, apesar de os módulos estarem corretamente registrados.

A auditoria removeu o cache antigo durante a reconstrução, validou a coleção fresca e confirmou quatro rotas públicas por ferramenta. Para impedir recorrência em distribuições futuras:

- `scripts/package-distribution.ps1` passa a remover arquivos PHP gerados de `bootstrap/cache` no staging;
- `scripts/verify-distribution.php` passa a rejeitar pacotes que ainda contenham caches PHP em `bootstrap/cache`.

O cache de rotas incluído neste patch foi regenerado apenas para compatibilidade imediata com o ZIP original que o usuário utiliza como base. O pacote de distribuição oficial continua devendo sair sem caches gerados.

### 2. Contrato de qualidade incompleto nos Lotes 12 e 13

`docs/TOOL_QUALITY.md` exige casos dourados concretos e gate de qualidade para ferramentas novas ativas. `NetSalaryCalculator` já atendia ao contrato, mas `OvertimeCalculator` e `DifalIcmsCalculator` possuíam perfil de risco sem a suíte de casos dourados correspondente.

Foram adicionados para ambos:

- `Tests/Fixtures/GoldenCases.php` com cenário típico, fronteira, entrada inválida, arredondamento, não aplicação, transição normativa e regressão;
- `Tests/Unit/ToolQualityContractTest.php`, validando o perfil com `ToolRiskClassifier` e `GoldenCaseSuiteValidator`;
- atualização dos checklists `QUALITY.md`.

Nenhuma fórmula de domínio foi alterada.

## Regressões confirmadas

- Salário Líquido: R$ 5.000,00 bruto em 2026 → R$ 4.498,49 líquido.
- Hora Extra: R$ 2.200,00 / divisor 220 / 10h a 50% → R$ 10,00 por hora e R$ 150,00 de horas extras.
- Hora Extra + noturno + DSR: 7h noturnas e DSR 22/4 → R$ 16,00 de adicional noturno, R$ 30,18 de DSR e R$ 196,18 de total variável no cenário protegido.
- DIFAL SP → BA: base R$ 1.000,00, interna 18%, FCP 2% → interestadual 7%, DIFAL R$ 110,00, FCP R$ 20,00 e total R$ 130,00.
- DIFAL importado SP → MG: base R$ 1.000,00 e interna 18% → interestadual 4% e DIFAL R$ 140,00 quando o usuário confirma o enquadramento aplicável.

## Catálogo, registro e rotas

A auditoria confirmou:

- 32 módulos no `ToolRegistry`;
- 23 ferramentas oficiais e 9 adicionais no inventário;
- todos os 32 classificados exatamente uma vez;
- nenhum slug duplicado;
- todos os manifests com rota principal existente;
- quatro rotas web registradas para cada uma das três ferramentas da expansão quando a coleção é carregada sem cache antigo.

## Checks executados

Aprovados neste ambiente:

- `php scripts/lint-php.php`;
- `php artisan tools:check-architecture`;
- `php artisan analytics:check`;
- validação direta do `ToolRegistry`, inventário e rotas principais;
- validação direta das suítes douradas das três ferramentas;
- smoke/regressão direta dos três cálculos da expansão.

Bloqueados pela plataforma deste ambiente:

- PHPUnit completo;
- Pint;
- `composer release:check` completo;
- geração oficial de caches via comandos Laravel com saída Termwind.

O `scripts/check-platform.php` informa ausência de `dom`, `mbstring`, `pdo_sqlite`, `xml` e `xmlwriter`. A aprovação operacional definitiva em CI continua condicionada a um ambiente que satisfaça `docs/INSTALLATION.md`.

## Resultado

O inventário passa a registrar `release_readiness = expansion_lot_15_audited`.

Isso significa que o código e os contratos da expansão foram auditados dentro das limitações declaradas do ambiente. Não significa que o pipeline de release oficial possa ser dispensado: `composer release:check` continua obrigatório no CI com todas as extensões requeridas.

## Continuidade

Próximas mudanças devem partir do ZIP original e reaplicar os Lotes 11–15 em ordem, ou partir de uma base já consolidada que contenha todos esses lotes. As três ferramentas da expansão passam a ser parte estável do catálogo oficial e seus slugs não devem ser alterados sem migração explícita.
