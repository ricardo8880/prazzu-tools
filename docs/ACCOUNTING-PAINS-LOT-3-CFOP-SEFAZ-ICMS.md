# Cobertura das dores contábeis — Lote 3 — CFOP, SEFAZ e ICMS próprio

## Reconstrução e regras obedecidas

O lote foi iniciado do zero na ordem exigida: ZIP original `prazzu-tools.zip`, aplicação do Lote 1 e aplicação do Lote 2. Antes das alterações foram relidos o README da raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos Lotes 1 e 2 e o inventário executável.

A entrega continua modular, sem ERP, CRM, gestão operacional ou scraping de SEFAZ. O ZIP do lote contém somente arquivos criados ou alterados neste lote.

## 1. Consultor e Validador de CFOP

- Módulo: `CfopAdvisor`
- Slug: `consultor-validador-cfop`
- ID/release: 45
- Essencial: valida formato e grupos 1/2/3/5/6/7, informa entrada/saída e abrangência da operação.
- Plus `catalog_details`: mostra descrição rápida quando o código está no recorte referencial embarcado.
- Regra de segurança normativa: código estruturalmente válido sem descrição no recorte **não recebe descrição inferida**; a tela exige conferência no Anexo II vigente do Convênio SINIEF s/nº/1970.

### Reutilização no Core

A necessidade deixou de ser hipotética porque o Conversor Fiscal XML também precisa validar CFOP de itens. Foi criado `App\Core\Tax\Fiscal\CfopCatalog`, usado pelos dois módulos. O Core contém apenas referência neutra; formulário, resultado e UX continuam no domínio do `CfopAdvisor`.

## 2. Validador Fiscal SEFAZ

- Módulo: `SefazFiscalValidator`
- Slug: `validador-fiscal-sefaz`
- ID/release: 46
- Essencial: normaliza e valida chave de 44 caracteres, decodifica cUF/AAMM/modelo e, para chave totalmente numérica, verifica o DV módulo 11.
- Plus `key_breakdown`: apresenta série, número e tipo de emissão.
- Não afirma autorização, situação cadastral, cancelamento, inutilização, protocolo ou disponibilidade SEFAZ.
- Não usa certificado digital e não chama serviço estadual nesta versão.

A chave foi tratada como **44 caracteres**, e não como “44 dígitos”, para não cristalizar uma premissa incompatível com a evolução oficial de CNPJ/chaves alfanuméricas em 2026. Para chave alfanumérica, o módulo deliberadamente não afirma validade de DV sem implementação específica da regra vigente.

## 3. Calculadora de ICMS Próprio

- Módulo: `IcmsCalculator`
- Slug: `calculadora-icms-proprio`
- ID/release: 47
- Essencial: base informada, alíquota e redução de base.
- Plus `inside_calculation`: quando o valor informado está sem ICMS, calcula o gross-up do imposto “por dentro”.
- Resultado: base tributável, ICMS próprio e valor com ICMS, com memória de cálculo.
- Não absorve ICMS-ST, DIFAL nem FCP; essas responsabilidades continuam nas ferramentas próprias.
- A ferramenta é paramétrica e não inventa alíquota estadual, benefício, isenção, diferimento ou composição específica de base.

## Ajuste no Conversor Fiscal de XML

O `NfeXmlParser` passou a reutilizar `CfopCatalog` para validar estruturalmente o CFOP de cada item e gera alerta quando encontra um código fora dos grupos válidos. O parser também atualizou o alerta da chave de “44 dígitos” para “44 caracteres”. Nenhuma consulta SEFAZ foi adicionada ao conversor.

## Catálogo e governança

- ferramentas oficiais: 44 → **47**;
- contabilidade: 43 → **46**;
- RH: permanece 1;
- contratos Plus: 138 → **141**;
- dívida Plus estrutural: 0;
- dívida Plus funcional: 0;
- novas dependências Composer/NPM: nenhuma.

## Fontes normativas consideradas

- CONFAZ — Convênio SINIEF s/nº, de 15 de dezembro de 1970, Anexo II (CFOP), incluindo a redação geral do Ajuste SINIEF 03/24 e alterações posteriores vigentes.
- Portal Nacional da NF-e — MOC 7.0 e notas técnicas de NF-e/NFC-e, incluindo a evolução de CNPJ alfanumérico em 2026.
- Lei Complementar 87/1996, especialmente art. 13, para base do ICMS; regras estaduais e enquadramentos específicos permanecem paramétricos.

## Verificações executadas

- `php scripts/lint-php.php`: passou.
- `php artisan tools:check-architecture`: passou sem violações.
- `php artisan analytics:check`: passou.
- execução direta dos serviços de CFOP, SEFAZ e ICMS: casos de fumaça executados com sucesso após correções.
- `php artisan route:list`: bloqueado no ambiente pela ausência de `DOMDocument`/extensão `dom` usada pelo Termwind. Não houve alteração de dependência para contornar o ambiente.

## Limites deliberados

A ferramenta CFOP não tenta reproduzir silenciosamente centenas de notas explicativas como um snapshot eterno: o recorte de descrições rápidas deve evoluir de forma versionada e auditável. A validação estrutural vale para todo CFOP; a descrição exata não é inventada quando não há referência embarcada.

O validador SEFAZ é offline por desenho. Integração online futura exige contrato técnico estável, timeout, falha segura e revisão da infraestrutura de certificado A1 antes de qualquer chamada autenticada.

## Continuidade obrigatória

O Lote 4 deve começar novamente em **ZIP original → Lote 1 → Lote 2 → Lote 3**, reler todos os documentos obrigatórios e então trabalhar apenas em Lucro Real + Reforma Tributária.
