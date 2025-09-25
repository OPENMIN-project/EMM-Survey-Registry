<?php

namespace App\Nova\Fields;

use Illuminate\Http\Resources\MergeValue;

class Panel extends \Laravel\Nova\Panel
{

    /**
     * Prepare the given fields.
     *
     * @param \Closure|array $fields
     * @return array
     */
    protected function prepareFields($fields)
    {
        return collect(is_callable($fields) ? $fields() : $fields)->each(function ($field) {
            if ($field instanceof MergeValue) {
                $this->prepareFields($field->data);
            } else {
                $field->panel = $this->name;
            }
        })->all();
    }
}
