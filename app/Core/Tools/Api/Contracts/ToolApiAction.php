<?php

namespace App\Core\Tools\Api\Contracts;

use App\Core\Tools\Api\Data\ToolExecutionContext;

interface ToolApiAction
{
    public function name(): string;

    /** @return array<string, mixed> */
    public function rules(): array;

    /** @param array<string, mixed> $input */
    public function execute(array $input, ToolExecutionContext $context): mixed;
}
