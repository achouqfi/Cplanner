<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'order_column',
    ];

    public $translatable = [
        'name',
        'slug',
    ];

    protected $casts = [
        'name' => 'array',
        'slug' => 'array',
    ];
}
