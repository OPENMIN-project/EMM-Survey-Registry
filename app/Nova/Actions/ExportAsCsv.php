<?php

namespace App\Nova\Actions;

use App\Enum\FieldType;
use App\Export;
use App\Survey;
use App\SurveyField;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\ActionRequest;

class ExportAsCsv extends Action implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public $user;
    static $chunkCount = 10000000;
    public $timeout = 240;

    public function __construct()
    {
        $this->user = request()->user();
    }

    /**
     * Perform the action on the given models.
     *
     * @param \Laravel\Nova\Fields\ActionFields $fields
     * @param \Illuminate\Support\Collection    $models
     * @throws Exception
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $fields   = SurveyField::all()->reject(fn(SurveyField $field) => $field->isHeading() || $field->isSubHeading());
        $time     = microtime(true);
        $filePath = storage_path("app/exports/tmp-{$time}.csv");
        $export   = $this->user->exports()->create();
        try {
            $file = fopen($filePath, 'w+');

            $fields->each(function (SurveyField $field) use ($models, $file) {
                $line = [$field->field_code, $field->label];
                $models->each(function (Survey $model) use (&$line, $field) {
                    $value = property_exists($model->answers, $field->field_code) ?
                        $model->answers->{$field->field_code} : '';
                    if (is_array($value)) {
                        $value = collect($value)->map(function ($i) use ($field) {
                            if (in_array($i, ['-9', '-99', '-999'], true)) {
                                return $i . '.' . $field->labelForOptionValue($i);
                            }
                            return $field->labelForOptionValue($i);
                        })->filter()->join('; ');
                    } else if ($field->field_code === 'f_1_0') {
                        $value = $value . ' (' . $field->labelForOptionValue($value) . ')';
                    } else {
                        $flip = in_array($field->type,
                                [FieldType::TEXT, FieldType::URL, FieldType::LONG_TEXT, FieldType::DATE],
                                true) && !!$field->optionsAsArray();
                        if (!!$field->optionsAsArray() && array_key_exists($value, $field->optionsAsArray($flip))) {
                            $value = $flip ? $field->valueForOptionLabel($value) . '.' . $value : $value . '.' . $field->labelForOptionValue($value);
                        }
                    }
                    $line[] = $value;
                });
                fputcsv($file, $line);
            });
            fclose($file);

            $export->update([
                'duration' => number_format(microtime(true)-$time, 2),
                'status'   => 'done',
                'result'   => "<a target=\"_blank\" download href=\"{$this->getDownloadUrl($filePath)}\">Download file</a>"
            ]);

            return Action::message("Export {$export->id} generated successfully");
        } catch (Exception $e) {
            $export->update(['status' => 'failed', 'result' => $e->getMessage()]);
            throw $e;
        }
    }

    public function handleRequest(ActionRequest $request)
    {
        parent::handleRequest($request);

        return Action::message("Job started. You can follow the progress in 'Exports'");
    }
    /**
     * @param string $filePath
     * @return string
     */
    protected function getDownloadUrl(string $filePath): string
    {
        return URL::temporarySignedRoute('export-csv.download', now()->addDays(10), [
            'path'     => encrypt($filePath),
            'filename' => 'survey-export.csv',
        ]);
    }
}
