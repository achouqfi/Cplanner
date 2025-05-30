<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id',
        'keyword',
        'page_url',
        'country',
        'device',
        'date',
        'clicks',
        'impressions',
        'ctr',
        'position',
    ];

    protected $casts = [
        'date' => 'date',
        'clicks' => 'integer',
        'impressions' => 'integer',
        'ctr' => 'float',
        'position' => 'float',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
