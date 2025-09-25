<?php

use Faker\Generator as Faker;

$factory->define(App\FieldOption::class, function (Faker $faker) {
    return [
        'survey_field_id' => factory(\App\SurveyField::class),
        'label'           => $faker->randomElement([$faker->word, $faker->sentence]),
        'value'           => $faker->unique()->randomNumber(),
        'order'           => $faker->boolean,
    ];
});
