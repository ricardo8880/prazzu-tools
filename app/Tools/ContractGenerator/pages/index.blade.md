# Gerador de Contratos — Prazzu Tools

- **View:** `app/Tools/ContractGenerator/Resources/views/index.blade.php`
- **Rota GET:** `tools.gerador-de-contratos.index`
- **Rota POST de geração:** `tools.gerador-de-contratos.build`
- **Rota POST de visualização:** `tools.gerador-de-contratos.preview`
- **Estado do módulo:** `beta`

## Objetivo

Conduzir o usuário por perguntas suficientes para gerar um contrato completo, permitir edição integral do conteúdo e visualizar a versão atual antes da exportação.

## Estado atual

A página oferece duas modalidades iniciais:

1. prestação de serviços;
2. compra e venda de bem móvel.

Depois do questionário, o backend cria um `ContractDraft` tipado e o `ContractTextGenerator` redige o documento da modalidade escolhida. O texto gerado fica em um `textarea` editável e pode ser reenviado para atualizar a visualização. Para visitantes, o fluxo permanece temporário; histórico e persistência versionada pertencem à continuidade autenticada da plataforma.

## Fluxo

1. usuário escolhe a modalidade;
2. informa as duas partes do contrato;
3. informa objeto, valor e condições de pagamento;
4. responde às perguntas específicas da modalidade;
5. informa foro, cidade e data de assinatura;
6. o backend valida os campos, incluindo CPF/CNPJ pelo Core técnico;
7. `BuildContractDraft` transforma os dados em domínio tipado;
8. `ContractTextGenerator` monta o texto completo e converte o valor em reais por extenso pelo Core;
9. a página exibe a conferência dos dados, o editor e a visualização;
10. o usuário pode editar o texto e enviar `Atualizar visualização` para conferir a versão alterada.

PDF e DOCX estão disponíveis no fluxo atual; JSON integra a política transversal de exportação da arquitetura final.

## Regras de interface

- utilizar Bootstrap e componentes Blade compartilhados antes de marcação ou CSS próprio;
- manter o questionário funcional sem JavaScript obrigatório;
- exibir erros com o resumo de validação compartilhado e feedback nos campos;
- manter todas as funções acessíveis sem autenticação;
- não exigir persistência no fluxo público; histórico e versões dependem de identidade e da infraestrutura compartilhada;
- informar que os modelos são gerais e podem exigir revisão em situações submetidas a regimes especiais;
- não apresentar recursos Plus como necessários ao preenchimento ou à revisão.

## Estados

### Nenhuma modalidade selecionada

Exibe as opções e orienta o usuário a escolher uma modalidade.

### Modalidade selecionada

Exibe o questionário específico e o botão `Gerar contrato completo`.

### Erro de validação

Mantém os dados anteriores, destaca os campos inválidos e utiliza o resumo de validação compartilhado.

### Contrato gerado

Exibe uma conferência resumida dos dados e abre o texto completo no editor.

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
- Core `Cpf`, `Cnpj`, `Money` e `BrazilianMoneyInWords`.

Nenhuma dependência de outro módulo de ferramenta.
