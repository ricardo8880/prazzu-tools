<?php

declare(strict_types=1);

namespace App\Core\Quality\Enums;

enum ExternalIntegrationDependency: string
{
    case None = 'none';
    case Optional = 'optional';
    case Required = 'required';
}
