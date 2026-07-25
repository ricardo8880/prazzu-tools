# Histórico de custos CLT

- **Tipo:** Página de histórico autenticada
- **Implementação principal:** `app/Tools/EmployeeCostCalculator/Resources/views/history/index.blade.php`
- **Rota:** `tools.custo-funcionario-clt.history.index`
- **Robots:** `noindex,nofollow`
- **Status:** ativo

## Objetivo

Permitir que a pessoa autenticada consulte as próprias simulações individuais
e em lote, sem transformar o recurso em gestão de funcionários ou folha.

## Funcionamento e conteúdos

A página lista execuções bem-sucedidas pertencentes ao usuário, com paginação e
filtros de data. Cada linha informa data, referência, tipo e link para detalhes.

## Estados e dependências

Possui estados vazio, listado e mensagem de sucesso após exclusão. Depende do
histórico compartilhado, do middleware de persistência autenticada e do gate da
feature `history`.

## Regras de manutenção

- Manter isolamento por proprietário e responder 404 para IDs de terceiros.
- Não mostrar payloads sensíveis na listagem.
- Preservar paginação, filtros e `noindex`.

## Validação mínima

Validar visitante redirecionado/bloqueado, lista própria, filtros, estado vazio,
paginação e tentativa de acesso cruzado.
