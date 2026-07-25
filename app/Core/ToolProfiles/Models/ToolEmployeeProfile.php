<?php

declare(strict_types=1);

namespace App\Core\ToolProfiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ToolEmployeeProfile extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'company_profile_id',
        'name',
        'document',
        'department',
        'role',
        'defaults',
    ];

    /** @return BelongsTo<ToolCompanyProfile, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(ToolCompanyProfile::class, 'company_profile_id');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'document' => 'encrypted',
            'defaults' => 'encrypted:array',
        ];
    }
}
