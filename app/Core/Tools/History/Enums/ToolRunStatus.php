<?php

namespace App\Core\Tools\History\Enums;

enum ToolRunStatus: string
{
    case Running = 'running';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
}
