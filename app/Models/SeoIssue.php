<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoIssue extends Model
{
    use HasFactory;

    protected $fillable = ['website_id', 'page_id', 'type', 'message', 'detected_at'];

    protected $casts = [
        'detected_at' => 'datetime',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
