# Lote 3 — Custos e encargos trabalhistas

## Escopo

Este lote alinha as ferramentas Custo de Funcionário CLT, Encargos Trabalhistas e INSS Patronal ao Core normativo criado no Lote 2.

## Regra compartilhada

`App\Core\Labor\Normative\EmployerChargeRule` concentra somente a política realmente reutilizada:

- FGTS de 8%;
- CPP patronal de 20% quando devida fora do Simples Nacional;
- RAT informado pelo usuário, preservando o valor ajustado aplicável;
- terceiros informados pelo usuário e zerados para regimes do Simples no modelo atual;
- tratamento da CPP fora do DAS para atividades do Anexo IV.

RAT/FAP, FPAS e terceiros não foram convertidos em uma tabela fixa porque dependem do enquadramento real da empresa. As ferramentas continuam estimativas e exibem essa premissa na memória.

## Fontes oficiais registradas

- Lei nº 8.212/1991, art. 22;
- Lei nº 8.036/1990, art. 15;
- orientação da Receita Federal sobre contribuição previdenciária no Anexo IV do Simples Nacional.

## Compatibilidade

As ferramentas continuam independentes. Nenhuma importa classes de domínio de outra ferramenta. A reutilização ocorre exclusivamente por meio do Core compartilhado.
