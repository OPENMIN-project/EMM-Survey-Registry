<?php

namespace App\Enum;

class DefaultFieldOptions
{
    const OPTIONS  = [
        'Don\'t know'               => -9,
        'Not applicable'            => -999,
        'Information not available' => -99,
    ];
    const DEFAULTS = [
        '-9',
        '-99',
        '-999',
        'Don\'t know',
        'Not applicable',
        'Information not available'
    ];

    public static function test($value): bool
    {
        return in_array((string)$value, static::DEFAULTS, true);
    }

    public static function getDefaultValue(string $string)
    {
        return static::OPTIONS[$string];
    }
}
