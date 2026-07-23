# Histórico de recibos

## Rota

`GET /ferramentas/emissor-de-recibos/historico`

## Acesso

Exige autenticação e autorização pela capacidade compartilhada de histórico.

## Objetivo

Lista recibos salvos pelo usuário autenticado e permite:

- reutilizar os dados em uma nova emissão;
- exportar novamente em PDF;
- excluir o registro do histórico.

A página não implementa persistência própria; consome o histórico versionado do Core.
