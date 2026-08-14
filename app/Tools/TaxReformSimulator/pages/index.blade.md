# Simulador da Reforma Tributária do Consumo

## Objetivo

Simular de forma orientativa a transição do modelo de tributação do consumo por competência, sem gravar uma alíquota futura presumida como se fosse definitiva e sem misturar o domínio histórico de PIS/Cofins com o novo modelo.

## Funcionamento

O usuário seleciona o ano/competência e informa a base e as referências aplicáveis ao cenário. A regra `ConsumptionTaxTransitionRule` descreve a etapa da transição e o domínio calcula a comparação conforme os parâmetros válidos para aquela fase.

## Implementação principal

- View: `app/Tools/TaxReformSimulator/Resources/views/index.blade.php`.
- Request: `Presentation/Requests/ExecuteToolRequest.php`.
- Regra de transição: `Domain/Rules/ConsumptionTaxTransitionRule.php`.
- Domínio: `Domain/Services/Calculator.php`.
- Rotas: `Routes/web.php`.

## Conteúdos e estados

A página exibe fase da transição, tributos considerados, cálculo orientativo, memória, premissas e alertas. Cenários sem parâmetros suficientes devem permanecer explícitos em vez de receber valores inventados.

## Dependências

Não existe consulta externa em tempo de execução. As referências normativas são mantidas no módulo e devem ser atualizadas por lote quando a legislação ou regulamentação alterar vigência, percentuais ou metodologia.

## Prazzu Plus

`transition_diagnostics` acrescenta diagnóstico detalhado da fase e dos componentes da transição. A simulação principal permanece Essencial.

## Regras de manutenção

Toda mudança normativa deve registrar competência e fonte oficial, atualizar os casos de teste e evitar constantes eternas. Não mover a linha do tempo para o Core até existir segunda reutilização realmente equivalente.

## Validação mínima

Validar competências de fronteira da transição, cenário comum, entrada inválida, memória de cálculo, manifesto, catálogo, rota, E2E, governança Plus e documentação normativa.
