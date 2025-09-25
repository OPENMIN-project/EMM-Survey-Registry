<?php

namespace App\Listeners;

use App\Events\SurveyStatusChangedToReady;
use App\Mail\SurveyReady;
use App\User;
use App\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifyAdminsSurveyReady implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param SurveyStatusChangedToReady $event
     * @return void
     */
    public function handle(SurveyStatusChangedToReady $event)
    {
        /** @var Collection $admins */
        $admins = User::whereRole(UserRole::ADMIN)->get();
        $emails = $admins->pluck('email');

        Mail::to($emails->first())
            ->bcc($emails->toArray())
            ->send(new SurveyReady($event->survey));
    }
}
