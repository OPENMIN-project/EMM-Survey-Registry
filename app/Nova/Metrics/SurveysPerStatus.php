<?php

namespace App\Nova\Metrics;

use App\Enum\SurveyStatus;
use App\Survey;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;

class SurveysPerStatus extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        return $this->count($request, Survey::class, 'status')
            ->label(function ($value) {
                switch ($value) {
                    case SurveyStatus::PUBLISHED:
                        return SurveyStatus::LABEL_PUBLISHED;
                    case SurveyStatus::DRAFT:
                        return SurveyStatus::LABEL_DRAFT;
                    case SurveyStatus::READY:
                        return SurveyStatus::LABEL_READY;
                    default:
                        return 'N/A';
                }
            })
            ->colors([
                SurveyStatus::LABEL_DRAFT     => SurveyStatus::COLOR_DRAFT,
                SurveyStatus::LABEL_READY     => SurveyStatus::COLOR_READY,
                SurveyStatus::LABEL_PUBLISHED => SurveyStatus::COLOR_PUBLISHED,
            ]);
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
//         return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'surveys-per-status';
    }
}
