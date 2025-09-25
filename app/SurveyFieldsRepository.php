<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class SurveyFieldsRepository
 * @package App
 */
class SurveyFieldsRepository
{
    protected $fields;

    public static function boot()
    {
        $self = (new self);
        $self->setFields(SurveyField::all());

        return $self;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function validationAttributes()
    {
        return cache()->remember('survey-fields-validation-attributes', 5, function () {
            return cache()->remember('fields', 5, function () {
                return app('fields-repository')->fields()->pluck('name', 'field_code');
            })->mapWithKeys(function ($name, $key) {
                return ["answers->{$key}" => $name];
            })->toArray();
        });
    }

    /**
     * @return Collection
     */
    public function fields(): Collection
    {
        return $this->fields;
    }

    /**
     * @param Collection $fields
     */
    protected function setFields(Collection $fields)
    {
        $this->fields = $fields;
    }
}
