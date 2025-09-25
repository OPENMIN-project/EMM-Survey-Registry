<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Filters\BooleanFilter;

class NationalityType extends BooleanFilter
{
    /**
     * Apply the filter to the given query.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        $values = array_map(function ($value) {
            return (bool)$value;
        }, array_keys($value, true));
        if (count($values)) {
            return $query->whereIn('only_subnational', $values);
        }

        return $query;
    }

    /**
     * Get the filter's available options.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'National'    => 0,
            'Subantional' => 1
        ];
    }
}
