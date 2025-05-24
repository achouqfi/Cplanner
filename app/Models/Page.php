<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['website_id', 'path', 'title', 'status_code'];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function seoIssues()
    {
        return $this->hasMany(SeoIssue::class);
    }
}
