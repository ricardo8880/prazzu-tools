# E2E — Lote 8 — Validação profunda de downloads

## Objetivo

Comprovar que ações de exportação produzem arquivos verdadeiros, íntegros e compatíveis com o formato declarado. Um clique isolado ou uma resposta HTTP 200 não constitui sucesso.

## Entregas

- contrato `ToolDownloadExpectation` associado ao cenário declarativo;
- validação de nome, extensão, MIME type e tamanho mínimo;
- rejeição de HTML salvo com extensão de documento;
- validação de cabeçalho `%PDF-` e marcador final `%%EOF` em PDF;
- validação de assinatura ZIP e diretório central;
- validação das entradas OOXML obrigatórias em XLSX e DOCX;
- validação mínima de conteúdo tabular em CSV;
- preservação do arquivo recebido como evidência do Playwright;
- resumo JSON das verificações aplicadas;
- reutilização dos IDs e logs correlacionados do Lote 7;
- gate executável `composer e2e:downloads:check`.

## Contrato declarativo

Cada download pertence a um cenário que prepara a tela e o resultado necessário. A declaração informa:

- `id` estável;
- `test_id` do botão ou link;
- formato e extensão;
- tamanho mínimo;
- fragmento opcional do nome;
- MIME type esperado;
- entradas internas obrigatórias para ZIP/OOXML.

O motor não contém condicionais por ferramenta. Novas ferramentas apenas acrescentam expectativas ao cenário correspondente.

## Formatos suportados

### PDF

O arquivo precisa começar com `%PDF-`, possuir `%%EOF`, não conter HTML no início e respeitar tamanho, extensão e MIME declarados.

### XLSX

O arquivo precisa ser um ZIP legível e conter ao menos `[Content_Types].xml` e `xl/workbook.xml`. Entradas adicionais podem ser exigidas pelo cenário.

### DOCX

O arquivo precisa ser um ZIP legível e conter ao menos `[Content_Types].xml` e `word/document.xml`.

### CSV

O conteúdo não pode ser HTML, deve possuir texto útil e pelo menos cabeçalho e uma linha de dados.

### ZIP

A assinatura e o diretório central precisam ser legíveis. O cenário pode exigir nomes internos específicos.

## Piloto

O cenário válido de `custo-funcionario-clt` declara dois downloads reais:

- relatório PDF;
- planilha XLSX.

Esses downloads são executados após o cálculo e preservados em `storage/app/e2e/artifacts/downloads`.

## Segurança e evidências

O relatório anexa o arquivo baixado e um JSON técnico com tamanho, MIME, formato, entradas internas e verificações realizadas. O validador não registra os dados preenchidos além da definição já controlada do cenário e não copia o conteúdo integral do documento para logs.

## Continuidade

O Lote 9 deve reutilizar os mesmos cenários, correlação e validadores ao cobrir autenticação, perfis de acesso e fluxos transversais. Não deve criar outra infraestrutura de download ou sessão.
