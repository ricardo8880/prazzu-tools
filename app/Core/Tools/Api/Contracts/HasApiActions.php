<?php

namespace App\Core\Tools\Api\Contracts;

interface HasApiActions
{
    /** @return list<class-string<ToolApiAction>> */
    public function apiActions(): array;
}
