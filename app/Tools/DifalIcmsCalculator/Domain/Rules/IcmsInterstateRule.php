<?php

declare(strict_types=1);
namespace App\Tools\DifalIcmsCalculator\Domain\Rules;
use App\Core\Dates\Contracts\EffectiveDated; use App\Core\Dates\EffectivePeriod; use App\Core\Normative\Contracts\NormativeRule; use App\Core\Normative\NormativeRuleMetadata;
final readonly class IcmsInterstateRule implements EffectiveDated, NormativeRule {
 public function __construct(private NormativeRuleMetadata $metadata) {}
 public function normativeMetadata(): NormativeRuleMetadata { return $this->metadata; }
 public function effectivePeriod(): EffectivePeriod { return $this->metadata->effectivePeriod; }
 public function rateFor(string $origin,string $destination,bool $imported): string { if($imported)return '4'; $southEastSouth=['SP','RJ','MG','RS','SC','PR']; $sevenDest=['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','PA','PB','PE','PI','RN','RO','RR','SE','TO']; return in_array($origin,$southEastSouth,true)&&in_array($destination,$sevenDest,true)?'7':'12'; }
}
