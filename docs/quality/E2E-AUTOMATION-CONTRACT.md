# Contrato da Automação E2E das Ferramentas

## Objetivo

Este documento define a base oficial da automação de navegador do Prazzu Tools. A automação deve crescer pelo catálogo oficial, sem listas paralelas de ferramentas e sem acoplamento entre módulos.

## Princípios obrigatórios

1. `config/product_tools.php` continua sendo o inventário oficial do produto.
2. `config/e2e_quality.php` é somente o inventário executável de qualidade e deve espelhar exatamente os mesmos 32 módulos e slugs.
3. O motor será compartilhado; cada ferramenta declarará somente dados, ações e expectativas específicas.
4. Nenhum teste E2E pode utilizar banco, storage, fila, e-mail ou integração de produção.
5. Logs de diagnóstico serão permanentes no ambiente E2E; o runner nunca deve inserir e remover logs no código.
6. Seletores de navegador deverão usar `data-testid` ou contratos compartilhados estáveis, nunca posição visual ou classes Bootstrap como contrato primário.
7. Dados pessoais, documentos completos, senhas, tokens e conteúdo de arquivos não podem aparecer em relatórios.
8. Testes exploratórios não bloqueiam release até que uma falha seja transformada em cenário determinístico.

## Perfis de execução

### Smoke

Executado frequentemente. Deve abrir todas as ferramentas oficiais, validar resposta, estrutura principal e ausência de falhas fatais no navegador ou backend.

### Regressão

Inclui smoke, um cenário válido e um cenário inválido por ferramenta. É bloqueante para integração de alterações.

### Completo

Inclui bordas, downloads, acesso, histórico, uploads, lotes, ações secundárias, responsividade e navegadores adicionais conforme o inventário da ferramenta.

### Exploratório

Gera variações controladas de entrada e interação. Seus achados precisam ser revisados antes de bloquear o fluxo de entrega.

## Contrato mínimo futuro de cenário

Cada cenário deverá declarar, no mínimo:

- chave estável;
- perfil de execução;
- identidade necessária;
- estado inicial;
- entradas sem dados sensíveis reais;
- ações ordenadas;
- expectativas visuais e HTTP;
- downloads esperados;
- efeitos persistidos esperados;
- política de limpeza;
- tags de risco.

O schema concreto e o executor serão implementados no lote específico do motor declarativo. Este lote não antecipa classes ou dependências sem uso real.

## Evidências mínimas de falha

- ferramenta, cenário e etapa;
- URL e método;
- status HTTP;
- erro de console ou exceção de página;
- exceção Laravel correlacionada, quando houver;
- screenshot;
- trace do navegador;
- dados do cenário sanitizados;
- arquivo baixado inválido, quando aplicável.

## Critério para ferramenta nova

Uma ferramenta nova somente poderá ser considerada pronta quando estiver no inventário oficial e no inventário E2E, possuir cenários mínimos e cumprir os gates arquiteturais vigentes no lote correspondente.

## Contrato declarativo de cenários — versão 1.0.0

O Lote 6 separa dados de domínio do motor de navegador. Cada cenário declara `id`, `title`, `kind`, `tool_slug`, `access_profile`, `tags`, `steps` e `expectations`. O runner somente interpreta o schema; ele não deve conter condicionais por ferramenta.

Ações suportadas nesta versão: `fill`, `select`, `check`, `uncheck`, `click` e `submit`.
Expectativas suportadas: `visible`, `hidden`, `text`, `url` e `field_value`.

Qualquer ampliação do schema deve ser motivada por cenário real, manter compatibilidade ou alterar explicitamente `schema_version`.

## Downloads declarativos

Downloads devem ser associados ao cenário que produz o resultado necessário e usar `ToolDownloadExpectation`. O sucesso exige validação do arquivo recebido: nome, extensão, MIME, tamanho, assinatura e, quando aplicável, estrutura interna. Um clique, uma resposta 200 ou a simples existência do botão não comprovam a exportação.

O runner oficial suporta PDF, XLSX, CSV, DOCX e ZIP. Arquivos HTML disfarçados devem falhar. Evidências ficam no storage E2E e nunca no repositório.
