<?php

declare(strict_types=1);
namespace App\Tools\BusinessDocumentValidator\Domain\Validators;
use App\Tools\BusinessDocumentValidator\Domain\Data\DocumentValidationResult;
final class BusinessDocumentValidator
{
    public const RULE_VERSION='US-EIN-FORMAT-1.0';
    public function validate(string $document):DocumentValidationResult
    {
        $digits=preg_replace('/\D+/','',$document)??'';
        $valid=strlen($digits)===9 && $digits!=='000000000';
        $formatted=strlen($digits)===9?substr($digits,0,2).'-'.substr($digits,2):$digits;
        $messages=$valid?[
            'The number matches the basic nine-digit EIN format.',
            'Format validation does not verify that the IRS issued the EIN or that it belongs to a particular business.',
        ]:[
            'An EIN must contain exactly nine digits and is commonly displayed as XX-XXXXXXX.',
            'This tool validates format only; it does not query IRS records.',
        ];
        return new DocumentValidationResult($digits,$formatted,$valid,$messages);
    }
}
