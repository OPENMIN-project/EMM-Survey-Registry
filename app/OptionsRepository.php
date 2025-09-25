<?php


namespace App;


use App\Enum\FieldType;
use Exception;

class OptionsRepository
{
    /**
     * @param $field
     * @return mixed
     * @throws Exception
     */
    public static function for($field)
    {
        return data_get(self::options(), $field, null);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function options()
    {
        return cache()->remember('options-list', 5 * 60, function () {
            $options = SurveyField::whereIn('type', [FieldType::CHOICE, FieldType::ARRAY])
                ->with('options')->get()
                ->pluck('options', 'field_code')
                ->map->pluck('label', 'value');
            $options['f_11_1'] = User::select("id", "orcid_id", "name")->get()
                ->mapWithKeys(function ($user) {
                    $orcidImg = !$user->orcid_id ? '' :
                    "<a target='_blank' href=\"https://orcid.org/{$user->orcid_id}\"><img class=\"inline ml-1\" src=\"https://i0.wp.com/info.orcid.org/wp-content/uploads/2021/12/orcid_16x16.gif?resize=16%2C16&ssl=1\" alt='' /></a>";
                    return [$user->id => "{$user->name} {$orcidImg}"];
                });
            return $options;
        });
    }

    /**
     * @param string $field
     * @param        $value
     * @return string|null
     * @throws Exception
     */
    public static function field(string $field, $value): ?string
    {
        switch (true) {
            case is_null($value):
                return null;
            case is_array($value):
                return collect($value)->map(function ($item) use ($field) {
                    return self::valueFromOptions($field, $item);
                })->implode(', ');
            default:
                return self::valueFromOptions($field, $value);
        }
    }

    /**
     * @param string $field
     * @param        $value
     * @return array|string|null
     * @throws Exception
     */
    public static function field_raw(string $field, $value)
    {
        switch (true) {
            case is_null($value):
                return null;
            case is_array($value):
                return collect($value)->map(function ($item) use ($field) {
                    return self::valueFromOptions($field, $item);
                })->all();
            default:
                return self::valueFromOptions($field, $value);
        }
    }

    /**
     * @param string $field Field code
     * @param string $value Survey field value
     * @return string|null
     * @throws Exception
     */
    public static function valueFromOptions(string $field, string $value): ?string
    {
        return data_get(self::options(), "{$field}.{$value}", $value);
    }
}
