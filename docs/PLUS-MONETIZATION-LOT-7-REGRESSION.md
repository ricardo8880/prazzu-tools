# Prazzu Plus — Lote 7 — Proteção contra regressão

## Base reconstruída

Este lote foi executado sobre o estado acumulado reconstruído na ordem obrigatória:

1. ZIP original;
2. Prazzu Plus Lote 1;
3. Prazzu Plus Lote 2;
4. Prazzu Plus Lote 3;
5. Prazzu Plus Lote 4;
6. Prazzu Plus Lote 5;
7. Prazzu Plus Lote 6.

Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos lotes anteriores e `config/product_tools.php`.

## Escopo

O Lote 7 corresponde ao P2 do relatório de monetização: proteção contra regressão de acesso Prazzu Plus em todo o catálogo oficial, sem criar novas funcionalidades de domínio fora dos 38 ajustes dos lotes anteriores.

## Matriz comercial global

`tests/Architecture/PlusFeatureAccessContractTest.php` agora fixa explicitamente o snapshot comercial atual:

- 43 ferramentas oficiais;
- 43 ferramentas com ao menos uma feature Plus;
- 137 features Plus declaradas;
- `Monetized + Free` deve negar com `feature.plus_required`;
- `Monetized + Plus` deve permitir com `feature.plus_plan`;
- nenhuma chave `slug:feature` pode aparecer duplicada.

A execução equivalente usada neste ambiente confirmou `43/43`, `137/137` e zero falhas de acesso.

## Governança da dívida legada

A dívida legada criada no Lote 1 continua representando recursos antigos que não fizeram parte do saneamento funcional pedido no relatório. Ela não é tratada como autorização para adicionar novos recursos declarativos.

O Lote 7 adiciona os seguintes invariantes:

- snapshot do catálogo: 43 ferramentas e 137 features Plus;
- teto da dívida legada: 76 contratos;
- piso de contratos estritos: 61 contratos;
- dívida legada não pode conter duplicatas;
- toda entrada legada precisa corresponder a uma feature Plus realmente declarada;
- features genéricas `advanced_productivity` e `advanced_analysis` continuam proibidas;
- a dívida não pode crescer silenciosamente e features já saneadas não podem retornar ao legado.

`PlusFeatureReadinessInspector` passou a validar esses invariantes dentro de `tools:check-architecture`, além das verificações individuais de implementação, gate e testes dos contratos estritos.

## CI

`.github/workflows/quality.yml` executa explicitamente a matriz Prazzu Plus antes do `composer release:check`:

```text
PlusFeatureAccessContractTest
PlusFeatureGovernanceContractTest
```

O `release:check` continua executando a suíte e a auditoria arquitetural completa, portanto a matriz explícita funciona como falha rápida e não substitui os gates existentes.

## Validações deste lote

- smoke comportamental equivalente à matriz PHPUnit: 43 ferramentas, 137 features, 0 falhas;
- dívida legada: 76 entradas, 0 entradas inexistentes;
- contratos estritos: 61;
- `php artisan tools:check-architecture`: 0 violações `tools.plus.*`;
- permanecem 48 violações arquiteturais históricas fora do escopo de monetização;
- lint PHP dos arquivos alterados aprovado;
- PHPUnit direcionado não pôde iniciar neste ambiente porque o PHP disponível não possui `dom`, `mbstring` e `xmlwriter`; o workflow oficial instala explicitamente essas extensões.

## Continuidade para o Lote 8

Antes do Lote 8, reconstruir obrigatoriamente:

`ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5 → Lote 6 → Lote 7`.

O Lote 8 deve ser a revisão final de monetização: conferir manifesto × implementação × interface, rodar os gates possíveis e preparar o estado para ativação comercial. Não deve reabrir escopo funcional já saneado sem regressão comprovada.
