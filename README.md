# Prazzu Tools

## Visão do Projeto

O **Prazzu Tools** é uma plataforma de ferramentas para profissionais da contabilidade, criada com um objetivo claro: resolver problemas reais do dia a dia contábil com precisão, rapidez e uma excelente experiência de uso.

A filosofia do projeto é simples:

- A versão **gratuita** deve ser realmente útil e suficiente para que o profissional execute seu trabalho corretamente.
- Os recursos **Plus** existem para oferecer produtividade, automação e conveniência, nunca para comprometer a qualidade dos cálculos gratuitos.

Mais do que um conjunto de calculadoras, o objetivo é construir uma plataforma confiável que se torne referência para escritórios de contabilidade, departamentos financeiros e profissionais da área.

A confiança é o principal ativo do projeto. Cada ferramenta deve produzir resultados corretos, transparentes e consistentes. Quando o usuário decidir assinar o plano Plus, a decisão deverá ser motivada pelo ganho de produtividade, e não pela falta de qualidade da versão gratuita.

---

> **Importante:** O restante deste README (arquitetura, organização dos módulos, padrões de desenvolvimento e demais seções técnicas) permanece igual ao documento original. Esta introdução foi criada para comunicar com clareza a missão e a visão do projeto.


Plataforma modular em Laravel para ferramentas voltadas à rotina contábil. O layout atual é considerado definitivo e deve ser preservado durante a evolução técnica e funcional.

## Estado atual

A fundação arquitetural está preparada para receber ferramentas isoladas e versionadas. Existe uma ferramenta-piloto de margem e markup usada para validar o padrão técnico; as demais ferramentas exibidas no catálogo continuam provisórias.

A plataforma já possui:

- catálogo e registro central de ferramentas;
- módulos isolados;
- manifestos tipados;
- dinheiro, percentuais, datas e identificadores compartilhados;
- versionamento de regras;
- histórico, auditoria e métricas separados;
- autorização, limites e feature flags;
- testes arquiteturais;
- pipeline de qualidade;
- ferramenta-piloto completa.

## Requisitos

- PHP 8.2 ou superior;
- Composer 2;
- Node.js 20 ou superior;
- extensões PHP exigidas pelo Laravel;
- `dom`, `mbstring`, `pdo_sqlite`, `xml` e `xmlwriter` para executar toda a suíte local.

## Instalação

```bash
composer setup
```

O comando prepara `.env`, SQLite, migrations, dependências PHP e Node e o build do Vite.

Instruções detalhadas para Windows e instalação manual estão em [`docs/INSTALLATION.md`](docs/INSTALLATION.md).

## Desenvolvimento

```bash
composer dev
```

Ou execute os serviços separadamente:

```bash
php artisan serve
php artisan queue:listen
npm run dev
```

## Qualidade

Corrigir a formatação:

```bash
composer format
```

Executar sintaxe, Pint, arquitetura e testes:

```bash
composer quality
```

Executar a verificação completa, incluindo caches e build:

```bash
composer verify
```

Diagnósticos individuais:

```bash
php artisan tools:check-architecture
php artisan route:list
php artisan migrate:status
```

## Criar uma ferramenta

```bash
php artisan make:tool NomeDaFerramenta \
    --slug=nome-da-ferramenta \
    --category=calculadoras \
    --access=free \
    --status=draft
```

Leia a convenção completa em [`app/Tools/README.md`](app/Tools/README.md).

## Assets

As fontes oficiais são:

```text
resources/css/app.css
resources/js/app.js
```

Os assets são compilados pelo Vite. Bootstrap e Bootstrap Icons continuam carregados por CDN para preservar o layout aprovado.

## Estrutura principal

```text
app/Core             fundamentos e serviços transversais
app/Tools            módulos isolados das ferramentas
config/tools         catálogo, categorias, módulos e métricas demonstrativas
resources/views      layout e páginas da plataforma
routes/tools.php     carregamento das rotas dos módulos
tests                testes unitários, funcionais e arquiteturais
```

## Limpeza e distribuição

Limpar resíduos locais conhecidos:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\cleanup-project.ps1
```

Criar um ZIP sem `.env`, Git, dependências, banco local ou logs:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\package-distribution.ps1
```

Detalhes em [`docs/LOTE-10-LIMPEZA-E-DISTRIBUICAO.md`](docs/LOTE-10-LIMPEZA-E-DISTRIBUICAO.md).

## Documentação

- [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)
- [`docs/LOT-3-MODULE-STANDARD.md`](docs/LOT-3-MODULE-STANDARD.md)
- [`docs/LOT-4-ACCOUNTING-FOUNDATIONS.md`](docs/LOT-4-ACCOUNTING-FOUNDATIONS.md)
- [`docs/LOTE-5-VERSIONAMENTO-AUDITORIA-HISTORICO.md`](docs/LOTE-5-VERSIONAMENTO-AUDITORIA-HISTORICO.md)
- [`docs/LOTE-6-INFRAESTRUTURA-TRANSVERSAL.md`](docs/LOTE-6-INFRAESTRUTURA-TRANSVERSAL.md)
- [`docs/LOTE-7-QUALIDADE-E-TESTES.md`](docs/LOTE-7-QUALIDADE-E-TESTES.md)
- [`docs/LOTE-8-FERRAMENTA-PILOTO.md`](docs/LOTE-8-FERRAMENTA-PILOTO.md)
- [`docs/LOTE_9_ESTABILIZACAO.md`](docs/LOTE_9_ESTABILIZACAO.md)
- [`docs/LOTE-10-LIMPEZA-E-DISTRIBUICAO.md`](docs/LOTE-10-LIMPEZA-E-DISTRIBUICAO.md)
- [`docs/RELEASE-CHECKLIST.md`](docs/RELEASE-CHECKLIST.md)

## Segurança

O arquivo `.env` contém segredos locais e não deve ser enviado. Distribua somente `.env.example`.
