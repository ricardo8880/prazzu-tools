<?php

declare(strict_types=1);

namespace App\Core\ToolProfiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ToolCompanyProfile extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'name',
        'legal_name',
        'document',
        'office_name',
        'accountant_name',
        'accountant_registration',
    ];

    /** @return HasMany<ToolEmployeeProfile, $this> */
    public function employees(): HasMany
    {
        return $this->hasMany(ToolEmployeeProfile::class, 'company_profile_id');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['document' => 'encrypted'];
    }
}
