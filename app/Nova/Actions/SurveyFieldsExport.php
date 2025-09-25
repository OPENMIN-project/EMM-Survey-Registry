<?php

namespace App\Nova\Actions;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class SurveyFieldsExport extends DownloadExcel implements WithMapping
{
    public function map($field): array
    {
        /** @var SurveyField $field */
        $options = collect($field->optionsAsArray())
        ->map (fn($Label, $key) => "{$key}. {$Label}")
        ->values()
        ->toArray();
        return [
            $field->id,
            $field->field_code,
            $field->label,
            ...$options,
        ];
    }
}
