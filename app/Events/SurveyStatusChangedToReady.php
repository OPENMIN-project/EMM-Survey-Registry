<?php

namespace App\Events;

use App\Survey;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SurveyStatusChangedToReady
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var Survey
     */
    public $survey;

    /**
     * Create a new event instance.
     *
     * @param Survey $survey
     */
    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
    }
}
