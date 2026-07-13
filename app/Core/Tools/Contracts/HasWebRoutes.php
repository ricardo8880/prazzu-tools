<?php

namespace App\Core\Tools\Contracts;

interface HasWebRoutes
{
    /** Caminho absoluto do arquivo de rotas web exclusivo da ferramenta. */
    public function webRoutesPath(): string;
}
