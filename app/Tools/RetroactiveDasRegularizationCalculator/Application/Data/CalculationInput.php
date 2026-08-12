<?php

declare(strict_types=1); namespace App\Tools\RetroactiveDasRegularizationCalculator\Application\Data; use App\Core\Tools\Contracts\ToolCalculationInput; final readonly class CalculationInput implements ToolCalculationInput { public function __construct(public array $competencies,public int $regularizationMonths=6){} public function toArray():array{return ['competencies'=>$this->competencies,'regularization_months'=>$this->regularizationMonths];}}
