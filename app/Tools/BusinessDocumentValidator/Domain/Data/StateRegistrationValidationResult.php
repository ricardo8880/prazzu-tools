<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Data;

final readonly class StateRegistrationValidationResult
{
    /** @param list<string> $candidateStates */
    public function __construct(
        public string $input,
        public string $normalized,
        public ?string $state,
        public string $stateLabel,
        public bool $valid,
        public bool $supported,
        public string $formatted,
        public string $message,
        public array $candidateStates = [],
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'input' => $this->input,
            'normalized' => $this->normalized,
            'state' => $this->state,
            'state_label' => $this->stateLabel,
            'valid' => $this->valid,
            'supported' => $this->supported,
            'formatted' => $this->formatted,
            'message' => $this->message,
            'candidate_states' => $this->candidateStates,
        ];
    }
}
