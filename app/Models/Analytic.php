<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    use HasFactory;

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
    ];

    public $timestamps = false; // Disable Laravel's created_at/updated_at

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
