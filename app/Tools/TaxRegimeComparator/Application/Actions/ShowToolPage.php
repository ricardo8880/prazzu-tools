<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Application\Actions;

use App\Core\Tools\Data\ToolManifest;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use App\Tools\TaxRegimeComparator\Domain\Enums\TaxRegime;
use App\Tools\TaxRegimeComparator\Tool;

final readonly class ShowToolPage
{
    public function __construct(private Tool $tool) {}

    /** @return array{tool: ToolManifest, regimes: list<TaxRegime>, activities: list<BusinessActivity>} */
    public function execute(): array
    {
        return [
            'tool' => $this->tool->manifest(),
            'regimes' => TaxRegime::cases(),
            'activities' => BusinessActivity::cases(),
        ];
    }
}
