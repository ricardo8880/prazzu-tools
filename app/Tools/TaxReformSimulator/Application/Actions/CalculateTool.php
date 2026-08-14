<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Application\Actions; use App\Core\Tools\Calculation\Data\ToolCalculationResult; use App\Tools\TaxReformSimulator\Application\Data\CalculationInput; use App\Tools\TaxReformSimulator\Domain\Services\Calculator; final readonly class CalculateTool { public function __construct(private Calculator $calculator){} public function execute(CalculationInput $input):ToolCalculationResult{return $this->calculator->calculate($input);} }
