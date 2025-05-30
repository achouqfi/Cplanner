<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoIndexation extends Model
{
    use HasFactory;

    protected $table = 'seo_indexations';

    protected $fillable = [
        'website_id',
        'url',
        'status',
        'verdict',
        'robots_state',
        'indexing_state',
        'page_fetch_state',
        'crawled_as',
        'last_crawled_at',
        'google_canonical',
        'user_canonical',
        'referring_urls',
        'mobile_usability_verdict',
        'inspection_link',
        'description',
    ];

    protected $casts = [
        'last_crawled_at' => 'datetime',
        'referring_urls' => 'array',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
