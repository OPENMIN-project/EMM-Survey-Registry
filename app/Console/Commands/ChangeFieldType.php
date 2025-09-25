<?php

namespace App\Console\Commands;

use App\Enum\DefaultFieldOptions;
use App\Enum\FieldType;
use App\Survey;
use App\SurveyField;
use BadMethodCallException;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChangeFieldType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ethmig:change-field-type {fieldCode} {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change a field\'s type';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $code = $this->argument('fieldCode');
        $type = $this->argument('type');
        if (!in_array($type, FieldType::values())) {
            $this->error("Type is invalid.");
            return 1;
        }

        $field = SurveyField::where('field_code', $code)->first();

        $slug = Str::camel('migrate-' . Str::slug($field->type) . '-' . Str::slug($type));

        try {
            $this->{$slug}($field);
        } catch (BadMethodCallException $e) {
            $this->warn("Migrating from '{$field->type}' to '{$type}' not implemented!");
            return 2;
        }

        return 0;
    }

    /**
     * @param SurveyField $field
     */
    public function migrateTextArray(SurveyField $field)
    {
        DB::beginTransaction();
        try {
            // get all options
            /** @var Collection $options */
            $options = Survey::select('id', "answers->{$field->field_code} as option")->pluck('option', 'id');
            $values  = $options->values()->unique();
            // split by ;
            $order      = 0;
            $newOptions = $values
                ->map(function ($value) {
                    return explode('; ', $value);
                })
                ->flatten()->unique()
                ->map(function ($value) use ($field, &$order) {
                    return [
                        'survey_field_id' => $field->id,
                        'label'           => $value,
                        'value'           => DefaultFieldOptions::test($value) ? DefaultFieldOptions::getDefaultValue($value) : Str::slug($value),
                        'order'           => $order += 10
                    ];
                });
            // add options to field_options table
            //batch insert new options
            $this->insertNewOptions($newOptions);
            //"TUR; ARA; PAN; URD; SND; SQI; BOS" => ["tur", "ara", ...]
            // update surveys answers.{field_code}
            $replaceArray = $newOptions->pluck('value', 'label');
            $options
                ->map(function ($values) use ($replaceArray) {
                    return collect(explode('; ', $values))->map(function ($value) use ($replaceArray) {
                        return $replaceArray[$value];
                    })->toJson();
                })
                ->each(function ($value, $id) use ($field) {
                    $rawValue = DB::raw("JSON_ARRAY(" . str_replace(['[', ']'], '', $value) . ")");
                    Survey::where('id', $id)->update([
                        "answers->{$field->field_code}" => $rawValue
                    ]);
                });
            //test
            if (!is_array(Survey::find($options->keys()->random(1)->first())->answers->{$field->field_code})) {
                throw new Exception("Failed to migrate field from text to array. Nothing changed.");
            }

            $field->update(['type' => FieldType::ARRAY]);
        } catch (Exception $e) {
            DB::rollBack();
            $this->error("Unknown error. Nothing changed.");
            throw $e;
        }
        $this->info("Success. Field {$field->field_code} is now of type array.");
        DB::commit();
    }

    private function migrateChoiceArray(SurveyField $field)
    {
        DB::beginTransaction();
        $field->update(['type' => FieldType::ARRAY]);
        DB::commit();
    }

    private function insertNewOptions($options)
    {
        DB::table('field_options')->insert($options->toArray());
    }

}
