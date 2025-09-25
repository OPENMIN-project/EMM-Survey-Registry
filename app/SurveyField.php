<?php

namespace App;

use App\Enum\FieldType;
use App\SurveyField as SurveyFieldModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @method static headings()
 * @method static Builder usedAsFilter()
 * @property SurveyField|null $parent
 * @property Collection       $options
 * @property string           field_code
 * @property string           code
 * @property string           type
 * @property string           label
 */
class SurveyField extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function (SurveyField $field) {
            if (!$field->field_code) {
                $field->field_code = SurveyFieldModel::slugifyCode($field->code, $field->type);
            }
        });
    }

    public static function byCode($code): ?self
    {
        return self::where('field_code', $code)->first();
    }

    public function options()
    {
        return $this->hasMany(FieldOption::class)->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(SurveyField::class, 'parent_field_id');
    }

    public function fields()
    {
        return $this->hasMany(SurveyField::class, 'parent_field_id');
    }

    public function isHeading()
    {
        return $this->type === FieldType::HEADING;
    }

    public function isSubHeading()
    {
        return $this->type === FieldType::SUB_HEADING;
    }

    /**
     * @return bool
     */
    public function shouldUseRaw()
    {
        return !in_array($this->type, [FieldType::CHOICE, FieldType::DATE]);
    }

    public function displayFields()
    {
        return $this->fields()->where('no_front', false);
    }

    public function getLabelAttribute()
    {
        return $this->code . ' ' . $this->name;
    }

    /**
     * @param Builder  $query
     * @param int|null $type
     * @throws \Exception
     * @return mixed
     */
    public function scopeUsedAsFilter(Builder $query, int $type = null)
    {
        if ($type && !in_array($type, ['simple', 'advanced'])) {
            throw new \Exception("Type is not valid. Valid options: simple, advanced");
        }

        if ($type) {
            return $query->whereTrue("{$type}_filter");
        }

        return $query->whereNotIn('type', [FieldType::HEADING, FieldType::SUB_HEADING])
            ->where(function (Builder $q) {
                return $q->where('simple_filter', true)
                    ->orWhere('advanced_filter', true);
            });
    }

    public function scopeHeadings($query)
    {
        return $query->whereNull('parent_field_id');
    }

    public function optionsAsArray(bool $flip = false): array
    {
        $value = $this->options->pluck('label', 'value');
        if($this->field_code === 'f_11_1') {
            return User::all()->pluck('name', 'id')->toArray();
        }
        $valueArray = $value->toArray();
        return $flip ? array_flip($valueArray) : $valueArray;
    }

    public function labelForOptionValue($value, bool $flip = false)
    {
        return $this->optionsAsArray($flip)[$value] ?? null;
    }

    public function valueForOptionLabel($label)
    {
        return $this->labelForOptionValue($label, true);
    }

    /**
     * @param        $code
     * @param string $type
     * @return string
     */
    public static function slugifyCode($code, $type = 'field'): string
    {
        switch ($type) {
            case FieldType::SUB_HEADING:
                $pre = 'sh_';
                break;
            case FieldType::HEADING:
                $pre = 'h_';
                break;
            default:
                $pre = 'f_';
                break;
        }

        return $pre . str_replace('.', '_', trim($code, ' .'));
    }

}
