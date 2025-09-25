<?php

namespace App\Policies;

use App\User;
use App\Survey;
use App\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class SurveyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the survey.
     *
     * @param \App\User $user
     * @param \App\Survey $survey
     * @return mixed
     */
    public function view(User $user, Survey $survey)
    {
        return true;
    }

    /**
     * Determine whether the user can view the survey.
     *
     * @param \App\User $user
     * @param \App\Survey $survey
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create surveys.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the survey.
     *
     * @param \App\User $user
     * @param \App\Survey $survey
     * @return mixed
     */
    public function update(User $user, Survey $survey)
    {
        return $user->hasRole([UserRole::MASTER, UserRole::ADMIN])
            || ($user->hasRole(UserRole::EDITOR) && $survey->users->contains($user));
    }

    /**
     * Determine whether the user can delete the survey.
     *
     * @param \App\User $user
     * @param \App\Survey $survey
     * @return mixed
     */
    public function delete(User $user, Survey $survey)
    {
        return $user->hasRole([UserRole::MASTER, UserRole::ADMIN]);
    }

    /**
     * Determine whether the user can restore the survey.
     *
     * @param \App\User $user
     * @param \App\Survey $survey
     * @return mixed
     */
    public function restore(User $user, Survey $survey)
    {
        return $user->hasRole([UserRole::MASTER, UserRole::ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the survey.
     *
     * @param \App\User $user
     * @param \App\Survey $survey
     * @return mixed
     */
    public function forceDelete(User $user, Survey $survey)
    {
        return $user->hasRole([UserRole::MASTER, UserRole::ADMIN]);
    }

    /**
     * @param User $user
     * @return bool
     * @see \App\Nova\Survey fields
     */
    public function manageOwnersField(User $user)
    {
        return $user->hasRole([UserRole::MASTER, UserRole::ADMIN]);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function changeStatusAdmin(User $user)
    {
        return $user->hasRole([UserRole::MASTER, UserRole::ADMIN]);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function changeStatusEditor(User $user)
    {
        return $user->hasRole([UserRole::EDITOR]);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function importSurveys(User $user)
    {
        return $user->hasRole([UserRole::MASTER, UserRole::ADMIN]);
    }
}
