# Gerador de Contratos

## Descrição

### Problema resolvido

Permitir que o usuário produza um contrato completo a partir de perguntas guiadas, revise e edite o texto final e exporte o documento em PDF ou Word.

## Escopo entregue até o lote 5

O lote 1 criou a fundação oficial do módulo. O lote 2 adicionou o questionário, a validação das respostas e o `ContractDraft` tipado. O lote 3 passou a redigir o contrato completo e permitir edição do texto. O lote 4 concluiu o fluxo Essencial com exportação do conteúdo atual em PDF e Word/DOCX. O lote 5 finaliza interface, acessibilidade, analytics, perfil de risco e contratos de qualidade.

As modalidades iniciais continuam sendo:

- prestação de serviços;
- compra e venda de bem móvel.

O módulo passa ao estado `beta`: fica visível e utilizável no catálogo, mas não é marcado como `active` enquanto a revisão jurídica especializada dos modelos gerais e o `release:check` integral não forem concluídos.

## Funcionalidades

### Experiência Essencial

- geração completa a partir de perguntas;
- redação do contrato conforme a modalidade selecionada;
- valor do contrato em formato monetário e por extenso;
- edição integral do texto gerado;
- visualização do texto editado;
- exportação em PDF pela infraestrutura compartilhada de impressão da plataforma;
- download do texto atual em Word/DOCX.

A edição, a visualização e as exportações do fluxo Essencial não exigem login nem persistência. O arquivo exportado usa exatamente o conteúdo presente no editor no momento da ação. Histórico e persistência versionada são capacidades de continuidade vinculadas à identidade do usuário e não bloqueiam o uso público da ferramenta.

### Prazzu Plus

- biblioteca ampliada de contratos;
- cláusulas inteligentes;
- favoritos;
- preenchimento automático da empresa;
- histórico;
- comparação entre versões.

O Plus adiciona produtividade e continuidade. Nenhum recurso Plus é necessário para gerar, revisar ou exportar corretamente o contrato no fluxo Essencial.

## Dependências

- Core técnico: CPF, CNPJ, Money, `BrazilianMoneyInWords`, `BrowserPrintExporter`, `PrintableDocument`, `SimpleZipArchiveBuilder`, contratos padronizados de ferramenta e componentes visuais compartilhados da plataforma.
- Nenhuma dependência de outro módulo de ferramenta.

## Regras arquiteturais

- todo domínio e redação contratual específicos permanecem neste módulo;
- PDF reutiliza `App\Core\Export\Services\BrowserPrintExporter` em vez de criar infraestrutura própria;
- a criação OpenXML do DOCX permanece específica deste módulo enquanto somente o Gerador de Contratos precisar de Word;
- o empacotamento ZIP foi promovido para `App\Core\Export\Services\SimpleZipArchiveBuilder`, pois passou a existir uma segunda necessidade real além do Analytics;
- o módulo não gerencia clientes, tarefas, processos ou workflow empresarial;
- o módulo não depende de nenhuma outra ferramenta;
- rascunho e texto editado permanecem temporários para visitantes; histórico e persistência versionada usam a infraestrutura compartilhada da plataforma quando vinculados à identidade do usuário.

## Integrações

- Contratos publicados: nenhum.
- Contratos aceitos: nenhum.

## Capacidades da plataforma

- Slug: `gerador-de-contratos`
- Estado: `beta`
- Categoria: `geradores`
- Histórico: habilitado para continuidade autenticada
- Persistência: versionada, schema `1`, com retenção de 365 dias
- Exportação: habilitada
- Formatos declarados: `pdf`, `docx`, `json`
- Compartilhamento: desabilitado
- Dados sensíveis persistidos: protegidos por política criptografada

## Estrutura relevante do lote 5

```text
Quality/RiskProfile.php
Tests/Fixtures/GoldenCases.php
Tests/Unit/ToolQualityContractTest.php
Tests/Architecture/ModuleArchitectureTest.php
Tests/Architecture/CatalogRegistrationTest.php
QUALITY.md
```

O fluxo Essencial permanece:

`perguntas → validação → ContractDraft → redação → edição → PDF/DOCX temporário`, com histórico/persistência disponíveis somente como continuidade vinculada à identidade.

Analytics registra apenas conclusão da geração e formato de exportação, sem nomes, documentos, endereços ou conteúdo do contrato.

## Limites do texto gerado

Os modelos são gerais e cobrem somente as duas modalidades declaradas. A interface informa que situações sujeitas a regimes especiais podem exigir adaptação. O gerador não tenta substituir análise jurídica específica nem cria cláusulas inteligentes; essa última capacidade permanece classificada como Plus.

## Histórico de versões

| Versão | Estado | Alterações |
| --- | --- | --- |
| 0.1.0 | Draft | Fundação oficial do módulo, manifesto, rota, view e testes do lote 1. |
| 0.2.0 | Draft | Questionário por modalidade, validação, dados tipados e rascunho estrutural do contrato. |
| 0.3.0 | Draft | Redação completa por modalidade, valor por extenso compartilhado pelo Core, editor e visualização do texto. |
| 0.4.0 | Draft | Exportação PDF pelo Core compartilhado, download Word/DOCX e promoção do empacotamento ZIP reutilizável para o Core. |
| 0.5.0 | Beta | Acabamento de interface, acessibilidade, analytics sem dados pessoais, perfil de risco, casos dourados e testes de arquitetura/catálogo. |
