# Conversor Fiscal de XML

## Descrição

Ferramenta para transformar XML de NF-e e NFC-e em dados fiscais legíveis e estruturados. O módulo prioriza leitura segura, transparência e preservação dos valores informados no documento.

## Funcionalidades

- Leitura segura de XML fiscal modelo 55 e 65.
- Extração de emitente, destinatário, identificação, itens, NCM, CFOP, tributos e totalizadores.
- Rejeição de documentos malformados, entidades externas e formatos não suportados.

## Experiência Essencial

A versão gratuita permitirá processar um XML por vez e visualizar todos os dados extraídos, alertas e totalizadores sem esconder informações fiscais importantes.

## Prazzu Plus

O Plus acrescentará processamento em lote, exportações consolidadas, comparações e continuidade operacional. A leitura completa de um documento individual continuará gratuita.

## Regras de domínio

O Lote 4 aceita NF-e e NFC-e nos modelos 55 e 65, limita cada XML a 10 MB e não executa DTD ou entidades externas. Valores são preservados como números decimais e não são recalculados como se fossem apuração fiscal.

## Integração entre ferramentas

- Contratos publicados: nenhum.
- Contratos aceitos: nenhum.

O módulo funciona de modo independente e não importa implementações internas de outras ferramentas.

## Dependências

- PHP DOM/libxml para leitura XML segura.
- Contratos de catálogo, qualidade e exportação do Core.

## Integração com a plataforma

- Slug: `conversor-fiscal-xml`.
- Rota principal: `tools.conversor-fiscal-xml.index`.
- Namespace de views: `tools-conversor-fiscal-xml`.
- Persistência e histórico: desabilitados neste lote.
- Exportação planejada: CSV, JSON e XLSX.

## Histórico de versões

| Versão | Estado | Alterações |
| --- | --- | --- |
| 0.2.0 | Beta | Fundação segura do parser de NF-e/NFC-e, contratos de dados, golden cases e testes unitários. |

## Qualidade e risco

O resultado possui risco tributário por reproduzir dados fiscais. O parser não substitui validação oficial, escrituração ou conferência do documento autorizado na SEFAZ.

## Recursos Plus concluídos na versão 1.0

A versão 1.0 mantém a leitura individual completa no Essencial e acrescenta produtividade no Plus:

- processamento temporário de 2 a 50 XMLs por lote;
- consolidação da quantidade de documentos, itens, falhas e valor total;
- exportação dos itens em CSV, JSON e SpreadsheetML compatível com Excel;
- histórico criptografado para usuários autenticados, com reabertura e exclusão;
- reutilização do mesmo parser seguro da leitura individual, sem armazenamento dos arquivos originais.

O lote permanece disponível para visitantes durante a fase inicial da plataforma. A autenticação é exigida somente para persistência e continuidade, conforme o README da raiz.

## Segurança e retenção

Os arquivos originais são lidos apenas durante a requisição. O módulo não cria storage próprio. Quando o histórico é autorizado pelo Core, apenas o resultado estruturado e os metadados necessários são persistidos, com retenção de 365 dias e classificação criptografada de `history_payload`.
