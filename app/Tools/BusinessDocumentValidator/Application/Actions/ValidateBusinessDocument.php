<?php

declare(strict_types=1);
namespace App\Tools\BusinessDocumentValidator\Application\Actions;
use App\Tools\BusinessDocumentValidator\Domain\Data\DocumentValidationResult;
use App\Tools\BusinessDocumentValidator\Domain\Validators\BusinessDocumentValidator;
final readonly class ValidateBusinessDocument
{
    public function __construct(private BusinessDocumentValidator $validator){}
    /** @param array{document_number:string} $input */
    public function execute(array $input):DocumentValidationResult{return $this->validator->validate($input['document_number']);}
}
