<?php

declare(strict_types=1);
namespace App\Tools\BusinessDocumentValidator\Domain\Enums;
enum DocumentType:string
{
    case Ein='ein';
    public function label():string{return 'Employer Identification Number (EIN)';}
}
