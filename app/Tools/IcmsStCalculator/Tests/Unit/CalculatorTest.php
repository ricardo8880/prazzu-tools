<?php

declare(strict_types=1);

namespace App\Tools\IcmsStCalculator\Tests\Unit;

use App\Tools\IcmsStCalculator\Application\Data\CalculationInput;
use App\Tools\IcmsStCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_internal_operation_calculates_icms_st(): void
    {
        $r=(new Calculator())->calculate($this->input());
        self::assertSame('R$ 1.400,00',$r->summary[0]->value);
        self::assertSame('R$ 72,00',$r->summary[1]->value);
        self::assertSame('R$ 72,00',$r->summary[3]->value);
    }
    public function test_interstate_adjusted_mva_is_applied(): void
    {
        $i=$this->input(operationType:'interstate',originUf:'SP',destinationUf:'MG',interstateRate:'12',adjustMva:true);
        $r=(new Calculator())->calculate($i);
        self::assertSame('50.243902',$r->details['used_mva']);
        self::assertGreaterThan(7200,$r->details['totals']['icms_st_minor']);
    }
    public function test_fcp_and_multiple_items_are_aggregated(): void
    {
        $i=$this->input(fcpRate:'2',items:[['description'=>'Item 2','merchandise_value'=>'500','mva'=>'30']]);
        $r=(new Calculator())->calculate($i);
        self::assertCount(2,$r->details['items']);
        self::assertSame(4100,$r->details['totals']['fcp_st_minor']);
    }
    private function input(string $operationType='internal',string $originUf='SP',string $destinationUf='SP',string $interstateRate='12',bool $adjustMva=false,string $fcpRate='0',array $items=[]): CalculationInput
    {
        return new CalculationInput('2026-08',$operationType,$originUf,$destinationUf,'1000','0','0','0','0','0','40','18',$interstateRate,$adjustMva,$fcpRate,'',$items);
    }
}
