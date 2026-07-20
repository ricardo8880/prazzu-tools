# Qualidade de Tax Regime Comparator

Este arquivo complementa o README da ferramenta. O README da raiz continua sendo a autoridade máxima.

## Perfil de risco gerado

- Natureza: `Comparison`
- Dependência normativa: `high`
- Dados pessoais: `None`
- Integração externa: `None`
- Persistência: `Account` (histórico Plus, retenção de 365 dias)
- Processamento: `Synchronous`
- Risco do resultado: `Tax`
- Frequência de atualização: `Unpredictable`
- Exportações: CSV, JSON e relatório imprimível/PDF pelo Core.

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

- [x] Form Requests validam todas as entradas.
- [ ] Nenhum dado pessoal é enviado para analytics.
- [ ] Logs não armazenam documentos, salários ou conteúdo sensível sem mascaramento.
- [ ] Uploads, quando existirem, validam tamanho, MIME e extensão.
- [ ] Integrações, quando existirem, possuem timeout, retry e tratamento de indisponibilidade.
- [x] Persistência e exclusão seguem as políticas do Core.
- [x] Rotas protegidas usam gates e middlewares centrais.

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

- [x] Bootstrap e componentes compartilhados foram usados antes de CSS específico.
- [ ] A interface funciona em mobile, tablet e desktop.
- [ ] Estados de foco, erro, carregamento e vazio estão cobertos.
- [ ] O fluxo principal funciona por teclado.

## Verificação antes da ativação

- [ ] Perfil de risco revisado.
- [ ] Casos dourados completos e aprovados.
- [ ] Fontes normativas registradas quando aplicável.
- [ ] Testes Unit, Feature e Browser exigidos pelo classificador implementados.
- [x] Regras financeiras não usam `float`.
- [ ] `composer release:check` está verde.
