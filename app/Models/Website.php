<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'domain', 'disabled', 'tracking_id', 'is_active', 'is_verified'];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function pageViews()
    {
        return $this->hasMany(PageView::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function seoIssues()
    {
        return $this->hasMany(SeoIssue::class);
    }
}
