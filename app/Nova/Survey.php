<?php

namespace App\Nova;

use App\Enum\FieldType;
use App\Nova\Actions\ExportAsCsv;
use App\Nova\Actions\ImportSurveys;
use App\Survey as SurveyModel;
use App\Nova\Actions\CloneSurvey;
use Donkfather\NovaTableOfContentsField\NovaTableOfContentsField;
use App\Nova\Filters\{
    GeneralFilter,
    StatusFilter,
    SurveyType
};
use App\Nova\Metrics\SurveysPerStatus;
use App\Nova\Traits\HasDependenciesAndIndents;
use App\SurveyField as SurveyFieldModel;
use App\SurveyFieldsRepository;
use Carbon\Carbon;
use Donkfather\Indent\Indent;
use Donkfather\TextChoice\TextChoice;
use Illuminate\{
    Http\Request,
    Http\Resources\MergeValue,
    Support\Arr,
    Support\Facades\Cache,
    Support\Facades\Validator,
    Support\Str
};
use Inspheric\Fields\Indicator;
use Inspheric\NovaDefaultable\HasDefaultableFields;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use App\Nova\Fields\{
    NovaDependencyContainer,
    Panel,
    RadioButton
};
use Laravel\Nova\Fields\{
    BelongsToMany,
    DateTime,
    Field,
    Heading,
    ID,
    Number,
    Select,
};
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use OptimistDigital\MultiselectField\Multiselect;
use App\Enum\SurveyStatus;

class Survey extends Resource
{
    use HasDependenciesAndIndents, HasDefaultableFields;

