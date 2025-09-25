<?php

namespace App\Mail;

use App\Survey;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SurveyReady extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var Survey
     */
    protected $survey;

    /**
     * Create a new message instance.
     *
     * @param Survey $survey
     */
    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Subject ready!')
            ->text('emails.survey_ready');
    }
}
