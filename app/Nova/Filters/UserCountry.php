<?php

namespace App\Nova\Filters;

use App\Country;
use App\User;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserCountry extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return ($value) ? $query->where('country', $value): $query;
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return Country::whereIn('code', User::pluck('country')->filter()->toArray())->pluck('code', 'label');
    }
}
