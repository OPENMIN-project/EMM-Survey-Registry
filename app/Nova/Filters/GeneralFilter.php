<?php

namespace App\Nova\Filters;

use App\UserRole;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class GeneralFilter extends Filter
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
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        $user = $request->user();

        switch ($value) {
            case 'mine':
                return $query->whereHas('users', function ($usersQuery) use ($user) {
                    $usersQuery->whereUserId($user->id);
                });
            case 'country':
                return $query->where('country', $user->country);
            case 'all':
            default:
                return $query;
        }
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
            'Mine'       => 'mine',
            'My Country' => 'country',
            'All'        => 'all'
        ];
    }
}
