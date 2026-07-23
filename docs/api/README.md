# API privada do Prazzu Tools

Esta API expõe o motor das ferramentas para produtos autorizados do ecossistema Prazzu. Ela não entrega HTML, Blade ou estilos. A plataforma consumidora monta a interface e envia os dados para processamento.

## Configuração

No ambiente do Prazzu Tools, cadastre os clientes em `TOOLS_API_CLIENTS`:

```env
TOOLS_API_CLIENTS='[{"id":"prazzu-core","name":"Prazzu Core","token":"troque-por-um-segredo-longo","abilities":["tools:read","tools:execute"]}]'
TOOLS_API_RATE_LIMIT=120
TOOLS_API_ALLOWED_ORIGINS=https://core.prazzu.com.br
```

Nunca envie o token para código executado no navegador. O Core deve chamar a API pelo backend.

## Autenticação

Todas as chamadas usam Bearer Token:

```http
Authorization: Bearer troque-por-um-segredo-longo
Accept: application/json
```

## Endpoints

```text
GET  /api/v1
POST /api/v1/tools/{tool}/{action}
```

O endpoint de execução exige a ability `tools:execute`.

## Envelope de sucesso

```json
{
  "success": true,
  "data": {},
  "meta": {
    "request_id": "..."
  }
}
```

## Envelope de erro

```json
{
  "success": false,
  "error": {
    "code": "validation_failed",
    "message": "Os dados informados são inválidos.",
    "details": {}
  },
  "meta": {
    "request_id": "..."
  }
}
```

## Ações disponíveis

| Ferramenta | Ação | Content-Type |
|---|---|---|
| `calculadora-de-honorarios-contabeis` | `calculate` | JSON |
| `validador-de-cnpj` | `validate` | JSON |
| `gerador-darf-gps` | `calculate` | JSON |
| `conversor-fiscal-xml` | `convert` | multipart/form-data |
| `calculadora-de-rescisao` | `calculate` | JSON |
| `calculadora-margem-markup` | `calculate` | JSON |
| `calculadora-pro-labore-distribuicao-lucros` | `calculate` | JSON |
| `calculadora-simples-nacional` | `calculate` | JSON |
| `comparador-tributario` | `compare` | JSON |
| `calculadora-ferias` | `calculate` | JSON |

Os exemplos completos estão em `prazzu-tools.postman_collection.json`. O contrato OpenAPI está em `openapi.json`.

## Upload de XML

A ação do conversor recebe o campo `xml_file`:

```bash
curl -X POST \
  -H 'Authorization: Bearer SEU_TOKEN' \
  -H 'Accept: application/json' \
  -F 'xml_file=@nota.xml;type=application/xml' \
  https://tools.prazzu.com.br/api/v1/tools/conversor-fiscal-xml/convert
```

## Exemplo PHP para o Core

Consulte `examples/PrazzuToolsClient.php`. O cliente usa o HTTP Client do Laravel, mantém o token somente no backend e lança exceção quando a API responde com erro.

## Versionamento

A versão atual é `v1`. Mudanças incompatíveis devem criar uma nova versão de URL, preservando a anterior durante o período de migração.
