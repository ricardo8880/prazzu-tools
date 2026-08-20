# Ativação das ferramentas Beta — Lote 2 — calculadoras de menor risco

## Escopo

Este lote continua a frente de ativação real das 37 ferramentas `beta`. A base foi reconstruída obrigatoriamente em **projeto atual enviado pelo usuário → Beta Activation Lote 1** antes das alterações.

O objetivo não foi promover status. O trabalho concentrou-se em seis calculadoras de menor risco para torná-las mais úteis no uso profissional, substituir evidência sintética por casos numéricos concretos e fechar os artefatos de qualidade exigidos pelo framework vigente.

Ferramentas tratadas:

- `WorkingCapitalCalculator`;
- `CashFlowCalculator`;
- `BreakEvenCalculator`;
- `SalesCommissionCalculator`;
- `SalaryAdjustmentCalculator`;
- `AssetDepreciationCalculator`.

A autoridade máxima permanece o `README.md` da raiz. Também foram preservadas as regras de `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `docs/TOOL_QUALITY.md` e `config/product_tools.php`.

## Melhorias de utilidade

### Capital de Giro

- O resultado passou a distinguir explicitamente **déficit de financiamento** e **folga de capital circulante**.
- Quando o CCL cobre a necessidade operacional, a ferramenta não mostra mais apenas um déficit igual a zero: informa a folga disponível e emite aviso contextual.
- Histórico e memória foram atualizados para carregar o novo indicador sem perder os campos existentes.

### Fluxo de Caixa

- O resultado passa a alertar explicitamente quando o saldo final projetado é negativo.
- Também alerta quando a geração operacional do período é negativa, mesmo que o saldo inicial ainda sustente o caixa.
- A fórmula principal permanece a mesma; a melhoria é de interpretação profissional e prevenção de leitura otimista do resultado.

### Ponto de Equilíbrio

- A ferramenta continua arredondando a quantidade necessária para a primeira unidade inteira capaz de cobrir os custos fixos.
- Agora expõe a **folga de cobertura** produzida por esse arredondamento, tornando auditável a diferença entre o ponto matemático e a unidade inteira praticável.
- Custos fixos iguais a zero passam a produzir 0 unidades com explicação explícita, em vez de depender apenas do comportamento matemático implícito.

### Comissão de Vendedores

- Meta e bônus deixaram de ser entradas artificialmente obrigatórias no cenário individual.
- O usuário pode calcular somente comissão-base sem preencher `0` em campos que não pertencem ao seu contrato.
- Quando não há meta, o resultado informa `Meta não definida` em vez de sugerir um percentual de atingimento sem significado.
- Meta/bônus continuam disponíveis quando usados e a regra contratual permanece parametrizada pelo usuário.

### Reajuste Salarial

- O resultado passou a mostrar o **percentual efetivo de reajuste**, considerando conjuntamente percentual e parcela fixa.
- A memória deixa explícita a composição do aumento e a premissa do impacto anual.
- Não foram inferidos encargos patronais, convenção coletiva ou reflexos além do escopo documentado.

### Depreciação de Ativos

- A ferramenta passou a aceitar **valor residual por ativo** diretamente.
- A base depreciável agora é `valor do ativo − valor residual` e aparece no resultado/memória.
- Valor residual negativo ou maior/igual ao custo é rejeitado.
- Métodos linear, saldo decrescente e soma dos dígitos respeitam a base depreciável e nunca reduzem o valor contábil abaixo do residual.
- Na projeção de carteira, o residual de ativos com vida útil menor continua compondo o valor contábil nos anos seguintes, em vez de desaparecer quando o cronograma daquele ativo termina.
- A ferramenta continua sem inferir vida útil fiscal, taxa normativa ou enquadramento: esses parâmetros permanecem responsabilidade explícita do usuário/política contábil aplicável.

## Evidência de qualidade

As cinco ferramentas que ainda possuíam golden cases sintéticos do scaffold tiveram seus casos substituídos por entradas e resultados numéricos do domínio. `AssetDepreciationCalculator`, que não possuía `QUALITY.md`, recebeu checklist completo baseado na implementação e testes efetivamente existentes.

As seis ferramentas agora possuem:

- `RiskProfile` compatível com o manifesto;
- golden cases concretos cobrindo os tipos exigidos pelo risco;
- `ToolQualityContractTest`;
- `QUALITY.md` sem pendências administrativas;
- testes unitários específicos para os novos comportamentos;
- documentação atualizada do escopo e das limitações.

Isso significa **prontidão estrutural**, não promoção automática. Todas permanecem `beta` até os lotes finais de UX, release e auditoria independente.

## Evolução do diagnóstico

Após o Lote 1:

- 37 Betas;
- 0 estruturalmente prontas;
- 24 com checklist aberto;
- 15 com artefatos ausentes;
- 15 com golden cases sintéticos.

Após o Lote 2:

- 37 Betas;
- **6 estruturalmente prontas**;
- **19 com checklist aberto**;
- **14 com artefatos ausentes**;
- **10 com golden cases sintéticos**.

Os grupos continuam sobrepostos.

## Compatibilidade e limites do lote

Nenhum slug, rota pública, ID, vertical, `release_order`, classificação Essencial/Plus ou status de maturidade foi alterado.

As versões de manifesto/schema foram incrementadas somente nas ferramentas cujo contrato de resultado/entrada mudou de forma compatível:

- Capital de Giro `1.1.0 → 1.2.0`;
- Fluxo de Caixa `1.1.0 → 1.2.0`;
- Ponto de Equilíbrio `1.1.0 → 1.2.0`;
- Comissão `1.1.0 → 1.2.0`;
- Reajuste Salarial `1.0.0 → 1.1.0`;
- Depreciação `1.0.0 → 1.1.0`.

## Validações do lote

Foram executados no estado acumulado:

- `php scripts/check-repository-integrity.php`;
- `php artisan tools:check-architecture`;
- `php scripts/check-accounting-pains.php`;
- `php artisan analytics:check`;
- `php scripts/e2e-tool-scenarios.php check`;
- `node --check resources/js/app.js`;
- `php scripts/check-beta-activation-readiness.php`;
- smoke tests diretos dos seis calculadores modificados;
- lint PHP dos arquivos PHP alterados.

O PHPUnit existe no repositório, mas não pode ser executado neste ambiente porque o PHP disponível não possui `dom`, `mbstring` e `xmlwriter`. Essa limitação não é tratada como aprovação nem como defeito do projeto.

## Continuidade

Antes do Lote 3 desta frente, reconstruir obrigatoriamente:

**projeto atual enviado pelo usuário → Beta Activation Lote 1 → Beta Activation Lote 2**.

Depois reler `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios `BETA-ACTIVATION-LOT-1-AUDIT.md` e este relatório, `docs/TOOL_QUALITY.md` e `config/product_tools.php`.

O Lote 3 deve tratar o grupo trabalhista/DP previsto no diagnóstico do Lote 1, mantendo a mesma regra: melhorar utilidade e correção primeiro; fechar evidência depois; não promover status administrativamente.
