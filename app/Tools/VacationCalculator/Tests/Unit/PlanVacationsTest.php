<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Unit;

use App\Tools\VacationCalculator\Application\Actions\PlanVacations;
use Tests\TestCase;

final class PlanVacationsTest extends TestCase
{
    public function test_it_calculates_multiple_employees(): void
    {
        $rows = app(PlanVacations::class)->execute([
            ['name'=>'Ana','monthly_salary'=>'3000','acquisition_start_date'=>'2025-01-01','vacation_start_date'=>'2026-01-05','unjustified_absences'=>0],
            ['name'=>'Bia','monthly_salary'=>'2400','acquisition_start_date'=>'2025-02-01','vacation_start_date'=>'2026-02-05','unjustified_absences'=>0],
        ]);
        self::assertCount(2, $rows);
        self::assertSame('Ana', $rows[0]['name']);
        self::assertArrayHasKey('summary', $rows[0]['result']);
    }
}
