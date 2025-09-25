<?php

namespace App;

use DOMDocument;
use Illuminate\Database\Eloquent\Collection;

class CodebookXMLDecorator
{

    public static function fromCache(Survey $survey) {
        $key = md5($survey->id . $survey->updated_at->timestamp);
        return cache()->rememberForever("ddi:{$key}", function () use ($survey) {
            return static::from($survey);
        });
    }
    /**
     * @throws \Throwable
     * @return string
     * @var Survey
     */
    public static function from(Survey $survey): string
    {
        /** @var Collection $fields */
        $fields = app(SurveyFieldsRepository::class)->fields();
        $answer = function ($field_code, $raw = false) use ($survey) {
            if ($field_code instanceof SurveyField) {
                $field_code = $field_code->field_code;
            }
            return $raw ?
                field_value_raw($survey, $field_code) :
                field_value($survey, $field_code);
        };

        return static::formatXML(view('ddi._codeblock', [
            'a'       => $answer,
            'fields'  => $fields,
            'survey'  => $survey,
            'answers' => $survey->answers
        ])->render());
    }

    public static function formatXML($contents)
    {
        try {
            $doc                     = new DOMDocument('1.0');
            $doc->formatOutput       = true;
            $doc->preserveWhiteSpace = false;            
            $doc->loadXML($contents);
            return $doc->saveXML($doc->firstChild, LIBXML_NOEMPTYTAG);
        } catch (\Exception $error) {
            logger()->error('Error encountered while parsing surveys to XML',[$error,$contents]);
            return "";
        }
    }
}
