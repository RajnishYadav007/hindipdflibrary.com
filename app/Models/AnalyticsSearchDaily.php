<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsSearchDaily extends Model
{
    use HasFactory;

    protected $table = 'analytics_search_daily';

    protected $fillable = [
        'date',
        'search_query',
        'locale',
        'search_count',
        'zero_result_count',
        'click_count',
    ];

    protected $casts = [
        'date' => 'date',
        'search_count' => 'integer',
        'zero_result_count' => 'integer',
        'click_count' => 'integer',
    ];
}
