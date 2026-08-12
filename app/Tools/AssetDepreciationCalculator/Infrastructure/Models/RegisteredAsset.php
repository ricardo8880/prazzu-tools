<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

final class RegisteredAsset extends Model
{
    protected $table = 'asset_depreciation_registered_assets';

    /** @var list<string> */
    protected $fillable = ['user_id', 'name', 'value_minor', 'useful_life_years', 'method'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'value_minor' => 'integer',
            'useful_life_years' => 'integer',
        ];
    }
}
