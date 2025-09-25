<?php

namespace App;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Actionable;
use ScoutElastic\Searchable;

/**
 * @property Collection|null users
 * @property Carbon updated_at
 * @property string status
 * @property mixed id
 * @property mixed country
 * @property array|mixed answers
 * @property mixed update_count
 * @property string country_name
 */
class Survey extends Model
{
    use Actionable, Searchable;

    protected $fillable = [
        'country',
        'status',
        'answers',
        'update_count'
    ];

    const SUBNATIONAL_TYPE = 2;

    protected $indexConfigurator = SurveyIndexConfigurator::class;
    protected $searchRules = [
        SurveySearch::class
    ];

    protected $casts = [
        'answers' => 'object',
    ];

    // Backwards compatibility with v lower than 7
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function toSearchableArray()
    {
        return $this->toArray();
    }

    public function getSurveyTypeAttribute()
    {
        return $this->answers->f_1_5;
    }

    public function isSubnational()
    {
        return $this->survey_type == self::SUBNATIONAL_TYPE;
    }

    public function getMappingAttribute()
    {
        return json_decode(file_get_contents(storage_path('mappings_output.json')), true);
    }
}
