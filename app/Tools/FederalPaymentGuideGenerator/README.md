# Gerador Inteligente de DARF/GPS

## Descrição
Ferramenta ativa para orientar a identificação da guia, o código de receita, o vencimento e os acréscimos legais de DARF e GPS com memória auditável.

## Funcionalidades
- catálogo versionado de códigos DARF e GPS suportados;
- cálculo determinístico de multa de mora, sem `float` no domínio;
- cálculo de juros com percentual Selic informado e validado;
- vencimentos básicos de GPS com ajuste de fins de semana;
- memória de cálculo e alertas obrigatórios;
- histórico autenticado, favoritos, recuperação e exclusão;
- exportação do cálculo atual e do histórico em CSV, JSON e PDF.

## Experiência Essencial
Código, vencimento, multa, juros e memória de cálculo completos para um caso por vez, sem exigir autenticação. A ferramenta não emite guia oficial, não transmite dados e não solicita CPF ou CNPJ.

## Prazzu Plus
Histórico autenticado, favoritos, reutilização e exportações profissionais acrescentam produtividade sem alterar a correção do cálculo Essencial.

## Regras e limitações
Ferramenta tributária de alto risco normativo. Nenhum código ou vencimento é inferido sem catálogo e referência. A multa usa 0,33% por dia, limitada a 20%. A Selic acumulada é entrada explícita e deve ser conferida em fonte oficial. O calendário atual ajusta sábados e domingos; feriados e expediente bancário devem ser confirmados no sistema oficial.

## Persistência e privacidade
A persistência usa `ToolRunHistory`, schema versionado e retenção de 365 dias. O payload de histórico é protegido segundo a política central. XML, CPF e CNPJ não são coletados nem armazenados.

## Dependências
O módulo depende somente dos contratos compartilhados do Core para dinheiro, percentuais, histórico, favoritos, persistência, analytics e exportações. Não depende de outra ferramenta nem mantém implementação paralela dessas capacidades.

## Arquitetura
Actions coordenam os casos de uso; Requests validam entrada; Services concentram domínio; Controllers apenas orquestram HTTP. Dinheiro e percentuais usam o Core. CSV usa `TabularExportService`; PDF usa `BrowserPrintExporter` e `PrintableDocument`.

## Qualidade
O módulo possui contrato arquitetural, contrato de integração, perfil de risco, golden cases completos e regressão executável. A regra de acréscimos legais implementa o contrato central `App\Core\Normative`, é resolvida pela data de vencimento e registra metadados completos no histórico.

## Histórico de versões
- 1.1.0 — governança normativa central, fontes oficiais, resolução por vigência e rastreabilidade histórica da regra aplicada.
- 1.0.0 — estabilização final, golden cases completos, contratos de arquitetura e qualidade, correção do perfil de persistência, documentação final e promoção para Active.
- 0.4.0 — exportação do cálculo atual e do histórico em CSV, JSON e PDF.
- 0.3.0 — histórico autenticado, favoritos, recuperação e exclusão.
- 0.2.0 — experiência Essencial pública, analytics, SEO e testes de feature.
- 0.1.0 — fundação de domínio, catálogo inicial, vencimentos, acréscimos legais e golden cases iniciais.
