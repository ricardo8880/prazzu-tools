<?php

namespace App\Core\Analytics\Models;

use Illuminate\Database\Eloquent\Model;

final class AnalyticsInsight extends Model
{
    protected $fillable = ['fingerprint','type','severity','title','message','recommendation','subject_type','subject_slug','metric_name','current_value','previous_value','change_percent','status','metadata','period_start','period_end','generated_at','resolved_at'];

    protected function casts(): array
    {
        return ['metadata'=>'array','current_value'=>'float','previous_value'=>'float','change_percent'=>'float','period_start'=>'date','period_end'=>'date','generated_at'=>'immutable_datetime','resolved_at'=>'immutable_datetime'];
    }
}
