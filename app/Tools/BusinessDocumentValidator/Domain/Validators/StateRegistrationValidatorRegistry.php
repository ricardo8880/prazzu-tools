<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Validators;

use App\Tools\BusinessDocumentValidator\Domain\Contracts\StateRegistrationValidator;
use App\Tools\BusinessDocumentValidator\Domain\Data\StateRegistrationValidationResult;

final class StateRegistrationValidatorRegistry
{
    /** @var array<string, StateRegistrationValidator> */
    private array $validators = [];

    /** @param iterable<StateRegistrationValidator> $validators */
    public function __construct(iterable $validators)
    {
        foreach ($validators as $validator) {
            $this->validators[$validator->state()] = $validator;
        }
        ksort($this->validators);
    }

    /** @return array<string, string> */
    public function supportedStates(): array
    {
        $states = [];
        foreach ($this->validators as $code => $validator) {
            $states[$code] = $validator->label();
        }

        return $states;
    }

    public function validate(string $input, ?string $state): StateRegistrationValidationResult
    {
        $normalized = preg_replace('/\D+/', '', $input) ?? '';
        $state = $state !== null ? strtoupper(trim($state)) : null;

        if ($state !== null && $state !== 'AUTO') {
            $validator = $this->validators[$state] ?? null;
            if ($validator === null) {
                return new StateRegistrationValidationResult($input, $normalized, $state, $state, false, false, $normalized, 'Ainda não há uma regra confiável disponível para a UF selecionada.');
            }

            $valid = $validator->validate($normalized);

            return new StateRegistrationValidationResult(
                $input,
                $normalized,
                $state,
                $validator->label(),
                $valid,
                true,
                $validator->format($normalized),
                $valid ? 'A Inscrição Estadual passou nas regras da UF selecionada.' : 'A Inscrição Estadual não passou nas regras da UF selecionada.',
            );
        }

        $candidates = [];
        foreach ($this->validators as $code => $validator) {
            if ($validator->validate($normalized)) {
                $candidates[] = $code.' — '.$validator->label();
            }
        }

        if (count($candidates) === 1) {
            [$code] = explode(' — ', $candidates[0], 2);
            $validator = $this->validators[$code];

            return new StateRegistrationValidationResult($input, $normalized, $code, $validator->label(), true, true, $validator->format($normalized), 'A UF foi identificada como candidata única entre as regras suportadas.', $candidates);
        }

        return new StateRegistrationValidationResult(
            $input,
            $normalized,
            null,
            count($candidates) > 1 ? 'Mais de uma UF possível' : 'UF não identificada',
            false,
            true,
            $normalized,
            count($candidates) > 1 ? 'O número é compatível com mais de uma UF. Selecione o estado para confirmar.' : 'O número não corresponde a nenhuma das regras estaduais atualmente suportadas.',
            $candidates,
        );
    }
}
