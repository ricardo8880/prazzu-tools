# Qualidade da Calculadora de Pró-Labore e Distribuição de Lucros

Este arquivo complementa o README do módulo. O README da raiz permanece como
autoridade máxima.

## Perfil de risco

- Natureza: `Calculation`
- Dependência normativa: `High`
- Dados pessoais: `Common`
- Integração externa: `None`
- Persistência planejada: `History`
- Persistência operacional no estado `draft`: desativada
- Processamento: `Synchronous`
- Risco do resultado: `Tax`
- Frequência de atualização: `Unpredictable`
- Exportações planejadas: CSV, JSON e PDF
- Nível derivado pelo classificador: `Critical`

Consequências obrigatórias:

- revisão por especialista tributário ou contábil;
- revisão de privacidade;
- metadados normativos completos;
- testes Unit, Feature e Browser;
- testes de histórico e exportação;
- casos dourados de todos os tipos exigidos para risco crítico.

## Contrato do Lote 1

- [x] Objetivo e limites definidos.
- [x] Escopo Essencial e Plus separado sem bloquear fórmulas.
- [x] Vários sócios, várias competências e histórico incluídos no produto.
- [x] Enquadramentos empresariais iniciais definidos.
- [x] Situações não suportadas explicitadas.
- [x] Registro normativo criado.
- [x] Manifesto atualizado, mantendo capacidades não implementadas desativadas.
- [x] Módulo permanece em `draft`.

## Contrato do Lote 3

- [x] Lucro máximo disponível calculado sem `float`.
- [x] Prejuízos, reservas, ajustes e antecipações tratados explicitamente.
- [x] Distribuição proporcional implementada com ajuste determinístico de centavos.
- [x] Distribuição por valores definidos validada contra o total pretendido.
- [x] Distribuição superior ao lucro disponível rejeitada.
- [x] Participações societárias exigem total exato de 100%.
- [x] Memória de cálculo e alertas do domínio implementados.
- [x] Domínio de lucros permanece independente do domínio de pró-labore.

## Casos dourados obrigatórios

Os placeholders de `Tests/Fixtures/GoldenCases.php` deverão ser substituídos por
casos aprovados antes da ativação:

- [ ] cenário comum;
- [ ] cenário de fronteira;
- [ ] entrada inválida;
- [ ] arredondamento;
- [ ] situação de não aplicação;
- [ ] transição normativa;
- [ ] regressão.

Cada caso deverá informar entrada completa, resultado esperado, fonte oficial,
versão normativa e política de arredondamento.

## Domínio e precisão

- [x] Valores financeiros usam `Money` ou inteiros em centavos.
- [x] Nenhuma regra financeira utiliza `float`.
- [x] Pró-labore e distribuição de lucros possuem domínios separados.
- [ ] Datas de referência resolvem a versão normativa aplicável.
- [ ] Arredondamentos são explícitos e testados por etapa.
- [x] Situações não cobertas não recebem aproximações silenciosas.
- [ ] Controllers seguem exclusivamente `Request -> Action -> Response`.

## Segurança e privacidade

- [ ] Form Requests validam todas as entradas.
- [x] Nome e documento não são obrigatórios no contrato básico.
- [x] Rótulos opcionais de sócios não podem ser enviados para analytics.
- [ ] Logs não armazenam remunerações ou resultados sem proteção.
- [ ] Exportações obedecem às políticas compartilhadas do Core.
- [ ] Histórico usa projeção segura, versionada e autenticada do Core.
- [ ] A política de retenção do histórico foi aprovada.
- [ ] A revisão de privacidade está registrada.

## Interface

- [ ] Componentes Blade compartilhados foram priorizados.
- [ ] Bootstrap foi usado antes de CSS específico.
- [ ] A interface funciona em mobile, tablet e desktop.
- [ ] Estados de foco, erro, carregamento e vazio estão cobertos.
- [ ] O fluxo principal funciona por teclado.
- [ ] A memória de cálculo é legível e acessível.

## Gate antes da ativação

- [x] Manifesto permanece em `draft`.
- [x] Perfil de risco contempla histórico planejado.
- [x] Famílias de fontes normativas registradas.
- [ ] Fontes, vigências e tabelas da primeira versão aprovadas.
- [ ] Casos dourados completos e sem placeholders.
- [ ] Revisão técnica especializada concluída.
- [ ] Revisão de privacidade concluída.
- [ ] Histórico e persistência implementados pelo Core.
- [ ] Testes Unit, Feature, Browser, histórico e exportação implementados.
- [ ] `composer release:check` está verde.


## Lote 4 — experiência Essencial

- [x] Request valida competência, regime, valores monetários e confirmação de premissas.
- [x] Application combina pró-labore e distribuição sem acoplar os domínios.
- [x] Resultado consolidado apresenta retenções, custo empresarial, lucro disponível e alertas.
- [x] Formulário não solicita CPF nem cadastro permanente.
- [ ] Testes Browser e revisão visual final permanecem para o gate de ativação.


## Lote 5 — simulações avançadas

- [x] Vários sócios por competência, com participação total validada pelo domínio.
- [x] Várias competências de 2026 por cenário.
- [x] Dois a quatro cenários temporários.
- [x] Consolidação por sócio, competência e cenário.
- [x] Comparação de total recebido e custo empresarial contra o cenário-base.
- [x] Limites de volume aplicados na borda HTTP.
- [x] Nenhum cadastro ou persistência introduzido no módulo.
- [ ] Testes Browser completos serão fechados no gate final.

## Lote 6

- [x] Manifesto declara histórico e persistência versionada.
- [x] Política de histórico contém projeção explícita de entrada e resultado.
- [x] Visitante continua calculando e exportando sem login.
- [x] Somente usuário autenticado pode acessar resultados persistidos.
- [x] Histórico usa contratos compartilhados do Core.
- [x] Exportações usam serviços compartilhados do Core.
- [x] Repetição, exclusão e exportação histórica respeitam propriedade do registro.
- [ ] Suíte completa, autorização e privacidade aguardam o gate do Lote 7.

## Lote 7 — release candidate

- [x] Casos dourados deixaram de ser placeholders.
- [x] Manifesto e testes refletem histórico, persistência e exportação implementados.
- [x] Versão elevada para `1.0.0-rc.1`.
- [x] Status elevado para `beta`, permitindo validação controlada sem declarar aprovação externa inexistente.
- [x] Sintaxe, autoload e testes unitários do módulo executados no ambiente disponível.
- [ ] Revisão técnica especializada deve ser registrada por profissional habilitado.
- [ ] Revisão formal de privacidade deve ser registrada.
- [ ] `composer release:check` completo depende das extensões PHP exigidas pelo projeto.
- [ ] Mudança para `active` somente após os três itens externos acima.
