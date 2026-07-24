# Qualidade do Gerador de Contratos

Este arquivo complementa o README do módulo. O README da raiz continua sendo a autoridade máxima.

## Perfil de risco revisado

- Natureza: `Generation`
- Dependência normativa: `high`
- Dados pessoais: `Common`
- Integração externa: `None`
- Persistência: `History`
- Processamento: `Synchronous`
- Risco do resultado no classificador atual: `Informational`
- Frequência de atualização: `Unpredictable`
- Exportações: `pdf`, `docx`, `json`

O perfil executável está em `Quality/RiskProfile.php`. A dependência normativa é alta porque modelos contratuais podem precisar acompanhar alterações legais e contextos especiais. O resultado permanece classificado como informativo porque a interface apresenta um modelo geral editável, sem afirmar substituição de análise jurídica específica.

## Casos dourados

A suíte em `Tests/Fixtures/GoldenCases.php` cobre cenário típico, fronteira, entrada inválida, transição normativa e regressão. Os casos estabilizam o comportamento do software; não constituem parecer jurídico.

## Segurança e privacidade

- [x] Form Requests validam todas as entradas.
- [x] Analytics recebe apenas slug, modalidade e formato de exportação; nomes, documentos, endereços e texto contratual não são enviados.
- [x] O fluxo Essencial público permanece temporário; histórico e persistência versionada são capacidades de continuidade vinculadas à identidade e seguem a política compartilhada da plataforma.
- [x] PDF e DOCX são gerados sob demanda a partir do texto atual.
- [x] Nenhuma integração externa é necessária.

## Interface

- [x] Bootstrap e componentes compartilhados são priorizados.
- [x] O fluxo principal funciona sem JavaScript obrigatório.
- [x] Estados inicial, seleção, validação, geração e edição estão documentados.
- [x] Ações permanecem acessíveis por teclado e usam rótulos textuais.
- [x] Layout usa grids responsivos e ações com quebra em telas menores.

## Verificação de publicação

- [x] Perfil de risco implementado.
- [x] Casos dourados sem placeholders.
- [x] Testes Unit, Feature, arquitetura e exportação presentes.
- [x] Regras monetárias reutilizam `Money` e não usam `float`.
- [x] Arquitetura e catálogo de analytics passam nos checks disponíveis.
- [ ] Revisão jurídica especializada dos modelos gerais.
- [ ] `composer release:check` integral em ambiente com todas as extensões PHP exigidas.

Enquanto os dois itens finais permanecerem pendentes, a ferramenta deve permanecer `beta`, e não `active`.
