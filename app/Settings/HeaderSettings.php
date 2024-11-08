<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HeaderSettings extends Settings
{

    public static function group(): string
    {
        return 'header';
    }
}