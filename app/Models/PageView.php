<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'website_id',
        'path',
        'full_url',
        'referrer',
        'browser',
        'os',
        'country',
        'device',
        'ip_address',
        'viewed_at',
        'source',
        'date',
        'pageviews',
        'sessions',
        'users',
        'bounce_rate',
        'engagement_time',
        'events',
        'revenue',
    ];


    protected $casts = [
        'viewed_at' => 'datetime',
        'date' => 'date', 
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
