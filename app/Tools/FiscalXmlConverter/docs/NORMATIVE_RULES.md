# Regras normativas — Conversor Fiscal de XML

## Escopo do Lote 4

- NF-e modelo 55 e NFC-e modelo 65.
- Estrutura baseada nos grupos `infNFe`, `ide`, `emit`, `dest`, `det`, `prod`, `imposto` e `ICMSTot`.
- Leitura por nome local para tolerar o namespace oficial da NF-e.

## Segurança

- Limite de 10 MB por documento.
- `LIBXML_NONET` obrigatório.
- DTD e declarações de entidade são rejeitados antes da leitura.
- Nenhuma URL, schema remoto ou entidade externa é resolvida.

## Precisão e interpretação

- O parser extrai os valores declarados; não refaz a apuração tributária.
- Ausência de um tributo é representada por zero no resumo numérico.
- A chave de acesso é obtida do atributo `Id` de `infNFe`.
- Chave ausente ou diferente de 44 dígitos gera alerta, sem inventar valor.

## Fora do escopo deste lote

- CT-e, MDF-e, NFS-e e eventos fiscais.
- Consulta de autorização na SEFAZ.
- Validação contra XSD.
- Processamento em lote, exportação e interface pública.
