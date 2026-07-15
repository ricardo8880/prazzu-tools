<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

final class MarginMarkupShare extends Model
{
    protected $fillable = [
        'tool_run_id', 'user_id', 'token', 'access_code_hash', 'expires_at', 'revoked_at',
    ];

    protected $hidden = ['access_code_hash'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
        ];
    }

    public function isAvailable(): bool
    {
        return $this->revoked_at === null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function isProtected(): bool
    {
        return $this->access_code_hash !== null;
    }
}
