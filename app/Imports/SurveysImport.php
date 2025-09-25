<?php

namespace App\Imports;

use App\AnswerParser;
use App\Enum\FieldType;
use App\QuestionParser;
use App\Survey;
use App\SurveyField;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Enum\SurveyStatus;

class SurveysImport implements ToCollection
{
    use Importable;

    const ID           = 0;
    const FIELD_CODE   = 1;
    const LABEL        = 2;
    const SURVEY_START = 3;
    private $id;
    private $surveyFields;

    public function __construct()
    {
        $this->id = Str::random(10);
        if (request() && request()->has('file')) {
            request()->validate([
                'file' => 'required|mimes:csv,txt'
            ]);
        }
        $this->surveyFields = SurveyField::whereNotIn('type', [FieldType::HEADING, FieldType::SUB_HEADING])->with('options')->get();
        info("IMPORT {$this->id}: Started survey import");
    }

    /**
     * @param Collection $collection
     * @throws ImportException
     */
    public function collection(Collection $collection)
    {
        DB::beginTransaction();
        try {
            $surveys = [];
            $collection
                ->reject(function ($row) {
                    // Reject rows that have the first column (eg. field code) empty
                    return $row[0] === null;
                })
                ->flatMap(function ($row) {
                    /** @var SurveyField $surveyField */
                    $surveyField = $this->getSurveyFieldFromId($row[self::ID]);
                    if (!$surveyField) {
                        return null;
                    }
                    $fields = [];
                    for ($i = self::SURVEY_START; $i < count($row); $i++) {
                        if ($row[$i] === null) continue;
                        $field_value = $this->processFieldValue($surveyField, $row[$i]);
                        $fields[$i - 1] = is_string($field_value) ? trim($field_value) : $field_value;
                    }
                    return [$surveyField->field_code => $fields];
                })
                ->filter()
                ->each(function ($values, $fieldCode) use (&$surveys) {
                    if ($surveyField = $this->getSurveyFieldFromCode($fieldCode)) {
                        foreach ($values as $survey_key => $value) {
                            if (is_array($value)) {
                                $surveys[$survey_key]['answers'][$fieldCode] = $value;
                                continue;
                            }
                            $answer = new AnswerParser($value, $surveyField);
                            $surveys[$survey_key]['answers'][$fieldCode] = $answer->getValue();
                        }
                    }
                });

            Model::unguard();
            collect($surveys)->map(function ($survey) {
                $survey['status'] = SurveyStatus::PUBLISHED;
                return Survey::create($survey)->id;
            });
        } catch (\Exception $e) {
            info("IMPORT {$this->id}: Survey import encountered error: {$e->getMessage()}");
            DB::rollBack();
            throw new ImportException("Something went wrong!", null, $e);
        }
        Db::commit();
        info("IMPORT {$this->id}: Survey import finished successfully");
    }

    /**
     * @param SurveyField $field
     * @param $value
     * @return array|mixed|string
     */
    private function processFieldValue(SurveyField $field, $value)
    {
        switch ($field->type) {
            case FieldType::DATE:
                return $this->fixDate($value);
            case FieldType::ARRAY:
                $options = explode(';', str_replace('; ', ';', trim($value, ' ;')));
                return collect($options)
                    ->map(function ($option) {
                        if (Str::startsWith($option, '-9')) {
                            $parts = explode('.', $option, 2);
                            return [
                                'label' => $parts[1],
                                'value' => $parts[0],
                            ];
                        }
                        return [
                            'label' => $option,
                            'value' => Str::slug($option),
                        ];
                    })
                    ->each(function ($option) use ($field) {
                        $field->options()->firstOrCreate([
                            'value' => $option['value']
                        ], array_merge($option, [
                            'order' => 10
                        ]));
                    })
                    ->map(function ($option) {
                        return $option['value'];
                    })->toArray();
            default:
                return $value;
        }
    }

    /**
     * @param $value
     * @return mixed|string
     */
    private function fixDate($value)
    {
        // Changes date format from d/m/y to y-m-d
        $value = str_replace('00/', '', trim($value, '/ '));
        $formattedDate = explode('/', $value);
        if (count($formattedDate) === 1) {
            return $formattedDate[0];
        } else if (count($formattedDate) === 2) {
            return $formattedDate[1] . '-' .
                str_pad($formattedDate[0], 2, '0', STR_PAD_LEFT);
        } else if (count($formattedDate) === 3) {
            return $formattedDate[2] . '-' .
                str_pad($formattedDate[1], 2, '0', STR_PAD_LEFT) . '-' .
                str_pad($formattedDate[0], 2, '0', STR_PAD_LEFT);
        }

        return $value;
    }

    /**
     * @param $code
     * @return mixed
     */
    private function getSurveyFieldFromCode($code)
    {
        return $this->surveyFields->where('field_code', $code)->first();
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getSurveyFieldFromId($id)
    {
        return $this->surveyFields->where('id', $id)->first();
    }
}
