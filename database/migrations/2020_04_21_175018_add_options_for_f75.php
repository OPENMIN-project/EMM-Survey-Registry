<?php

use App\SurveyField;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptionsForF75 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $field = SurveyField::where('field_code', 'f_7_5')->first();
        if (!$field) return;
        DB::table('field_options')->insert([
            [
                'survey_field_id' => $field->id,
                'label'           => 'Don\'t know',
                'value'           => -9,
                'order'           => 10
            ],
            [
                'survey_field_id' => $field->id,
                'label'           => 'Information not available',
                'value'           => -99,
                'order'           => 20
            ],
            [
                'survey_field_id' => $field->id,
                'label'           => 'Not applicable',
                'value'           => -999,
                'order'           => 30
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var SurveyField $field */
        $field = SurveyField::byCode('f_7_5');
        if ($field) {
            $field->options()->whereIn('value', [-9, -99, -999])->delete();
        }
    }
}
