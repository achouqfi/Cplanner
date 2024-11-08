<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HeroSettings extends Settings
{

    public static function group(): string
    {
        return 'hero';
    }
}