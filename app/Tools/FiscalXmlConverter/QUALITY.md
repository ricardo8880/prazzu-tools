# Qualidade — Conversor Fiscal de XML

## Riscos principais

- XML malicioso ou excessivamente grande.
- Confusão entre valor extraído e cálculo tributário validado.
- Variações de namespace e campos opcionais.
- Documentos não pertencentes aos modelos 55 e 65.

## Gates do Lote 4

- Rejeitar XML vazio, malformado, com DTD ou entidade externa.
- Rejeitar documento sem `infNFe`, sem itens ou de modelo não suportado.
- Extrair identificação, partes, itens, NCM, CFOP, tributos e totais.
- Preservar independência do módulo.
- Manter a ferramenta em `draft` até existir experiência pública completa.
