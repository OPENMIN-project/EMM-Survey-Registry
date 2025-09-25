<?php

namespace App\Nova\Actions;

use App\Enum\SurveyStatus;
use App\Survey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class CloneSurvey extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param \Laravel\Nova\Fields\ActionFields $fields
     * @param \Illuminate\Support\Collection $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        /** @var Survey $survey */
        $survey = $models->first();
        /** @var Survey $clone */
        $clone = $survey->replicate();
        $clone->status = SurveyStatus::DRAFT;
        $clone->save();

        $this->markAsFinished($clone);

        return Action::push("/resources/surveys/{$clone->id}/edit");
    }

    public function authorizedToRun(Request $request, $model)
    {
        return true;
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}
