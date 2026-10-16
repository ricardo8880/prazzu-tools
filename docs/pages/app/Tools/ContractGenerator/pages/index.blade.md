# Gerador de Contratos — Prazzu Tools

- **View:** `app/Tools/ContractGenerator/Resources/views/index.blade.php`
- **Rota GET:** `tools.contract-generator.index`
- **Rota POST de geração:** `tools.contract-generator.build`
- **Rota POST de visualização:** `tools.contract-generator.preview`
- **Rota POST PDF:** `tools.contract-generator.export.pdf`
- **Rota POST DOCX:** `tools.contract-generator.export.docx`
- **Estado do módulo:** `beta`

## Objetivo

Conduzir o usuário por perguntas suficientes para gerar um contrato completo, permitir edição integral do conteúdo e exportar exatamente a versão atual em PDF ou Word.

## Estado atual — lote 5

A página oferece duas modalidades iniciais:

1. prestação de serviços;
2. compra e venda de bem móvel.

Depois do questionário, o backend cria um `ContractDraft` tipado e o `ContractTextGenerator` redige o documento. O conteúdo fica em um `textarea` editável. O mesmo formulário permite atualizar a visualização, abrir a versão para impressão/PDF ou baixar um arquivo DOCX. A página usa indicadores textuais das quatro etapas e permanece funcional sem JavaScript obrigatório.

## Fluxo

1. usuário escolhe a modalidade;
2. informa as duas partes do contrato;
3. informa objeto, valor e condições de pagamento;
4. responde às perguntas específicas da modalidade;
5. informa foro, cidade e data de assinatura;
6. o backend valida os campos, incluindo CPF/CNPJ pelo Core técnico;
7. `BuildContractDraft` transforma os dados em domínio tipado;
8. `ContractTextGenerator` monta o texto completo e converte o valor em reais por extenso pelo Core;
9. a página exibe conferência, editor e visualização;
10. `Atualizar visualização` reexibe o texto atual sem persistência;
11. `Exportar PDF` envia o texto atual ao `BrowserPrintExporter` compartilhado;
12. `Baixar Word` gera um pacote OpenXML `.docx` temporário com o texto atual.

## Regras de interface

- utilizar Bootstrap e componentes Blade compartilhados antes de marcação ou CSS próprio;
- manter o questionário funcional sem JavaScript obrigatório;
- exibir erros com o resumo de validação compartilhado e feedback nos campos;
- manter geração, edição e exportação acessíveis sem autenticação;
- não salvar respostas, contratos ou versões;
- os botões de exportação devem enviar o conteúdo atual do `textarea`, inclusive alterações ainda não reenviadas para a visualização;
- PDF deve abrir em nova aba para impressão/salvamento pelo navegador;
- Word deve iniciar download de `.docx`;
- informar que os modelos são gerais e podem exigir revisão em situações submetidas a regimes especiais;
- não apresentar recursos Plus como necessários ao preenchimento, revisão ou exportação;
- manter aviso de modelo geral visível e semanticamente identificado;
- registrar analytics somente com modalidade, slug e formato, nunca com dados das partes ou texto contratual;
- manter o módulo como `beta` até revisão jurídica especializada e release check integral.

## Estados

### Nenhuma modalidade selecionada

Exibe as opções e orienta o usuário a escolher uma modalidade.

### Modalidade selecionada

Exibe o questionário específico e o botão `Gerar contrato completo`.

### Erro de validação

Mantém os dados anteriores, destaca os campos inválidos e utiliza o resumo de validação compartilhado.

### Contrato gerado

Exibe uma conferência resumida dos dados, editor, visualização e ações PDF/Word.

### Contrato editado

Após `Atualizar visualização`, exibe exatamente o texto enviado pelo editor e informa que nenhum dado foi salvo.

## Dependências

- `x-tools.page`;
- `x-tools.form-panel`;
- `x-tools.form.input`;
- `x-tools.form.money`;
- `x-tools.form.select`;
- `x-tools.result-panel`;
- `x-tools.validation-summary` via `x-tools.page`;
- Core `Cpf`, `Cnpj`, `Money` e `BrazilianMoneyInWords`;
- Core `BrowserPrintExporter` e `PrintableDocument` para PDF;
- Core `SimpleZipArchiveBuilder` para empacotamento do DOCX.

Nenhuma dependência de outro módulo de ferramenta.
