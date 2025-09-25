<?php

namespace App\Http\Controllers;

use App\CodebookXMLDecorator;
use App\Enum\FieldType;
use App\Enum\SurveyStatus;
use App\Survey;
use App\SurveyField;
use App\SurveyFieldsRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Scout\Builder;
use ScoutElastic\Builders\FilterBuilder;
use ScoutElastic\Facades\ElasticClient;
use Throwable;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        set_time_limit(0);
        $query = $this->applyFilters($request);

        $searchResults = $query->paginate(25)->appends('score');

        $searchPayload                  = $query->buildPayload();
        $filtersPayload["body"]["size"] = 0;
        $filtersPayload["body"]["aggs"] = $this->filterAggs($searchPayload[0]);
        $filters                        = ElasticClient::search($filtersPayload);

        return response()->json([
            'data'    => $searchResults,
            'filters' => $this->mapFilterAggs($filters),
        ]);
    }

    public function xmlShow(Request $request, Survey $survey)
    {
        $outputFileName = $survey->id . '-ethmigsurveydataDDIPrototype-' . time() . '.xml';
        return response()->streamDownload(function () use ($survey) {
            echo file_get_contents(resource_path('views/ddi/_header.txt'));
            echo CodebookXMLDecorator::from($survey);
        }, $outputFileName, [
            'Content-Type' => 'text/xml'
        ]);
    }

    public function xmlSearch(Request $request)
    {
        $query = $this->applyFilters($request)->take(10000);

        return $this->sendXmlResponse($query);
    }

    protected function sendXmlResponse(Builder $query)
    {
        set_time_limit(1200);
        $outputFileName = 'ethmigsurveydataDDIPrototype-' . time() . '.xml';
        return response()->streamDownload(function () use ($query) {
            echo file_get_contents(resource_path('views/ddi/_header.txt'));
            echo "<root>";
            $page = 0;
            ob_start();
            try {
                do {
                    $surveys = $query->paginate(500, 'page', $page);
                    $page += 1;
                    foreach ($surveys as $survey) {
                        echo CodebookXMLDecorator::fromCache($survey);
                    }
                    ob_flush();
                } while ($surveys->hasMorePages());
            } catch (Throwable $t) {
                ob_end_clean();
                throw $t;
            }
            ob_end_flush();
            echo '</root>';
        }, $outputFileName, [
            'Content-Type' => 'text/xml'
        ]);
    }

    /**
     * @param Request $request
     * @return Builder
     */
    protected function applyFilters(Request $request): Builder
    {
        $fields = app(SurveyFieldsRepository::class)->fields();
        $text   = $request->input('text', '*') ?? '*';
        /** @var FilterBuilder $query */
        $query = Survey::search(strtolower($text));
        $query->where('status', SurveyStatus::PUBLISHED);
        $filters = $request->input('filters', []);
        collect($filters)->each(function ($value, $field_code) use (&$query, $fields) {
            /** @var SurveyField $field */
            /** @var Collection $fields */
            $field = $fields->where('field_code', $field_code)->first();
            $key   = "answers.{$field_code}";
            $key   .= ($field->shouldUseRaw()) ? '.raw' : '';
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                switch ($field_code) {
                    case 'f_1_11':
                        $query->where($key, '>=', $value);
                        break;
                    case 'f_1_12':
                        $query->where($key, '<=', $value);
                        break;
                }
            }
        });

        $query->orderBy(request('sort.field', 'country_name'), request('sort.direction', 'asc'));
        if ($text && $text !== '*') {
            $query->orderBy('_score', 'desc');
        }

        return $query;
    }

    /**
     * @param array $searchPayload
     * @return array
     */
    private function filterAggs(array $searchPayload)
    {
        $aggs          = [];
        $activeFilters = collect(data_get($searchPayload, 'body.query.bool.filter.bool.must'));
        $fields        = SurveyField::usedAsFilter()
            ->with('options', 'parent')
            ->orderBy('order')
            ->get();

        $fields->each(function (SurveyField $field) use (&$aggs, $activeFilters) {
            $fieldKey = "{$field->field_code}";
            if ($field->type === FieldType::NUMBER) {
                data_set($aggs, "$fieldKey.stats", [
                    'field' => "answers.{$field->field_code}"
                ]);
            } else if ($field->type === FieldType::DATE) {
                data_set($aggs, "$fieldKey.stats", [
                    'field'  => "answers.{$field->field_code}.raw",
                    'format' => 'yyyy',
                ]);
            } else if (in_array($field->type,
                [FieldType::TEXT, FieldType::ARRAY, FieldType::CHOICE, FieldType::LONG_TEXT])) {
                $fieldKey    = "{$field->field_code}.aggs.filtered_{$field->field_code}";
                $field_in_es = "answers.{$field->field_code}";
                $field_in_es .= ($field->shouldUseRaw()) ? ".raw" : "";
                data_set($aggs, "$fieldKey.terms", [
                    'field' => $field_in_es,
                    'size'  => 200,
                ]);
                /** @var \Illuminate\Support\Collection $fieldFilters */
                $fieldFilters = $activeFilters->filter(function ($filter) use ($field, $field_in_es) {
                    return array_key_first(Arr::first($filter)) !== $field_in_es;
                });
                if ($fieldFilters->isNotEmpty()) {
                    $aggs[$field->field_code]['filter']['bool']['must'] = $fieldFilters->values()->toArray();
                }
            }
            data_set($aggs, "$fieldKey.meta", [
                'field_code'            => $field->field_code,
                'field_type'            => $field->type,
                'field_order'           => $field->order,
                'field_filter_simple'   => $field->simple_filter,
                'field_filter_advanced' => $field->advanced_filter,
                'field_name'            => $field->code . '. ' . $field->name,
                'field_options_order'   => $field->options->pluck('order', 'value'),
                'field_options'         => $field->optionsAsArray(),
            ]);
            if ($field->parent) {
                data_set($aggs, "$fieldKey.meta.field_parent", [
                    'name' => $field->parent->label,
                    'code' => $field->parent->field_code,
                    'type' => $field->parent->type,
                ]);
            }
        });

        $aggs = [
            'all_surveys' => [
                'global' => (object) [],
                'aggs'   => array_filter($aggs)
            ]
        ];

        return $aggs;
    }

    private function mapFilterAggs($filters)
    {
        $filters = $filters['aggregations']['all_surveys'];
        unset($filters['doc_count']);
        unset($filters['meta']);
        $filters = collect($filters)->mapWithKeys(function ($filter, $field_code) {
            $filter = array_key_exists("filtered_{$field_code}", $filter) ? $filter["filtered_{$field_code}"] : $filter;
            $result = [
                'name'            => $filter['meta']['field_name'],
                'parent'          => $filter['meta']['field_parent'] ?? [],
                'order'           => $filter['meta']['field_order'],
                'code'            => $field_code,
                'type'            => $filter['meta']['field_type'],
                'raw'             => in_array($filter['meta']['field_type'], ['text', 'array']),
                'filter_simple'   => $filter['meta']['field_filter_simple'],
                'filter_advanced' => $filter['meta']['field_filter_advanced'],
            ];
            if ($filter['meta']['field_type'] === FieldType::DATE) {
                $result = array_merge($result, [
                    'min_date' => data_get($filter, 'min_as_string', null),
                    'max_date' => data_get($filter, 'max_as_string', null),
                ]);
            } else if ($filter['meta']['field_type'] === FieldType::NUMBER) {
                $result = array_merge(
                    $result,
                    [
                        'stats' => [
                            'min' => $filter['min'],
                            'max' => $filter['max'],
                        ]
                    ]
                );
            } else if (in_array($filter['meta']['field_type'], [FieldType::SUB_HEADING, FieldType::HEADING])) {
                $result = array_merge(
                    $result,
                    ['fields' => []]
                );
            } else {
                $result = array_merge($result, [
                    'type'    => $result['type'] === FieldType::ARRAY ? FieldType::ARRAY : FieldType::CHOICE,
                    'options' => collect($filter['buckets'])->map(function ($option) use ($filter) {
                        return [
                            'value'     => ($option['key'] === 'N/A') ? null : $option['key'],
                            'doc_count' => Arr::get($option, 'doc_count'),
                            'order'     => Arr::get($filter['meta']['field_options_order'], $option['key'], 999),
                            'label'     => Arr::get($filter['meta']['field_options'], $option['key'], $option['key']),
                        ];
                    })->sortBy('order')->values()->toArray(),
                ]);
            }

            return [$field_code => $result];
        })->sortBy('order');

        return $filters;
    }

    public function viewFields(Request $request)
    {
        return SurveyField::where('list_view', true)->get();
    }

    public function sortableFields(Request $request)
    {
        return SurveyField::where('sortable', true)->get();
    }
}
