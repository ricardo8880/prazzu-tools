# Qualidade de Receipt Issuer

Este arquivo complementa o README da ferramenta. O README da raiz continua sendo a autoridade máxima.

## Perfil de risco gerado

- Natureza: `Generation`
- Dependência normativa: `low`
- Dados pessoais: `Common`
- Integração externa: `None`
- Persistência: `History`
- Processamento: `Synchronous`
- Risco do resultado: `Financial`
- Frequência de atualização: `Rare`
- Exportações: pdf

O perfil executável está em `Quality/RiskProfile.php`. Revise-o antes de implementar o domínio.

## Casos dourados

Substitua os placeholders de `Tests/Fixtures/GoldenCases.php` por casos aprovados. A ativação é bloqueada enquanto houver referências provisórias.

Documente para cada caso:

- entrada completa;
- resultado esperado;
- fonte ou revisão responsável;
- versão normativa, quando aplicável;
- política de arredondamento, quando aplicável.

## Segurança e privacidade

- [ ] Form Requests validam todas as entradas.
- [ ] Nenhum dado pessoal é enviado para analytics.
- [ ] Logs não armazenam documentos, salários ou conteúdo sensível sem mascaramento.
- [ ] Uploads, quando existirem, validam tamanho, MIME e extensão.
- [ ] Integrações, quando existirem, possuem timeout, retry e tratamento de indisponibilidade.
- [ ] Persistência e exclusão seguem as políticas do Core.
- [ ] Rotas protegidas usam gates e middlewares centrais.

## Integração entre ferramentas

- Contratos publicados: Nenhum.
- Contratos aceitos: Nenhum.

- [ ] Todos os contratos declarados estão registrados no Core.
- [ ] A ferramenta funciona normalmente sem integração.
- [ ] O reaproveitamento é iniciado por ação explícita do usuário.
- [ ] Dados importados permanecem revisáveis antes da execução.
- [ ] Contratos ausentes, incompatíveis e não autorizados possuem testes.
- [ ] Nenhuma classe interna de outro módulo em `app/Tools` é importada.

## Interface

- [ ] Bootstrap e componentes compartilhados foram usados antes de CSS específico.
- [ ] A interface funciona em mobile, tablet e desktop.
- [ ] Estados de foco, erro, carregamento e vazio estão cobertos.
- [ ] O fluxo principal funciona por teclado.

## Verificação antes da ativação

- [ ] Perfil de risco revisado.
- [ ] Casos dourados completos e aprovados.
- [ ] Fontes normativas registradas quando aplicável.
- [ ] Testes Unit, Feature e Browser exigidos pelo classificador implementados.
- [ ] Regras financeiras não usam `float`.
- [ ] `composer release:check` está verde.
