<?php

namespace Tests\Feature;

use App\Enum\DefaultFieldOptions;
use App\Enum\FieldType;
use App\FieldOption;
use App\Survey;
use App\SurveyField;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;

class ChangeFieldTypeCommandTest extends TestCase
{

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_text_to_array()
    {
        $field = factory(SurveyField::class)->create([
            'type' => FieldType::TEXT
        ]);

        $survey1 = factory(Survey::class)->create([
            'answers' => [
                'f_1_0'            => \App\Country::first()->code,
                $field->field_code => "TEST1; TEST2"
            ]
        ]);
        $survey2 = factory(Survey::class)->create([
            'answers' => [
                'f_1_0'            => \App\Country::first()->code,
                $field->field_code => "Not applicable"
            ]
        ]);

        $this->artisan("ethmig:change-field-type {$field->field_code} " . FieldType::ARRAY)
            ->assertExitCode(0);

        $this->assertEquals(FieldType::ARRAY, $field->refresh()->type);
        $this->assertDatabaseHas('field_options', [
            'survey_field_id' => $field->id,
            'label'           => 'TEST1',
            'value'           => Str::slug('TEST1')
        ]);
        $this->assertDatabaseHas('field_options', [
            'survey_field_id' => $field->id,
            'label'           => 'Not applicable',
            'value'           => DefaultFieldOptions::getDefaultValue('Not applicable')
        ]);
        $this->assertTrue(is_array($survey1->refresh()->answers->{$field->field_code}));
        $this->assertTrue(is_array($survey2->refresh()->answers->{$field->field_code}));
    }

    public function test_choice_to_array()
    {
        /** @var SurveyField $field */
        $field = factory(SurveyField::class)->create([
            'type' => FieldType::CHOICE
        ]);

        /** @var Collection<FieldOption> $options */
        $options = factory(FieldOption::class, 3)->create([
            'survey_field_id' => $field->id,
        ]);

        factory(Survey::class)->create([
            'answers' => [
                'f_1_0'            => \App\Country::first()->code,
                $field->field_code => $options->first()->value
            ]
        ]);

        $this->artisan("ethmig:change-field-type {$field->field_code} " . FieldType::ARRAY)
            ->assertExitCode(0);

        $this->assertEquals(FieldType::ARRAY, $field->refresh()->type);
        $this->assertDatabaseHas('field_options', [
            'survey_field_id' => $field->id,
            'label'           => $options[0]->label,
            'value'           => $options[0]->value
        ]);
        $this->assertDatabaseHas('field_options', [
            'survey_field_id' => $field->id,
            'label'           => $options[1]->label,
            'value'           => $options[1]->value
        ]);
    }
}
