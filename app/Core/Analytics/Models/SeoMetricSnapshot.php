<?php

namespace App\Core\Analytics\Models;

use App\Blog\Models\BlogPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SeoMetricSnapshot extends Model
{
    protected $table = 'analytics_seo_metric_snapshots';

    protected $fillable = [
        'blog_post_id', 'metric_date', 'source', 'search_type', 'device', 'country_code',
        'clicks', 'impressions', 'average_position', 'discover_clicks',
        'discover_impressions', 'news_clicks', 'news_impressions',
        'rich_result_clicks', 'rich_result_impressions',
    ];

    protected function casts(): array
    {
        return [
            'metric_date' => 'immutable_date',
            'average_position' => 'decimal:2',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }
}
