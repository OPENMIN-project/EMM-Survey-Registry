<?php

namespace App\Jobs;

use App\Enum\FieldType;
use App\SurveyField;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateFieldMapping implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fields = SurveyField::with('options')->get();
        $mappings = $fields->mapWithKeys(function ($field, $key) {
            return [
                $field['field_code'] => $this->getElasticsearchDataForField($field)];
        });
        $output = json_encode([
            'properties' => [
                'id'           => [
                    'type' => 'keyword',
                ],
                'my_all'       => [
                    'type' => 'text',
                ],
                'status'       => [
                    'type' => 'keyword',
                ],
                'country_name' => [
                    'type' => 'keyword'
                ],
                'answers'      => [
                    'type'       => 'object',
                    'properties' => $mappings
                ],
                'created_at'   => [
                    'type'   => 'date',
                    "format" => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                ],
                'updated_at'   => [
                    'type'   => 'date',
                    "format" => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                ],
            ]
        ]);

        $output_file = storage_path('mappings_output.json');
        if (file_exists($output_file)) {
            unlink($output_file);
        }

        file_put_contents(storage_path('mappings_output.json'), $output);

    }

    /**
     * @param $field
     * @return array
     */
    private function getElasticsearchDataForField($field)
    {
        switch ($field['type']) {
            case FieldType::CHOICE:
                return [
                    'type'    => 'keyword',
                    'copy_to' => 'my_all',
                ];
            case FieldType::NUMBER:
                return [
                    'type'    => 'integer',
                    'copy_to' => 'my_all'
                ];
            case FieldType::DATE:
                return [
                    'type' => 'keyword',
                ];
            default:
                $result = [
                    'type'     => 'text',
                    'copy_to'  => 'my_all',
                    'analyzer' => 'ethmig_analyzer',
                ];
                return array_merge($result, [
                    'fielddata' => true,
                    'fields'    => [
                        'raw' => [
                            'type' => 'keyword',
                        ]
                    ]
                ]);
        }
    }
}
