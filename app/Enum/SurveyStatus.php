<?php

namespace App\Enum;

class SurveyStatus
{
    const DRAFT     = 'draft';
    const READY     = 'ready';
    const PUBLISHED = 'published';

    const LABEL_DRAFT     = 'Draft';
    const LABEL_READY     = 'Ready';
    const LABEL_PUBLISHED = 'Published';

    const COLOR_DRAFT     = '#DADEE3';
    const COLOR_READY     = '#E3A64C';
    const COLOR_PUBLISHED = '#82E389';

    public static function options()
    {
        return [
            SurveyStatus::DRAFT,
            SurveyStatus::READY,
            SurveyStatus::PUBLISHED,
        ];
    }

    public static function optionsWithLabels()
    {
        return [
            SurveyStatus::DRAFT     => SurveyStatus::LABEL_DRAFT,
            SurveyStatus::READY     => SurveyStatus::LABEL_READY,
            SurveyStatus::PUBLISHED => SurveyStatus::LABEL_PUBLISHED,
        ];
    }

    public static function optionsForEditors()
    {
        return [
            SurveyStatus::DRAFT => SurveyStatus::LABEL_DRAFT,
            SurveyStatus::READY => SurveyStatus::LABEL_READY,
        ];
    }

    public static function colors()
    {
        return [
            SurveyStatus::DRAFT     => SurveyStatus::COLOR_DRAFT,
            SurveyStatus::READY     => SurveyStatus::COLOR_READY,
            SurveyStatus::PUBLISHED => SurveyStatus::COLOR_PUBLISHED,
        ];
    }
}
