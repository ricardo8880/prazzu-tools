<?php

namespace App\Core\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class AnalyticsFunnel extends Model
{
    protected $fillable = ['name', 'description', 'identity_type', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function steps(): HasMany
    {
        return $this->hasMany(AnalyticsFunnelStep::class, 'funnel_id')->orderBy('position');
    }
}
