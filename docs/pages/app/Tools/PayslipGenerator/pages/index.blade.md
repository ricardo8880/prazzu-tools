# Gerador de Holerite

## Objetivo

Montar um demonstrativo individual de pagamento com identificação,
competência, proventos, descontos e líquido, pronto para revisão e impressão.
O gerador não apura automaticamente INSS ou IRRF.

## Funcionamento

- `GET /ferramentas/gerador-holerite` abre o formulário público.
- `POST /ferramentas/gerador-holerite` valida e gera o demonstrativo.
- O usuário informa funcionário, documento, empregador, competência, salário,
  outros proventos, INSS, IRRF e outros descontos.
- O domínio soma proventos e descontos, impede descontos superiores aos
  proventos e calcula o líquido.
- O holerite renderizado pode ser impresso ou salvo em PDF pelo navegador.

## Implementação principal

- **View:** `app/Tools/PayslipGenerator/Resources/views/index.blade.php`
- **Rotas:** `tools.gerador-holerite.index` e
  `tools.gerador-holerite.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A página estende `layouts.app`, declara título, meta description e canonical e
usa componentes compartilhados de página, formulário, resultado e impressão.

## Conteúdos

- identificação de funcionário e empregador;
- competência mensal;
- salário e outros proventos;
- INSS, IRRF e outros descontos;
- totais de proventos, descontos e líquido;
- demonstrativo tabular e botão de impressão.

## Estados

- **Inicial:** formulário sem documento gerado e valores adicionais zerados.
- **Inválido:** erros de identificação, competência ou valores.
- **Calculado:** resumo e holerite formatado.
- **Descontos acima dos proventos:** o domínio rejeita. Atualmente o
  `FormRequest` não antecipa essa regra, portanto o fluxo ainda precisa
  apresentar erro controlado em vez de propagar a exceção.
- **Impressão:** documento calculado enviado ao recurso do navegador.

## Dependências

Depende de `Money`, contratos compartilhados de cálculo, política de dados
sensíveis, componentes Blade, Bootstrap e impressão do browser. Nome e documento
devem continuar criptografados em qualquer persistência.

## Manutenção

INSS e IRRF são entradas explícitas e devem vir de uma apuração válida. Não
transformar o módulo em sistema de folha. A feature Plus
`advanced_productivity` não possui fluxo; cadastro de funcionários, envio por
e-mail, assinatura, histórico e emissão em lote ainda não estão disponíveis.

## Validação mínima

- confirmar GET público, HTML completo, SEO e canonical;
- gerar holerite com e sem valores adicionais;
- validar competência no formato `YYYY-MM`;
- rejeitar descontos acima dos proventos como erro de formulário;
- conferir escape dos dados pessoais, totais, impressão e responsividade.


## Estado após o Lote 5

A página deve apresentar resultados coerentes com a memória estruturada do domínio e deixar visíveis as premissas estimativas ou os valores informados manualmente pelo usuário.
