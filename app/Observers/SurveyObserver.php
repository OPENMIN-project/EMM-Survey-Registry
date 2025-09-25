<?php

namespace App\Observers;

use App\Country;
use App\Enum\SurveyStatus;
use App\Events\SurveyStatusChangedToReady;
use App\Survey;
use Illuminate\Support\Arr;

class SurveyObserver
{
    public function saving(Survey $survey)
    {
        $fields = [];
        collect($survey->answers)->each(function ($value, $field) use (&$fields) {
            if ($value !== null) {
                $fields[$field] = $value;
            }
        });
        $this->handleCountryField($survey, $fields);
        $survey->answers = $fields;

        return $survey;
    }

    /**
     * Handle the survey "created" event.
     *
     * @param \App\Survey $survey
     * @return void
     */
    public function created(Survey $survey)
    {
        if (auth()->check()) {
            $survey->users()->attach(auth()->id());
        }
    }

    public function updating(Survey $survey)
    {
        $survey->update_count++;
    }

    private function handleCountryField(Survey &$survey, $fields)
    {
        $survey->country = $fields['f_1_0'] ?? null;
        $survey->country_name = Country::where('code', $fields['f_1_0'] ?? null)->first()->label ?? "Unknown";
    }
}
