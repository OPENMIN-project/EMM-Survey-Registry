<?php

namespace App\Providers;

use App\FieldGroup;
use App\FieldOption;
use App\Policies\FieldGroupPolicy;
use App\Policies\FieldOptionPolicy;
use App\Policies\SurveyFieldPolicy;
use App\Policies\SurveyPolicy;
use App\Survey;
use App\SurveyField;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Survey::class => SurveyPolicy::class,
        SurveyField::class => SurveyFieldPolicy::class,
        FieldOption::class => FieldOptionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
