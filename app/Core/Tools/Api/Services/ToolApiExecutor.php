<?php

namespace App\Core\Tools\Api\Services;

use App\Core\Tools\Api\Data\ToolExecutionContext;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

final readonly class ToolApiExecutor
{
    public function __construct(
        private ToolApiActionRegistry $actions,
        private ToolApiResultNormalizer $normalizer,
        private ValidationFactory $validator,
    ) {}

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(
        string $tool,
        string $action,
        array $input,
        ToolExecutionContext $context,
    ): array {
        $apiAction = $this->actions->resolve($tool, $action);
        $validated = $this->validator->make($input, $apiAction->rules())->validate();
        $result = $apiAction->execute($validated, $context);

        return $this->normalizer->normalize($result);
    }
}
