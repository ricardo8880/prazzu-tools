# Calculadora de ISS

## Descrição

Estima o ISS a partir dos dados e da alíquota municipal informados pelo usuário, sem substituir a apuração fiscal oficial.

## Funcionalidades

- cálculo do ISS e do líquido após retenção;
- memória de cálculo e avisos de conferência;
- consolidação, cenários e exportações no Plus.

## Escopo

Calcula o ISS estimado de um serviço usando município, descrição, valor e **alíquota informada pelo usuário**. Não consulta cadastro municipal nem decide automaticamente local de incidência, código de serviço ou retenção.

## Experiência Essencial

- município, serviço, valor e alíquota;
- ISS = valor × alíquota;
- líquido quando a retenção é explicitamente marcada;
- memória e aviso de conferência municipal.

## Prazzu Plus

- retenção e tomador;
- até 10 serviços por simulação;
- consolidação por município;
- cenários de alíquota por município;
- PDF e XLSX.

## Referências verificadas em 12/08/2026

A LC 116/2003 estabelece normas gerais do ISS; a alíquota mínima geral é 2% e a máxima geral é 5%, ressalvadas hipóteses legais. A legislação municipal e o enquadramento concreto devem ser confirmados pelo usuário.

Não há persistência operacional nem função de ERP.

## Regras de domínio

O valor do ISS é o valor do serviço multiplicado pela alíquota informada. Valores monetários usam `Money` e percentuais usam `Percentage`, sem `float`.

## Dependências

- objetos financeiros `Money` e `Percentage` do Core;
- memória de cálculo e exportadores compartilhados;
- regras de acesso Plus da plataforma.

## Histórico de versões

- `1.0.0` — implementação inicial em 12/08/2026.
