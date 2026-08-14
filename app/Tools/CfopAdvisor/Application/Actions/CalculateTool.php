<?php

declare(strict_types=1);
namespace App\Tools\CfopAdvisor\Application\Actions;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\CfopAdvisor\Application\Data\CalculationInput;
use App\Tools\CfopAdvisor\Domain\Services\Calculator;
final readonly class CalculateTool { public function __construct(private Calculator $calculator) {} public function execute(CalculationInput $input): ToolCalculationResult { return $this->calculator->calculate($input); } }
