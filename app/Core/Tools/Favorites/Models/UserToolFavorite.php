<?php

declare(strict_types=1);

namespace App\Core\Tools\Favorites\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class UserToolFavorite extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'tool_slug',
    ];
}
