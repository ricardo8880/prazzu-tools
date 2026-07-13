# Prazzu Tools

Plataforma modular em Laravel para reunir ferramentas voltadas à rotina contábil. O catálogo e o layout já estão estruturados; as ferramentas exibidas atualmente são provisórias e serão substituídas gradualmente por módulos reais.

## Requisitos

- PHP 8.2 ou superior
- Composer 2
- Node.js 20 ou superior
- Extensões PHP exigidas pelo Laravel e pelos testes: `ctype`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `session`, `tokenizer` e `xmlwriter`

## Instalação

```bash
composer install
npm ci
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

Para desenvolvimento local:

```bash
composer run dev
```

Ou execute os serviços separadamente:

```bash
php artisan serve
npm run dev
```

## Testes e verificações

```bash
php artisan test
php artisan route:cache
php artisan config:cache
npm run build
```

Depois das verificações de cache, limpe os arquivos gerados no ambiente de desenvolvimento quando necessário:

```bash
php artisan optimize:clear
```

## Assets

Os assets próprios da plataforma têm uma única fonte oficial:

```text
resources/css/app.css
resources/js/app.js
```

Eles são compilados pelo Vite. Bootstrap e Bootstrap Icons permanecem carregados por CDN para preservar exatamente o layout atual.

Código específico de uma ferramenta deverá ficar sob `resources/js/tools/<slug>` e, quando necessário, em uma estrutura equivalente de CSS. O padrão definitivo dos módulos será estabelecido nos lotes arquiteturais seguintes.

## Estrutura principal

```text
app/Core/Tools       contratos, manifestos, registro e catálogo
app/Tools            módulos de ferramentas
config/tools/         módulos, categorias, placeholders e métricas
resources/views      layout e páginas da plataforma
routes/tools.php     carregamento de rotas dos módulos
 tests                testes unitários e funcionais
```

## Estado atual

- O layout existente é considerado definitivo e não deve ser redesenhado durante a preparação arquitetural.
- As ferramentas atuais são placeholders de catálogo.
- Newsletter, sugestão, login, cadastro, planos e conteúdos institucionais ainda são demonstrativos.
- Nenhuma ferramenta contábil real foi implementada.

## Arquitetura

Leia também:

- [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)
- [`docs/LOT-1-FOUNDATION.md`](docs/LOT-1-FOUNDATION.md)
- [`docs/LOT-2-TOOL-CORE.md`](docs/LOT-2-TOOL-CORE.md)
- [`app/Tools/README.md`](app/Tools/README.md)

## Versionamento, auditoria e histórico

As regras de persistência de execuções, versões e retenção estão documentadas em [`docs/LOTE-5-VERSIONAMENTO-AUDITORIA-HISTORICO.md`](docs/LOTE-5-VERSIONAMENTO-AUDITORIA-HISTORICO.md).
