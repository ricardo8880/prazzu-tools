# Instalação e validação local

## Requisitos

- PHP 8.2 ou superior;
- Composer 2;
- Node.js 20 ou superior;
- npm;
- extensões PHP `dom`, `mbstring`, `pdo_sqlite`, `xml` e `xmlwriter` para a suíte completa.

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

## Verificação

```powershell
composer format
composer verify
```

## Diagnóstico rápido

```powershell
php artisan route:list
php artisan tools:check-architecture
php artisan migrate:status
```
