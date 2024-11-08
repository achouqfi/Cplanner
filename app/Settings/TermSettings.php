<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TermSettings extends Settings
{

    public static function group(): string
    {
        return 'Term';
    }
}