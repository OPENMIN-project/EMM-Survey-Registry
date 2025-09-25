<?php

namespace App\Nova;

use App\Nova\Filters\UserCountry;
use App\UserRole;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Pdewit\ExternalUrl\ExternalUrl;
use Illuminate\Validation\Rule;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\\User';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Select::make('Role')
                ->options([
                    UserRole::MASTER => "Super Admin",
                    UserRole::ADMIN  => "Admin",
                    UserRole::EDITOR => "Editor"
                ])
                ->sortable()
                ->displayUsingLabels()
                ->exceptOnForms(),

            Select::make('Role')
                ->options([
                    UserRole::MASTER => "Super Admin",
                    UserRole::ADMIN  => "Admin",
                    UserRole::EDITOR => "Editor"
                ])
                ->displayUsingLabels()
                ->onlyOnForms()
                ->rules(['required'])
                ->canSeeWhen('editRole', $this),

            Country::make('Country')
                ->displayUsingLabels()
                ->required()
                ->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Text::make('ORCID ID')
                ->sortable()
                ->rules(['nullable', 'string','regex:/^[0-9X]{4}-[0-9X]{4}-[0-9X]{4}-[0-9X]{4}$/',Rule::unique('users', 'orcid_id')->ignore($this->id)])
                ->creationRules('unique:users,orcid_id')
                ->placeholder('0000-0000-0000-0000')
                ->help('Must contain only digits and X\'s')
                ->onlyOnForms(),
            ExternalUrl::make('ORCID ID', function ($user) {
                return $user->orcid_id ? 'https://orcid.org/' . $user->orcid_id : null;
            })->onlyOnDetail(),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        /** @var \App\User $user */
        $user = $request->user();
        return ($user->can('viewAllUsers', $user)) ? $query : $query->where('id', $request->user()->id);
    }

    public static function availableForNavigation(Request $request)
    {
        return $request->user()->can('viewAllUsers', $request->user());
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new \App\Nova\Filters\UserRole(),
            new UserCountry(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
