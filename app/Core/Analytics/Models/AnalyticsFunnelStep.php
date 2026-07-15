<?php

namespace App\Core\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AnalyticsFunnelStep extends Model
{
    protected $fillable = ['funnel_id', 'position', 'name', 'event_names'];

    protected function casts(): array
    {
        return ['event_names' => 'array'];
    }

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(AnalyticsFunnel::class, 'funnel_id');
    }
}
