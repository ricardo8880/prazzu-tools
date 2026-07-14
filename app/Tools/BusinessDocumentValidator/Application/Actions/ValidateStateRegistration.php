<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Application\Actions;

use App\Tools\BusinessDocumentValidator\Domain\Data\StateRegistrationValidationResult;
use App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrationValidatorRegistry;

final readonly class ValidateStateRegistration
{
    public function __construct(private StateRegistrationValidatorRegistry $registry) {}

    /** @param array{state_registration:string,state:string} $data */
    public function execute(array $data): StateRegistrationValidationResult
    {
        return $this->registry->validate($data['state_registration'], $data['state']);
    }
}
