<?php


namespace App\Enum;


use ReflectionClass;

class FieldType
{
    const HEADING     = 'heading';
    const SUB_HEADING = 'sub-heading';
    const CHOICE      = 'choice';
    const ARRAY       = 'array';
    const TEXT        = 'text';
    const LONG_TEXT   = 'long-text';
    const DATE        = 'date';
    const URL         = 'url';
    const NUMBER      = 'number';

    /**
     * @return array
     * @throws \ReflectionException
     */
    public static function values()
    {
        $constants = (new ReflectionClass(new static))->getConstants();

        return array_values($constants);
    }
}
