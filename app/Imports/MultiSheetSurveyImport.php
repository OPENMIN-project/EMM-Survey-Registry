<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetSurveyImport implements WithMultipleSheets, SkipsUnknownSheets
{
    use Importable;

    public function __construct()
    {
        ini_set('max_execution_time', 180);
        info("Started survey import with multiple sheets");
    }

    /**
     * @param string|int $sheetName
     */
    public function onUnknownSheet($sheetName)
    {
        info("Sheet {$sheetName} was skipped");
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            'NATIONAL SURVEYS' => new SurveysImport,
            'SUBNATIONAL SURVEYS' => new SurveysImport,
        ];
    }
}
