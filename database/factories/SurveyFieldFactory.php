<?php

use App\Enum\FieldType;
use App\FieldOption;
use App\SurveyField;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\SurveyField::class, function (Faker $faker) {
    return [
        'field_code' => $faker->unique()->regexify('/\d_\d\d?a?/'),
        'code'       => $faker->unique()->word,
        'name'       => $faker->sentence,
        'type'       => $faker->randomElement(FieldType::values()),
    ];
});

$factory->afterCreating(SurveyField::class, function ($surveyField) {
    if ($surveyField->type === FieldType::CHOICE) {
        factory(FieldOption::class, rand(2, 5))->create([
            'survey_field_id' => $surveyField->id,
        ]);
    }
});
