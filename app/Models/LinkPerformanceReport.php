<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkPerformanceReport extends Model
{
    protected $fillable = [
        'url',
        'domain',
        'ip_address',
        'location',
        'load_time_seconds',
        'content_length',
        'readability_score',
        'performance_score',
        'structure_score',
        'gtmetrix_grade',
        'web_vitals',
        'html_elements',
        'technologies',
        'monetization',
        'json_ld_schemas',
        'recommendations',
        'langchain_summary',
    ];

    protected $casts = [
        'web_vitals' => 'array',
        'html_elements' => 'array',
        'technologies' => 'array',
        'monetization' => 'array',
        'json_ld_schemas' => 'array',
        'recommendations' => 'array',
    ];
}
