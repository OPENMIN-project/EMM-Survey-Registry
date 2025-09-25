<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FieldOption extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        parent::created(function () {
            cache()->forget('options-list');
            cache()->forget('survey-fields');
        });
        parent::updated(function () {
            cache()->forget('options-list');
            cache()->forget('survey-fields');
        });
    }

    public function survey_field()
    {
        return $this->belongsTo(SurveyField::class);
    }
}
