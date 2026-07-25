# Aplicação das correções

Copie os arquivos deste pacote sobre o projeto atual.

## Arquivos que devem ser removidos antes de executar o Laravel

bootstrap/cache/config.php
bootstrap/cache/routes-v7.php
bootstrap/cache/events.php

Esses arquivos são caches gerados no ambiente anterior e não devem ser distribuídos.
Após removê-los, execute no ambiente do projeto:

php artisan optimize:clear
php artisan config:cache
php artisan route:cache

Não remova bootstrap/cache/.gitignore, packages.php ou services.php.
