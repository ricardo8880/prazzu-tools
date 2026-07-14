<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Contracts;

interface StateRegistrationValidator
{
    public function state(): string;

    public function label(): string;

    public function validate(string $registration): bool;

    public function format(string $registration): string;
}
