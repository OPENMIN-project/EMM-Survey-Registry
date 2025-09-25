<?php

namespace App\Policies;

use App\User;
use App\FieldOption;
use App\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class FieldOptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the field option.
     *
     * @param \App\User $user
     * @param \App\FieldOption $fieldOption
     * @return mixed
     */
    public function view(User $user, FieldOption $fieldOption)
    {
        return $user->hasRole(UserRole::MASTER);
    }

    /**
     * Determine whether the user can view any field option.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasRole(UserRole::MASTER);
    }

    /**
     * Determine whether the user can create field options.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole(UserRole::MASTER);
    }

    /**
     * Determine whether the user can update the field option.
     *
     * @param \App\User $user
     * @param \App\FieldOption $fieldOption
     * @return mixed
     */
    public function update(User $user, FieldOption $fieldOption)
    {
        return $user->hasRole(UserRole::MASTER);
    }

    /**
     * Determine whether the user can delete the field option.
     *
     * @param \App\User $user
     * @param \App\FieldOption $fieldOption
     * @return mixed
     */
    public function delete(User $user, FieldOption $fieldOption)
    {
        return $user->hasRole(UserRole::MASTER);
    }

    /**
     * Determine whether the user can restore the field option.
     *
     * @param \App\User $user
     * @param \App\FieldOption $fieldOption
     * @return mixed
     */
    public function restore(User $user, FieldOption $fieldOption)
    {
        return $user->hasRole(UserRole::MASTER);
    }

    /**
     * Determine whether the user can permanently delete the field option.
     *
     * @param \App\User $user
     * @param \App\FieldOption $fieldOption
     * @return mixed
     */
    public function forceDelete(User $user, FieldOption $fieldOption)
    {
        return $user->hasRole(UserRole::MASTER);
    }
}
