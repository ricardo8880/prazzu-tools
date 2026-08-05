# Contrato visual estável da automação E2E

## Objetivo

Definir seletores de automação que sobrevivam a mudanças de texto, tradução, Bootstrap, posição e composição visual. `data-testid` é um contrato técnico e não deve ser usado para estilo, regra de negócio ou Analytics.

## Regras

1. O seletor deve descrever a função estável do elemento, não sua aparência.
2. Componentes compartilhados fornecem identificadores padrão.
3. Telas com mais de uma ocorrência podem sobrescrever `testId` explicitamente.
4. Nomes de campos são normalizados por `App\Core\Quality\E2E\Support\TestId`.
5. Testes não devem depender de `.btn-primary`, ordem de elementos ou texto localizado quando existe `data-testid`.
6. Um identificador deve ser único dentro do escopo em que o teste o consulta; coleções intencionais podem repetir o padrão e usar `.first()` ou filtro semântico.

## Identificadores oficiais deste lote

| Superfície | Padrão |
|---|---|
| Raiz da ferramenta | `tool-page-<slug>` |
| Painel principal de entrada | `tool-form-panel` |
| Resultado principal | `tool-result` |
| Resumo de validação | `validation-summary` |
| Campo compartilhado | `field-<nome-normalizado>` |
| Grupo de downloads | `download-actions` |
| PDF | `download-pdf` |
| XLSX | `download-xlsx` |
| Impressão legada permitida | `print-result` |

## Sobrescrita

```blade
<x-tools.form-panel title="Cenário A" test-id="scenario-a-form">
    ...
</x-tools.form-panel>

<x-tools.result-panel title="Cenário A" test-id="scenario-a-result">
    ...
</x-tools.result-panel>
```

A sobrescrita é obrigatória quando múltiplos painéis equivalentes aparecem na mesma jornada e precisam ser endereçados separadamente.

## Limite do Lote 4

Este lote estabelece o contrato nos componentes compartilhados e migra o piloto do navegador. Formulários legados que não usam os componentes compartilhados serão classificados pelo inventário e migrados junto dos cenários concretos, sem antecipar o motor declarativo do Lote 6.
