# Calculadora de Rescisão Trabalhista

Módulo para estimar verbas de rescisão trabalhista, integrado ao catálogo, histórico criptografado e padrões arquiteturais do Prazzu Tools.

## Recursos entregues

- saldo de salário;
- férias vencidas, proporcionais, em dobro e adicional de 1/3;
- 13º proporcional;
- aviso-prévio trabalhado, indenizado ou descontado, incluindo proporcionalidade;
- modalidades de desligamento e contratos por prazo indeterminado, determinado e experiência;
- indenizações dos artigos 479 e 480;
- remuneração variável, comissões, horas extras e adicionais recorrentes;
- INSS, IRRF, descontos e valor líquido estimado;
- FGTS, multa rescisória, saque estimado e regime específico do empregado doméstico;
- histórico criptografado por 180 dias para usuários autenticados;
- repetição, visualização e exclusão dos cálculos salvos;
- relatório completo otimizado para impressão e salvamento em PDF pelo navegador.

## Exportação PDF

A exportação abre um relatório A4 independente, com dados do contrato, verbas, descontos, FGTS, alertas, versões das regras e referências gerais. O botão **Imprimir / Salvar como PDF** utiliza o recurso nativo do navegador, sem dependência externa no servidor.

## Versões

- ferramenta: `1.0.0`;
- regra de cálculo: definida em `LaborTerminationCalculator::RULE_VERSION`;
- tabelas tributárias: versionadas no calculador de folha.

## Observações

Os resultados são estimativas informativas e não substituem o TRCT, os documentos oficiais nem a conferência por profissional qualificado. Casos com estabilidade, normas coletivas, afastamentos, médias complexas ou decisões judiciais podem exigir cálculo especializado.
