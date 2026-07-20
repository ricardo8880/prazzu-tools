# Instalação e validação local

## Requisitos

- PHP 8.2 ou superior;
- Composer 2;
- Node.js 20 ou superior;
- npm;
- extensões PHP `dom`, `mbstring`, `pdo_sqlite`, `xml` e `xmlwriter`.

As extensões são obrigatórias para o gate oficial. O projeto não considera uma validação completa quando Pint ou PHPUnit são ignorados por falta de dependências do ambiente.

## Instalação automática

```powershell
composer setup
```

O comando:

1. instala dependências PHP;
2. cria `.env` a partir de `.env.example` quando necessário;
3. cria `database/database.sqlite` quando necessário;
4. gera a chave da aplicação;
5. executa migrations;
6. instala dependências Node com `npm ci`;
7. compila os assets.

## Instalação manual

```powershell
composer install
Copy-Item .env.example .env
New-Item .\database\database.sqlite -ItemType File -Force
php artisan key:generate
php artisan migrate
npm ci
npm run build
```

## Desenvolvimento

```powershell
composer dev
```

## Gate oficial de entrega

```powershell
composer release:check
```

Esse é o único comando aceito para declarar a base pronta. Ele executa, na ordem:

1. validação da versão do PHP, extensões obrigatórias, Node.js e npm;
2. lint de todos os arquivos PHP próprios;
3. verificação de formatação pelo Pint;
4. validadores de arquitetura e Analytics;
5. suíte automatizada;
6. caches de configuração, rotas e views;
7. build de produção dos assets;
8. limpeza dos caches gerados pela validação.

A integração contínua executa esse mesmo comando para evitar divergência entre a validação local e a validação do repositório.

## Comandos parciais para diagnóstico

Comandos parciais ajudam a localizar uma falha, mas não substituem o gate oficial:

```powershell
composer platform:check
composer lint
composer format:check
composer architecture
composer test
php artisan route:list
php artisan migrate:status
```
