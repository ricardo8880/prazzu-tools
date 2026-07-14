<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

final class SimplesNacionalCalculation extends Model
{
    protected $table = 'simples_nacional_calculations';

    protected $fillable = [
        'user_id',
        'session_key',
        'company_name',
        'reference_month',
        'annex',
        'rbt12_cents',
        'monthly_revenue_cents',
        'estimated_das_cents',
        'effective_rate',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'reference_month' => 'date:Y-m',
            'payload' => 'array',
            'effective_rate' => 'decimal:4',
        ];
    }
}
