<?php

namespace App\Core\Tools\Api\Actions;

use Illuminate\Foundation\Http\FormRequest;
use InvalidArgumentException;

trait UsesFormRequestRules
{
    /** @return class-string<FormRequest> */
    abstract protected function requestClass(): string;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $class = $this->requestClass();
        $request = app($class);

        if (! $request instanceof FormRequest) {
            throw new InvalidArgumentException("A classe [{$class}] deve ser um FormRequest.");
        }

        return $request->rules();
    }
}
