<?php

use App\Enum\SurveyStatus;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Survey::class, function (Faker $faker) {
    return [
        'status' => SurveyStatus::DRAFT,
        'country_name' => 'Unknown',
        'country' => $countryCode = \App\Country::first()->code,
        'answers' => [
            'f_1_0' => $countryCode
        ]
    ];
});
