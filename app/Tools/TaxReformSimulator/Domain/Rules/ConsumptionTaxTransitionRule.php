<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Domain\Rules;
final readonly class ConsumptionTaxTransitionRule { public const VERSION='2026.08.1'; public function forYear(int $year):array{if($year<2026||$year>2033)throw new \InvalidArgumentException('Ano inválido.');return match($year){2026=>['lf'=>100,'ls'=>100,'ibs'=>0,'note'=>'Ano-teste: CBS 0,9% e IBS 0,1%, observadas compensação/dispensa legal.'],2027,2028=>['lf'=>0,'ls'=>100,'ibs'=>0,'note'=>'PIS/Cofins extintos; CBS referência menos 0,1 p.p. e IBS 0,1%.'],2029=>['lf'=>0,'ls'=>90,'ibs'=>10,'note'=>'ICMS/ISS 90% e IBS 10%.'],2030=>['lf'=>0,'ls'=>80,'ibs'=>20,'note'=>'ICMS/ISS 80% e IBS 20%.'],2031=>['lf'=>0,'ls'=>70,'ibs'=>30,'note'=>'ICMS/ISS 70% e IBS 30%.'],2032=>['lf'=>0,'ls'=>60,'ibs'=>40,'note'=>'ICMS/ISS 60% e IBS 40%.'],2033=>['lf'=>0,'ls'=>0,'ibs'=>100,'note'=>'Novo modelo integral; ICMS/ISS extintos.']};} }
