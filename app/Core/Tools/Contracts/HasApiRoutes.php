<?php

namespace App\Core\Tools\Contracts;

interface HasApiRoutes
{
    /** Caminho absoluto do arquivo de rotas de API exclusivo da ferramenta. */
    public function apiRoutesPath(): string;
}
