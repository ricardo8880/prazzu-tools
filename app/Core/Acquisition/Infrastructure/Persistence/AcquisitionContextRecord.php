<?php

namespace App\Core\Acquisition\Infrastructure\Persistence;

use App\Core\Acquisition\Domain\Enums\AcquisitionContextStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class AcquisitionContextRecord extends Model
{
    protected $table = 'acquisition_contexts';

    protected $fillable = [
        'name',
        'keyword',
        'campaign_identifier',
        'status',
        'hero_title_before',
        'hero_title_line',
        'hero_title_highlight',
        'hero_description',
        'hero_search_placeholder',
        'tools_section_title',
        'cta_title',
        'cta_description',
        'cta_label',
        'cta_url',
        'cta_tool_slug',
        'primary_tool_slug',
    ];

    public function tools(): HasMany
    {
        return $this->hasMany(AcquisitionContextToolRecord::class, 'acquisition_context_id')
            ->orderBy('position')
            ->orderBy('id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(AcquisitionContextArticleRecord::class, 'acquisition_context_id')
            ->orderBy('position')
            ->orderBy('id');
    }

    protected function casts(): array
    {
        return [
            'status' => AcquisitionContextStatus::class,
        ];
    }
}
