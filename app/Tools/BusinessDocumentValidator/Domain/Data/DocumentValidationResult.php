<?php

declare(strict_types=1);
namespace App\Tools\BusinessDocumentValidator\Domain\Data;
final readonly class DocumentValidationResult
{
    /** @param list<string> $messages */
    public function __construct(public string $digits,public string $formatted,public bool $valid,public array $messages){}
    public function toArray():array{return ['type'=>'ein','type_label'=>'Employer Identification Number (EIN)','digits'=>$this->digits,'formatted'=>$this->formatted,'valid'=>$this->valid,'messages'=>$this->messages];}
}
