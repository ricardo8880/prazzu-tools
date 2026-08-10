<?php

declare(strict_types=1);

namespace App\Tools\InvoiceWithholdingCalculator\Tests\Unit;

use App\Tools\InvoiceWithholdingCalculator\Application\Data\CalculationInput;
use App\Tools\InvoiceWithholdingCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_common_federal_withholdings_are_calculated(): void
    {
        $r=(new Calculator())->calculate($this->input());
        self::assertSame('R$ 10.000,00',$r->summary[0]->value);
        self::assertSame('R$ 615,00',$r->summary[1]->value);
        self::assertSame('R$ 9.385,00',$r->summary[2]->value);
        self::assertSame(15000,$r->details['taxes']['irrf']['withheld_minor']);
        self::assertSame(6500,$r->details['taxes']['pis']['withheld_minor']);
        self::assertSame(30000,$r->details['taxes']['cofins']['withheld_minor']);
        self::assertSame(10000,$r->details['taxes']['csll']['withheld_minor']);
    }
    public function test_custom_base_percentage_reduces_a_tax_base(): void
    {
        $i=$this->input(applyInss:true,inssRate:'11',inssBase:'50'); $r=(new Calculator())->calculate($i);
        self::assertSame(55000,$r->details['taxes']['inss']['withheld_minor']);
    }
    public function test_multiple_notes_are_aggregated(): void
    {
        $i=$this->input(notes:[['description'=>'Nota 2','value'=>'5.000,00']]); $r=(new Calculator())->calculate($i);
        self::assertSame('R$ 15.000,00',$r->summary[0]->value); self::assertCount(2,$r->details['notes']);
    }
    private function input(bool $applyInss=false,string $inssRate='11',string $inssBase='100',array $notes=[]): CalculationInput
    {
        return new CalculationInput('2026-08','NF-1','Consultoria','10.000,00',true,'1.5','100',$applyInss,$inssRate,$inssBase,false,'2','100',true,'0.65','100',true,'3','100',true,'1','100',$notes);
    }
}
