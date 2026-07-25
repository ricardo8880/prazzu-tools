# Gerador de Declaração de Trabalho/Renda

## Objetivo

Gerar uma declaração pontual de trabalho e renda com texto personalizado,
pronta para conferência, assinatura e impressão.

## Funcionamento

- `GET /ferramentas/declaracao-trabalho-renda` abre o formulário público.
- `POST /ferramentas/declaracao-trabalho-renda` valida e gera a declaração.
- O usuário informa trabalhador, documento, empregador/declarante, função,
  cidade, início do vínculo, renda mensal e data de emissão.
- O domínio compõe o texto e os campos de local, data e assinatura.
- A declaração pode ser impressa ou salva em PDF pelo navegador.

## Implementação principal

- **View:** `app/Tools/WorkIncomeStatementGenerator/Resources/views/index.blade.php`
- **Rotas:** `tools.declaracao-trabalho-renda.index` e
  `tools.declaracao-trabalho-renda.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A view estende `layouts.app`, configura título, descrição SEO e canonical e usa
componentes compartilhados para formulário, resultado e impressão.

## Conteúdos

- identificação do trabalhador e declarante;
- função e data inicial;
- renda mensal;
- cidade e data de emissão;
- texto completo da declaração;
- linha de assinatura e ação de impressão.

## Estados

- **Inicial:** data de emissão sugerida como a data corrente.
- **Inválido:** erros de identificação, datas, cidade ou renda.
- **Calculado:** resumo e declaração pronta.
- **Impressão:** documento gerado entregue ao recurso do navegador.

## Dependências

Usa `Money`, contratos compartilhados de cálculo, política de dados sensíveis,
componentes Blade, Bootstrap e impressão do browser. Nome e documento exigem
proteção em qualquer persistência futura.

## Manutenção

O texto não comprova por si só vínculo ou renda; os dados devem ser conferidos e
a assinatura deve vir de responsável legítimo. Manter escape de conteúdo
fornecido pelo usuário. A feature Plus `advanced_productivity` não possui fluxo;
modelos ilimitados, assinatura digital, histórico, logotipo e envio por e-mail
não estão disponíveis.

## Validação mínima

- verificar GET público, HTML completo, SEO e canonical;
- gerar texto com caracteres especiais e garantir escape;
- validar campos obrigatórios, renda e formatos de data;
- conferir local, data e linha de assinatura;
- testar impressão e responsividade.
