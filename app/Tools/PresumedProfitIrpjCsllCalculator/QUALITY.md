# Qualidade — Calculadora de IRPJ e CSLL no Lucro Presumido

Este arquivo complementa o README da ferramenta. O README da raiz continua sendo a autoridade máxima.

## Perfil de risco

- Natureza: `Calculation`
- Dependência normativa: `High`
- Dados pessoais: `None`
- Integração externa: `None`
- Persistência: `History`
- Processamento: `Synchronous`
- Risco do resultado: `Tax`
- Frequência de atualização: `Unpredictable`
- Exportações: `pdf`, `xlsx`

O perfil executável está em `Quality/RiskProfile.php`.

## Casos dourados

`Tests/Fixtures/GoldenCases.php` cobre cenário típico, fronteira, entrada inválida, arredondamento, não aplicabilidade, transição normativa de 2026 e regressão do adicional de IRPJ. As referências apontam para a documentação normativa do módulo e fontes oficiais registradas.

## Segurança e privacidade

- [x] Form Request valida todas as entradas e regras cruzadas relevantes.
- [x] Nenhum dado pessoal é coletado ou enviado para Analytics.
- [x] O módulo não depende de upload ou integração externa.
- [x] Histórico usa a política compartilhada e retenção declarada no manifesto.
- [x] O domínio também rejeita estados inválidos essenciais, evitando confiança exclusiva na camada HTTP.

## Arquitetura

- [x] Nenhuma classe interna de outro módulo em `app/Tools` é importada.
- [x] Bootstrap e componentes compartilhados são usados antes de CSS/JS específico.
- [x] Regras fiscais ficam em `Domain/Rules`, não na view.
- [x] Cálculos monetários e percentuais não usam `float`.
- [x] Resultado, memória, histórico e exportações usam contratos do Core.

## Verificação

- [x] Perfil de risco revisado.
- [x] Casos dourados preenchidos, sem placeholders ativos.
- [x] Fontes normativas oficiais registradas.
- [x] Testes Unit, Feature, Architecture e contratos de qualidade foram implementados.
- [x] Cobertura E2E declarativa foi atualizada para incluir o 34º módulo por descoberta do inventário.
- [ ] `composer release:check` deve ser executado no ambiente do projeto com as extensões PHP exigidas pelo PHPUnit (`dom`, `mbstring`, `xmlwriter`).
