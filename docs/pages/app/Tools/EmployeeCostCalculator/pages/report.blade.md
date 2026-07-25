# Relatório individual de custo CLT

- **Tipo:** Documento de saída/impressão
- **Implementação principal:** `app/Tools/EmployeeCostCalculator/Resources/views/report.blade.php`
- **Fluxo:** `tools.custo-funcionario-clt.print` e segunda via do histórico
- **Status:** ativo

## Objetivo

Apresentar para impressão ou salvamento em PDF a identificação autorizada da
empresa, as premissas, o resumo e a memória de um cálculo individual.

## Funcionamento e conteúdos

A partial é renderizada pelo exportador compartilhado de impressão. Ela recebe
`input`, `result` e, opcionalmente, `company`. Mostra apenas valores já
calculados pela Action; não executa regras de domínio.

## Estados e dependências

O bloco empresarial é omitido quando não existe perfil selecionado. Resumo e
memória toleram listas vazias para manter uma saída segura. Depende de
`BrowserPrintExporter`, `PrintableDocument` e do resultado versionado da
ferramenta.

## Regras de manutenção

- Não calcular ou buscar dados na view.
- Não expor perfis sem validação prévia de propriedade.
- Preservar o aviso de estimativa e o uso do exportador compartilhado.

## Validação mínima

Validar resposta HTML completa, valores do resumo, memória, saída sem empresa e
saída com identidade empresarial autorizada.
