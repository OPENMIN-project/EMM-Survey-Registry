<?php

namespace App\Imports;

use App\Enum\FieldType;
use App\SurveyField;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SurveyFieldsImport implements ToCollection
{
    const FIELD_NUMBER           = 0;
    const FIELD_LABEL            = 1;
    const FIELD_TYPE             = 2;
    const FIELD_REQUIRED         = 3;
    const FIELD_ADVANCED_FILTER  = 4;
    const FIELD_SIMPLE_FILTER    = 5;
    const FIELD_SORTING          = 6;
    const FIELD_DISPLAY          = 7;
    const FIELD_NO_FRONT         = 8;
    const FIELD_ONLY_SUBNATIONAL = 9;
    const FIELD_HELP             = 10;
    const FIELD_DDI              = 11;
    const FIELD_ORIGINAL_NAME    = 12;
    const FIELD_OPTIONS_START    = 13;

    const BOOLEAN_TRUE = [true, '=TRUE()'];

    protected $field_groups;
    protected $order = 0;

    public function __construct()
    {
        $this->field_groups = collect();
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection): void
    {
        $collection->shift();
        $collection
            ->filter(function (Collection $row) {
                return $row->first() !== null;
            })
            ->each(function (Collection $row) {
                $row = $row->toArray();
                $this->createField($row);
            });
    }

    /**
     * @param array $row
     */
    private function createField(array $row): void
    {
        $field = SurveyField::create([
            'parent_field_id'  => $this->determineParentId($row),
            'order'            => $this->order += 10,
            'code'             => $code = $this->sanitize($row[self::FIELD_NUMBER]),
            'name'             => $row[self::FIELD_LABEL],
            'type'             => $type = strtolower($row[self::FIELD_TYPE]),
            'field_code'       => SurveyField::slugifyCode($code, $type),
            'required'         => $this->isTrue($row[self::FIELD_REQUIRED]),
            "simple_filter"    => $this->hasSimpleFilter($row),
            "advanced_filter"  => $this->hasAdvancedFilter($row),
            'sortable'         => $this->isTrue($row[self::FIELD_SORTING]),
            'list_view'        => $this->isTrue($row[self::FIELD_DISPLAY]),
            'no_front'         => $this->isTrue($row[self::FIELD_NO_FRONT]),
            'only_subnational' => $this->isTrue($row[self::FIELD_ONLY_SUBNATIONAL]),
            'hint'             => $row[self::FIELD_HELP],
        ]);


        if ($this->isHeading($field->type)) {
            $this->field_groups->prepend($field);
        }

        if (isset($row[13])) {
            $i = self::FIELD_OPTIONS_START;
            $order = 0;
            while (array_key_exists($i, $row) && $row[$i] !== null) {
                $parts = explode(' ', $row[$i], 2);
                if (count($parts) < 2) {
                    continue;
                }
                $field->options()->create([
                    'value' => $this->sanitize($parts[0], ($field->field_code === 'f_1_0') ? ' .()' : ' .'),
                    'label' => $this->sanitize($parts[1], ($field->field_code === 'f_1_0') ? ' .()' : ' .'),
                    'order' => $order += 10,
                ]);
                $i++;
            }
        }
    }

    /**
     * @param array $row
     * @return int|null
     */
    private function determineParentId(array $row): ?int
    {
        if ($this->isHeading($row[self::FIELD_TYPE])) {
            return null;
        }

        return optional($this->field_groups->first())->id;
    }

    /**
     * @param $type
     * @return bool
     */
    private function isHeading($type): bool
    {
        return $type === FieldType::HEADING;
    }

    /**
     * @param string $value
     * @param string $charlist
     * @return string
     */
    private function sanitize(string $value, string $charlist = ' .'): string
    {
        return trim($value, $charlist);
    }

    /**
     * @param array $row
     * @return bool
     */
    private function hasSimpleFilter(array $row): bool
    {
        return $this->isTrue($row[self::FIELD_SIMPLE_FILTER]);
    }

    /**
     * @param $value
     * @return bool
     */
    private function isTrue($value): bool
    {
        if (in_array($value, static::BOOLEAN_TRUE, true)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $row
     * @return bool
     */
    private function hasAdvancedFilter(array $row): bool
    {
        return $this->isTrue($row[self::FIELD_ADVANCED_FILTER]);
    }

}
