<?php

namespace App\Policies;

use App\User;
use App\SurveyField;
use App\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class SurveyFieldPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the survey field.
     *
     * @param \App\User $user
     * @param \App\SurveyField $surveyField
     * @return mixed
     */
    public function view(User $user, SurveyField $surveyField)
    {
        return $user->hasRole([UserRole::MASTER, UserRole::ADMIN]);
    }

    /**
     * Determine whether the user can view any survey field.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasRole([UserRole::MASTER, UserRole::ADMIN]);
    }

    /**
     * Determine whether the user can create survey fields.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole(UserRole::MASTER);
    }

    /**
     * Determine whether the user can update the survey field.
     *
     * @param \App\User $user
     * @param \App\SurveyField $surveyField
     * @return mixed
     */
    public function update(User $user, SurveyField $surveyField)
    {
        return $user->hasRole(UserRole::MASTER);
    }

    /**
     * Determine whether the user can delete the survey field.
     *
     * @param \App\User $user
     * @param \App\SurveyField $surveyField
     * @return mixed
     */
    public function delete(User $user, SurveyField $surveyField)
    {
        return $user->hasRole(UserRole::MASTER);
    }

    /**
     * Determine whether the user can restore the survey field.
     *
     * @param \App\User $user
     * @param \App\SurveyField $surveyField
     * @return mixed
     */
    public function restore(User $user, SurveyField $surveyField)
    {
        return $user->hasRole(UserRole::MASTER);
    }

    /**
     * Determine whether the user can permanently delete the survey field.
     *
     * @param \App\User $user
     * @param \App\SurveyField $surveyField
     * @return mixed
     */
    public function forceDelete(User $user, SurveyField $surveyField)
    {
        return $user->hasRole(UserRole::MASTER);
    }
}
