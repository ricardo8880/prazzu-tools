<?php

namespace App\Core\Acquisition\Infrastructure\Persistence;

use App\Core\Acquisition\Domain\Enums\AcquisitionContextToolPlacement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AcquisitionContextToolRecord extends Model
{
    protected $table = 'acquisition_context_tools';

    protected $fillable = [
        'acquisition_context_id',
        'tool_slug',
        'placement',
        'position',
    ];

    public function context(): BelongsTo
    {
        return $this->belongsTo(AcquisitionContextRecord::class, 'acquisition_context_id');
    }

    protected function casts(): array
    {
        return [
            'placement' => AcquisitionContextToolPlacement::class,
            'position' => 'integer',
        ];
    }
}
