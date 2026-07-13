<?php

namespace App\Core\Tools\History\Contracts;

use App\Core\Tools\History\Data\ToolHistoryPolicy;

interface HasHistoryPolicy
{
    public function historyPolicy(): ToolHistoryPolicy;
}
