<?php

namespace App\Core\Tools\Contracts;

use App\Core\Tools\Data\ToolDefinition;

interface ToolModule
{
    public function definition(): ToolDefinition;

    /**
     * Caminho absoluto do arquivo de rotas exclusivo da ferramenta.
     */
    public function routeFile(): ?string;
}
