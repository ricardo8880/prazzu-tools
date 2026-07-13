<?php

namespace App\Core\Tools\Contracts;

interface HasMigrations
{
    public function migrationsPath(): string;
}
