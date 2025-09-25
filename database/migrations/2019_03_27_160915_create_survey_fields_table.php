<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveyFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_fields', function (Blueprint $table) {
            $table->increments('id');

            $table->string('field_code')->unique();
            $table->unsignedInteger('order')->default(0);
            $table->string('code');
            $table->string('name');
            $table->string('type');
            $table->text('hint')->nullable();
            $table->boolean('required')->default(false);
            $table->boolean('sortable')->default(true);
            $table->boolean('simple_filter')->default(false);
            $table->boolean('advanced_filter')->default(false);
            $table->boolean('list_view')->default(false);
            $table->boolean('no_front')->default(true);
            $table->boolean('only_subnational')->default(false);
            $table->unsignedInteger('parent_field_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_fields');
    }
}
