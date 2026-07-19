<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ToolRunFavorite extends Model
{
    use HasUuids;

    protected $fillable = [
        'tool_run_id',
        'user_id',
    ];

    /** @return BelongsTo<ToolRun, $this> */
    public function run(): BelongsTo
    {
        return $this->belongsTo(ToolRun::class, 'tool_run_id');
    }
}
