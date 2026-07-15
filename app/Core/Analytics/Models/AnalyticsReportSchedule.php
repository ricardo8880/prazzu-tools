<?php

declare(strict_types=1);

namespace App\Core\Analytics\Models;

use Illuminate\Database\Eloquent\Model;

final class AnalyticsReportSchedule extends Model
{
    protected $fillable = ['name', 'frequency', 'format', 'filters', 'is_active', 'next_run_at', 'last_run_at', 'last_file_path', 'last_error'];

    protected function casts(): array
    {
        return ['filters' => 'array', 'is_active' => 'boolean', 'next_run_at' => 'immutable_datetime', 'last_run_at' => 'immutable_datetime'];
    }
}
