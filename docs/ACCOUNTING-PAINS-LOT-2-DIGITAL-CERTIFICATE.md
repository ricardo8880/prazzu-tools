# Cobertura das dores contábeis — Lote 2 — Certificado Digital A1

## Reconstrução e regras obedecidas

O trabalho partiu novamente do ZIP original e reaplicou integralmente o Lote 1 antes de qualquer alteração. Foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `docs/ACCOUNTING-PAINS-LOT-1-AUDIT.md`, `config/product_tools.php` e o inventário oficial.

O lote preserva a separação Prazzu Tools × Prazzu Core: não existe cadastro operacional de certificados, renovação, agenda, emissão ou gestão de clientes.

## Ferramenta publicada

- Módulo: `DigitalCertificateAnalyzer`
- Nome: **Analisador de Certificado Digital A1**
- Slug: `analisador-certificado-digital-a1`
- ID oficial: 44
- Release order: 44
- Vertical: `contabilidade`
- Estado: `Beta`
- Histórico/persistência: desabilitados
- Exportação: PDF

## Escopo Essencial

A ferramenta recebe um arquivo PKCS#12 `.pfx`/`.p12` e a senha somente durante a requisição. Quando o arquivo é aberto com sucesso, apresenta:

- titular/CN e organização;
- emissor;
- início e fim do período de validade;
- dias restantes;
- alerta de vencido, ainda não válido ou vencimento em até 30 dias;
- CPF/CNPJ quando um documento válido puder ser detectado nos campos retornados pelo OpenSSL;
- indicação “provável e-CPF/e-CNPJ” somente quando essa evidência existir.

O módulo não afirma que um certificado temporalmente válido está confiável ou não revogado.

## Plus

`technical_report` acrescenta serial, algoritmo de assinatura, tipo/tamanho da chave pública, fingerprint SHA-256, SAN quando disponível e relatório PDF. O PDF recebe apenas metadados sanitizados; senha e chave privada ficam fora do payload.

## Segurança

1. upload máximo de 5 MB;
2. extensões `.pfx` e `.p12`;
3. nenhuma chamada a histórico ou persistência;
4. `CalculationInput::toArray()` não contém bytes PKCS#12 nem senha;
5. resultado não contém chave privada;
6. erros do OpenSSL são drenados e substituídos por mensagem controlada;
7. relatório não recebe senha nem bytes do certificado;
8. fixture criptográfica dos testes é fictícia e autoassinada.

## Fora do escopo 1.0

- emissão ICP-Brasil;
- A3/token/cartão;
- assinatura de documentos;
- armazenamento/gestão de certificados;
- autenticação em SEFAZ;
- consulta OCSP/CRL;
- validação completa da cadeia de confiança.

Essas capacidades exigem novo lote e revisão explícita de segurança.

## Governança e catálogo

O inventário oficial passa de 43 para 44 ferramentas. O novo contrato Plus eleva a matriz de 137 para 138 contratos declarados, estritos e funcionalmente certificados, mantendo dívida zero.

## Qualidade

O módulo possui testes para abertura do PKCS#12, detecção de CNPJ, ausência de segredos no payload, senha incorreta, modo Essencial sem detalhes Plus, página pública e geração PDF real. O cenário E2E usa o atributo genérico `data-e2e-fixture` para fornecer o PKCS#12 de teste ao upload.

## Continuidade

O Lote 3 deve reconstruir na ordem: **ZIP original → Lote 1 → Lote 2**. Depois disso, o escopo aprovado é CFOP + SEFAZ + ICMS próprio.
