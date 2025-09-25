<?php

namespace App\Nova;

use App\Enum\FieldType as FieldTypeEnum;
use App\Nova\Actions\SurveyFieldsExport;
use App\Nova\Filters\FieldType;
use App\Nova\Filters\NationalityType;
use App\SurveyField as SurveyFieldModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Laravel\Nova\Nova;

class SurveyField extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\SurveyField';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'label';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'field_code',
        'code',
        'name',
        'type',
    ];

    public static $with = [
        'parent'
    ];

    public static function label()
    {
        return "Fields";
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        if (!$this->isIndexRequest($request)) {
            $nextOrderValue = SurveyField::max('order') + 10;
        }

        return [
            ID::make('Id')->sortable(),
            Select::make('Type')
                ->options([
                    FieldTypeEnum::HEADING => 'Heading',
                    FieldTypeEnum::SUB_HEADING => 'Sub Heading',
                    FieldTypeEnum::TEXT => 'Text',
                    FieldTypeEnum::LONG_TEXT => 'Long text',
                    FieldTypeEnum::CHOICE => 'Choice',
                    FieldTypeEnum::DATE => 'Date',
                    FieldTypeEnum::URL => 'Url',
                    FieldTypeEnum::ARRAY => 'Array',
                    FieldTypeEnum::NUMBER => 'Number',
                ])
                ->displayUsingLabels()
                ->readonly(function (NovaRequest $request) {
                    return $request->isUpdateOrUpdateAttachedRequest();
                })
                ->rules(['required']),
            BelongsTo::make('Parent', 'parent', SurveyField::class)
                ->withoutTrashed()
                ->rules(['required_unless:type,'.FieldTypeEnum::HEADING])
                ->nullable(),
            Text::make('Field Name', 'name')
                ->onlyOnIndex()
                ->sortable()
                ->resolveUsing(function ($value) {
                    return Str::limit($value, 40);
                }),
            Text::make('Name')
                ->rules(['required', 'min:1'])
                ->hideFromIndex(),
            Text::make('Code')
                ->rules(['required', 'min:1', function ($attribute, $value, $fail) {
                    $field_code = SurveyFieldModel::slugifyCode(request()->input('code'), request()->input('type'));
                    if ($field = SurveyField::where('field_code', $field_code)->where('id', '!=', request()->route('resourceId'))->first()) {
                        $url = url('/nova/resources/survey-fields', ['resourceId' => $field->id]);
                        $fail("Code already taken by: <a href=\"{$url}\" target='_blank'><strong>{$field->label}</strong></a>");
                    };
                }])
                ->withMeta(['placeholder' => 'ex: 5.4'])
                ->sortable(),
            Text::make('Field Code')
                ->onlyOnDetail(),
            Text::make('Hint')->hideFromIndex(),
            Number::make("Order")->sortable()
                ->default($nextOrderValue ?? 0),
            Boolean::make("Required"),
            Boolean::make("Simple Filter")->hideFromIndex(),
            Boolean::make("Advanced Filter")->hideFromIndex(),
            Boolean::make("List View")->hideFromIndex(),
            Boolean::make("No Front")->hideFromIndex(),
            Boolean::make("Only Subnational")->hideFromIndex(),
            HasMany::make('Options', 'options', FieldOption::class),
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

    public function filters(Request $request)
    {
        return [
            new FieldType,
            new NationalityType,
        ];
    }

    public static function relatableQuery(NovaRequest $request, $query)
    {
        return $query->whereIn('type', [FieldTypeEnum::SUB_HEADING, FieldTypeEnum::HEADING]);
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            (new SurveyFieldsExport())
                ->withFilename('Survey-fields-' . time() . '.xlsx')
                ->onlyOnIndex(),
        ];
    }

}