    const CACHE_FIELDS_TTL = 50;
    const DEFAULT_ORDER    = 10;


    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = SurveyModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = null;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'answers',
    ];

    public static $with = [
        'users',
    ];

    /**
     * Build an "index" query for the given resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Builder   $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->getQuery()->orders = collect($query->getQuery()->orders)->map(function ($order) {
            if ($order['column'] === "answers->f_1_0") {
                $order['column'] = 'country_name';
            }
            return $order;
        })->toArray();

        return $query;
    }

    /**
     * Create a validator instance for a resource creation request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @throws \Exception
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public static function validatorForCreation(NovaRequest $request)
    {
        $fieldsValidationNames = SurveyFieldsRepository::validationAttributes();

        return Validator::make($request->all(), static::rulesForCreation($request), [], $fieldsValidationNames)
            ->after(function ($validator) use ($request) {
                static::afterValidation($request, $validator);
                static::afterCreationValidation($request, $validator);
            });
    }

    /**
     * @param NovaRequest $request
     * @param null        $resource
     * @throws \Exception
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public static function validatorForUpdate(NovaRequest $request, $resource = null)
    {
        $fieldsValidationNames = SurveyFieldsRepository::validationAttributes();

        return Validator::make($request->all(), static::rulesForUpdate($request), [], $fieldsValidationNames)
            ->after(function ($validator) use ($request) {
                static::afterValidation($request, $validator);
                static::afterUpdateValidation($request, $validator);
            });
    }

    public function title()
    {
        return $this->answers->f_1_3 ?? '-';
    }

    public function subtitle()
    {
        $name = $this->answers->f_1_4 ?? '-';
        return "Nat. name: " . $name;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return array
     */
    public function fields(Request $request)
    {
        return array_merge(
            [
                ID::make()->sortable(),
                BelongsToMany::make('Users')
                    ->canSeeWhen('manageOwnersField', $this->resource)->searchable(),
                NovaTableOfContentsField::make('Contents')->hideFromIndex(),
                Indicator::make('Status')
                    ->sortable()
                    ->labels(SurveyStatus::optionsWithLabels())
                    ->colors(SurveyStatus::colors()),
                Select::make('Status') // Status field for admins
                ->options(SurveyStatus::optionsWithLabels())
                    ->displayUsingLabels()
                    ->help('Before publishing, we will need to review your survey. Please send an email to <a target="_blank" href="mailto:sshoc.project@sciencespo.fr?subject=Survey%20record%3A%20EMM%20Survey%20Registry">sshoc.project@sciencespo.fr</a> with the subject line: <code style=\"font-weight: bold\">Survey record: EMM Survey Registry</code>')
                    ->rules(['required', 'in:' . implode(',', SurveyStatus::options())])
                    ->default(SurveyStatus::DRAFT)
                    ->onlyOnForms()
                    ->canSeeWhen('changeStatusAdmin', $this->resource),
                Select::make('Status') // Status field for editors
                ->options(SurveyStatus::optionsForEditors())
                    ->displayUsingLabels()
                    ->help('Before publishing, we will need to review your survey. Please send an email to <a target="_blank" href="mailto:sshoc.project@sciencespo.fr?subject=Survey%20record%3A%20EMM%20Survey%20Registry">sshoc.project@sciencespo.fr</a> with the subject line: <code style=\"font-weight: bold\">Survey record: EMM Survey Registry</code>')
                    ->withMeta(['value' => SurveyStatus::DRAFT])
                    ->onlyOnForms()
                    ->rules(['required', 'in:' . SurveyStatus::DRAFT . ',' . SurveyStatus::READY])
                    ->canSeeWhen('changeStatusEditor', $this->resource),
                DateTime::make('Updated at')->onlyOnDetail(),
                Number::make('Update count')->onlyOnDetail()
            ],
            (!$this->isIndexRequest($request)) ? [] : $this->getIndexFields(),
            ($this->isIndexRequest($request)) ? [] : $this->getAllFields(),
            [
                DateTime::make('Updated at')->onlyOnIndex()->sortable(),
            ]
        );
    }

    /**
     * @inheritDoc
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            new SurveysPerStatus()
        ];
    }

    /**
     * @return array
     */
    private function getIndexFields(): array
    {
        $cached = Cache::remember('index-fields', 5, function () {
            return
                SurveyFieldModel::with('options')
                    ->whereIn('field_code', ['f_1_0', 'f_1_3', 'f_1_4', 'f_1_5'])
                    ->get();
        });

        return $cached
            ->map(function ($field) {
                $fieldObject = $this->createFieldFor($field);

                if ($fieldObject === null) {
                    return null;
                }

                if ($fieldObject instanceof TextChoice) {
                    $fieldObject->resolveUsing(function ($value) {
                        if (Str::length($value) > 40) {
                            $value = Str::limit($value, 37);
                        }
                        return $value;
                    });
                }
                return $fieldObject->onlyOnIndex()->sortable();
            })
            ->filter()
            ->toArray();
    }

    /**
     * @throws \Exception
     * @return array
     */
    public function getAllFields(): array
    {
        return cache()->remember('survey-fields', self::CACHE_FIELDS_TTL, function () {
            return SurveyFieldModel::whereNull('parent_field_id')
                ->orderBy('order')
                ->with('fields', 'fields.options', 'fields.fields')
                ->get();
        })->map(function ($heading) {
            return (new Panel($heading->label, $heading->fields
                ->map(function ($field) {
                    return $this->getFieldObjectFrom($field);
                })))->limit(4)->withMeta(['id' => Str::slug($heading->label)]);
        })->all();
    }

    /**
     * @param SurveyFieldModel $field
     * @return NovaDependencyContainer|Field|null
     */
    private function getFieldObjectFrom(SurveyFieldModel $field)
    {
        /** @var Field|null $fieldObject */
        $fieldObject = $this->createFieldFor($field);

        if (!$fieldObject instanceof Field) {
            return $fieldObject;
        }

        $fieldObject->hideFromIndex();

        if (method_exists($fieldObject, 'help')) {
            $fieldObject->help($field->hint);
        }

        if ($field->required) {
            $fieldObject->required();
        }

        return ($field->only_subnational) ?
            NovaDependencyContainer::make([$fieldObject])
                ->dependsOn('answers->f_1_5', SurveyModel::SUBNATIONAL_TYPE)
            : $fieldObject;
    }

    /**
     * @param $field
     * @return MergeValue|Field|Panel|null
     */
    private function createFieldFor(SurveyFieldModel $field)
    {
        switch ($field->type) {
            case FieldType::SUB_HEADING:
               return new MergeValue(
                    $field->fields->prepend($field)->map(function ($field) {
                        return ($field->type === FieldType::SUB_HEADING) ?
                            Heading::make($field->label)
                            :
                            Indent::make($this->getFieldObjectFrom($field));
                    })
                );
            case FieldType::NUMBER:
                return Number::make($field->label, "answers->{$field->field_code}");
            case FieldType::TEXT:
            case FieldType::LONG_TEXT:
            case FieldType::URL:
                return TextChoice::make($field->label, "answers->{$field->field_code}")
                    ->options($field->optionsAsArray())
                    ->withMeta([
                        'extraAttributes' => [
                            'class' => 'pl-6'
                        ]
                    ]);
            case FieldType::ARRAY:
                return Multiselect::make($field->label, "answers->{$field->field_code}")
                    // ->displayUsing(function ($value) {
                    //     return $value;
                    // })
                    // ->resolveUsing(function ($value) {
                    //     return $value;
                    // })
                    ->taggable($field->field_code !== 'f_11_1')
                    ->fillUsing(function (NovaRequest $request, $model, $attribute, $requestAttribute) use ($field) {
                        $values              = $request->input($attribute) ?? [];

                        if ($field->field_code === 'f_11_1') {
                            $model->{$attribute} = $values;
                            return;
                        }
                        $existing            = $field->options()->whereIn('value', $values)->pluck('value');
                        $values              = collect($values)
                            ->diff($existing)
                            ->map(function ($missing) use ($field) {
                                return $field->options()->firstOrCreate(
                                    [
                                        'value' => Str::slug($missing),
                                    ],
                                    [
                                        'label' => $missing,
                                        'value' => Str::slug($missing),
                                        'order' => self::DEFAULT_ORDER
                                    ])->value;
                            })
                            ->merge($existing)
                            ->values();
                        $model->{$attribute} = $values->toArray();
                    })
                    ->options($field->optionsAsArray());
            case FieldType::DATE:
                return TextChoice::make($field->label, "answers->" . $field->field_code)
                    ->rules([
                        function ($input, $value, $fail) use ($field) {
                            if ($value === null || in_array($value,
                                    $field->optionsAsArray()) || $this->isValidDate($value)) {
                                return true;
                            }

                            return $fail("Invalid date or option");
                        },
                    ])
                    ->options($field->optionsAsArray());
            case FieldType::CHOICE:
                $options = $field->optionsAsArray();

                if ($field->options->count() < 4) {
                    if (!$field->required) {
                        $options = Arr::add($options, '', 'Not set');
                    }
                    return RadioButton::make($field->label, "answers->" . $field->field_code)
                        ->default('')
                        ->options($options);
                }

                return Select::make($field->label, "answers->" . $field->field_code)
                    ->displayUsingLabels()
                    ->options($options);
            default:
                return null;
        }
    }

    /**
     * @param $value
     * @return bool
     */
    private function isValidDate($value)
    {
        try {
            $partsCount = count(explode('-', $value));
            switch ($partsCount) {
                case 3:
                    Carbon::createFromFormat('Y-m-d', $value);
                    break;
                case 2:
                    Carbon::createFromFormat('Y-m', $value);
                    break;
                case 1:
                    Carbon::createFromFormat('Y', $value);
                    break;
            }
            return true;
        } catch (\Exception $e) {
        }

        return false;
    }


    /**
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new GeneralFilter,
            new SurveyType,
            new StatusFilter,
        ];
    }

    public function actions(Request $request)
    {
        return [
            (new ExportAsCsv()),
            (new ImportSurveys())->onlyOnIndex(),
            (new CloneSurvey())
                ->onlyOnTableRow()
                ->canRun(function () {
                    return true;
                })
        ];
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isIndexRequest(Request $request): bool
    {
        return $request instanceof ResourceIndexRequest;
    }
}
