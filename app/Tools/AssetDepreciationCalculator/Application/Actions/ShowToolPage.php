<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Application\Actions;

use App\Core\Tools\Data\ToolManifest;
use App\Tools\AssetDepreciationCalculator\Tool;

final readonly class ShowToolPage
{
    public function __construct(private Tool $tool) {}

    /** @return array{tool: ToolManifest, methods: array<string, string>} */
    public function execute(): array
    {
        return [
            'tool' => $this->tool->manifest(),
            'methods' => [
                'linear' => 'Straight-line',
                'declining_balance' => 'Double declining balance',
                'sum_of_years_digits' => 'Sum-of-the-years-digits',
            ],
        ];
    }
}
