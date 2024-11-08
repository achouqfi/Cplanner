<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PrivacySettings extends Settings
{

    public static function group(): string
    {
        return 'privacy';
    }
}