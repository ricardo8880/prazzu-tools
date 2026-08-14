# Consultor e Validador de CFOP

## Objetivo

Valida a estrutura de CFOP, classifica entrada/saída e abrangência e oferece descrição rápida para códigos recorrentes.

## Funcionamento

A página recebe os parâmetros do usuário, valida a entrada no FormRequest do módulo e envia o caso ao serviço de domínio.

## Implementação principal

- View: `app/Tools/CfopAdvisor/Resources/views/index.blade.php`
- Controller: `app/Tools/CfopAdvisor/Presentation/Controllers/ToolController.php`
- Domínio: `app/Tools/CfopAdvisor/Domain/Services/Calculator.php`

## Conteúdos e estados

Exibe formulário, erros de validação, resultado resumido, premissas e alertas fiscais.

## Dependências

Não exige integração externa nesta versão. Reutiliza apenas contratos compartilhados do Prazzu Tools.

## Regras de manutenção

Não transformar a página em ERP ou rotina de gestão. Mudanças normativas precisam preservar fonte, competência e caráter paramétrico quando a legislação estadual variar.

## Prazzu Plus

O recurso `catalog_details` amplia o diagnóstico sem esconder a solução Essencial.

## Validação mínima

Validar rota GET, caso válido, caso inválido, manifesto, registro no catálogo, arquitetura e contrato Plus.
