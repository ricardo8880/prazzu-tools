<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Application\Actions;

use App\Core\Tools\Data\ToolManifest;
use App\Tools\FederalPaymentGuideGenerator\Domain\Enums\GuideType;
use App\Tools\FederalPaymentGuideGenerator\Domain\Services\RevenueCodeCatalog;
use App\Tools\FederalPaymentGuideGenerator\Tool;

final readonly class ShowToolPage
{
    public function __construct(private Tool $tool, private RevenueCodeCatalog $catalog) {}

    /** @return array{tool:ToolManifest,codes:array<string,list<array<string,mixed>>>} */
    public function execute(): array
    {
        $codes = ['darf' => [], 'gps' => []];

        foreach ($this->catalog->all() as $entry) {
            $codes[$entry->guideType->value][] = [
                'type' => $entry->guideType->value,
                'code' => $entry->code,
                'description' => $entry->description,
                'periodicity' => $entry->periodicity,
                'reference' => $entry->officialReference,
                'requires_confirmation' => $entry->requiresProfessionalConfirmation,
            ];
        }

        return ['tool' => $this->tool->manifest(), 'codes' => $codes];
    }
}
