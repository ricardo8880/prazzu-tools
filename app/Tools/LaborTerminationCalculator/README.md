# Calculadora de Rescisão Trabalhista

## Descrição

Ferramenta para estimar verbas de rescisão trabalhista, integrada ao catálogo,
ao histórico criptografado e aos contratos compartilhados do Prazzu Tools.

## Funcionalidades

- saldo de salário;
- férias vencidas, proporcionais, em dobro e adicional de 1/3;
- 13º proporcional;
- aviso-prévio trabalhado, indenizado ou descontado, incluindo
  proporcionalidade;
- modalidades de desligamento e contratos por prazo indeterminado, determinado
  e de experiência;
- indenizações dos artigos 479 e 480;
- remuneração variável, comissões, horas extras e adicionais recorrentes;
- INSS, IRRF, descontos e valor líquido estimado;
- FGTS, multa rescisória, saque estimado e regime do empregado doméstico;
- histórico criptografado por 180 dias para usuários autenticados;
- repetição, visualização e exclusão dos cálculos salvos;
- relatório completo otimizado para impressão e salvamento como PDF pelo
  navegador.

## Regras

- Os resultados são estimativas informativas e não substituem TRCT, documentos
  oficiais ou conferência profissional.
- Casos com estabilidade, normas coletivas, afastamentos, médias complexas ou
  decisões judiciais podem exigir cálculo especializado.
- Regras de cálculo e tabelas tributárias possuem versões independentes.
- A data de referência é recebida pelo caso de uso; o Domain não consulta o
  relógio global.
- A tabela progressiva do INSS usa os limites vigentes desde janeiro de 2026,
  publicados na Portaria Interministerial MPS/MF nº 13/2026, e trunca o
  resultado de cada faixa em centavos conforme a orientação do eSocial.
- Valores monetários utilizam `Money`, sem `float`.
- Visitantes calculam e exportam o resultado atual sem autenticação durante a
  fase gratuita.
- Login é exigido somente para salvar, consultar, repetir ou excluir histórico.
- Histórico, auditoria e impressão são responsabilidades do Core e devem ser
  acessados por seus contratos.

## Dependências

- `Money`, datas de referência e exceções de domínio do Core;
- contratos centrais de histórico e auditoria;
- `BrowserPrintExporter` e `PrintableDocument` para impressão;
- Laravel apenas nas camadas Presentation e Infrastructure;
- recurso nativo de impressão do navegador, sem biblioteca externa de PDF no
  servidor.

O relatório inclui dados do contrato, verbas, descontos, FGTS, alertas, versões
das regras e referências gerais.

## Histórico de versões

- `1.0.0`: cálculo completo de rescisão, regras trabalhistas e tributárias,
  histórico autenticado, repetição, exclusão e relatório para impressão.
- Regra de cálculo: versionada em `LaborTerminationCalculator::RULE_VERSION`.
- Tabelas tributárias: versionadas no calculador de folha.
