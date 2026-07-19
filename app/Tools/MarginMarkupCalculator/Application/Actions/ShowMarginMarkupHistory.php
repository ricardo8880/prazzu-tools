<?php

declare(strict_types=1);
namespace App\Tools\MarginMarkupCalculator\Application\Actions;
use App\Core\Tools\History\Data\ToolRunEntry;
final readonly class ShowMarginMarkupHistory
{
 public function __construct(private RequireOwnedMarginMarkupRun $ownedRun) {}
 /** @return array{run: ToolRunEntry} */
 public function execute(string $runId,int $userId): array { return ['run'=>$this->ownedRun->execute($runId,$userId)]; }
}
