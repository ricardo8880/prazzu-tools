<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\Enums;

enum DocumentType: string
{
    case Cpf = 'cpf';
    case Cnpj = 'cnpj';
}
